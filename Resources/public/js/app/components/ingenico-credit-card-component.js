define(function(require) {
    'use strict';

    window.forge = require('node-forge');

    const _ = require('underscore');
    const __ = require('orotranslation/js/translator');
    const $ = require('jquery');
    const BaseComponent = require('oroui/js/app/components/base/component');
    const ConnectSdk = require('connect-sdk-client-js');
    const mediator = require('oroui/js/mediator');
    const routing = require('routing');
    const paymentProductListTemplate = require('tpl-loader!ingenico/templates/payment-products-list.html');

    const IngenicoCreditCardComponent = BaseComponent.extend({
        options: {
            paymentMethod: null,
            paymentDetails: {},
            createSessionRoute: 'ingenico_create_session',
            saveForLaterUseEnabled: false,
            savedCreditCardList: [],
            cardsGroupName: 'cards',
            tokenRequiredFields: ['cvv'],
            selectors: {
                paymentProductItem: 'a.payment-product__item',
                paymentProductList: '.ingenico__payment-product-list',
                paymentGeneralFields: '.ingenico__payment-general-fields',
                paymentProductFields: '.ingenico__payment-product-fields'
            }
        },

        listen: {
            'checkout:payment:method:changed mediator': 'onPaymentMethodChanged',
            'checkout:payment:before-transit mediator': 'beforeTransit',
            'checkout:payment:before-hide-filled-form mediator': 'beforeHideFilledForm',
            'checkout:payment:before-restore-filled-form mediator': 'beforeRestoreFilledForm',
            'checkout:payment:remove-filled-form mediator': 'removeFilledForm',
            'checkout-content:initialized mediator': 'refreshPaymentMethod'
        },

        session: null,
        paymentProductItems: [],
        currentPaymentProduct: null,
        paymentProductListTemplate: paymentProductListTemplate,
        saveForLaterUse: false,

        /**
         * @property {jQuery}
         */
        $el: null,

        /**
         * @property {jQuery}
         */
        $paymentProductList: null,

        /**
         * @property {jQuery}
         */
        $paymentProductFieldsElement: null,

        /**
         * @property {jQuery}
         */
        $paymentGeneralFieldsElement: null,

        /**
         * @property {Boolean}
         */
        disposable: true,

        /**
         * @property {Object}
         */
        _requiredFields: {},

        /**
         * @inheritDoc
         */
        constructor: function IngenicoCreditCardComponent(options) {
            IngenicoCreditCardComponent.__super__.constructor.call(this, options);
        },

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = _.extend({}, this.options, options);

            this.$el = this.options._sourceElement;
            this.$paymentProductList = $(this.options.selectors.paymentProductList);
            this.$paymentGeneralFieldsElement = $(this.options.selectors.paymentGeneralFields);
            this.$paymentProductFieldsElement = $(this.options.selectors.paymentProductFields);

            this.$el
                .on(
                    'click.' + this.cid,
                    this.options.selectors.paymentProductItem,
                    this.onPaymentProductClick.bind(this)
                )
                .on('change.' + this.cid, this.getSaveForLaterSelector(), this.onSaveForLaterChange.bind(this))
                .on('change.' + this.cid, this.getTokenFieldSelector(), this.onTokenChange.bind(this));
        },

        refreshPaymentMethod: function() {
            mediator.trigger('checkout:payment:method:changed', {paymentMethod: this.options.paymentMethod});
        },

        onPaymentMethodChanged: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                // reset save for later use checkbox to default value
                // because we support this feature for credit card payment group only
                this.saveForLaterUse = false;

                this.getSession()
                    .then(this.getPaymentProducts.bind(this))
                    .then(this.renderPaymentProductsList.bind(this));
            }
        },

        getSession: function() {
            const deffer = $.Deferred();

            if (this.session) {
                deffer.resolve();
            } else {
                mediator.execute('showLoading');
                $.getJSON(
                    routing.generate(
                        this.options.createSessionRoute,
                        {paymentMethod: this.options.paymentMethod}
                    ),
                    data => {
                        if (data.success) {
                            this.session = new ConnectSdk(data.sessionInfo);
                            mediator.execute('hideLoading');
                            deffer.resolve();
                        } else {
                            this.$el.html(__('ingenico.payment_method_is_not_available'));
                            mediator.execute('hideLoading');
                            deffer.reject();
                        }
                    }
                );
            }

            return deffer.promise();
        },

        getPaymentProducts: function() {
            const deffer = $.Deferred();

            if (this.paymentProductItems.length) {
                deffer.resolve();
            } else {
                mediator.execute('showLoading');
                this.session.getBasicPaymentItems(this.options.paymentDetails).then(
                    _basicPaymentItems => {
                        this.paymentProductItems = _basicPaymentItems.basicPaymentItems;
                        mediator.execute('hideLoading');
                        deffer.resolve();
                    },
                    () => {
                        mediator.execute('hideLoading');
                        this.$el.html(__('ingenico.api.error.no_available_payment_products'));
                        deffer.reject();
                    }
                );
            }

            return deffer.promise();
        },

        getPaymentProductDetails: function(paymentProductId) {
            const deffer = $.Deferred();

            if (this.isPaymentProductChanged(paymentProductId)) {
                this.session
                    .getPaymentProduct(paymentProductId, this.options.paymentDetails)
                    .then(
                        paymentProduct => {
                            this.currentPaymentProduct = paymentProduct;
                            mediator.execute('hideLoading');
                            deffer.resolve();
                        },
                        () => {
                            mediator.execute('hideLoading');
                            mediator.execute(
                                'showFlashMessage',
                                'error',
                                __('ingenico.api.error.get_payment_product_details')
                            );
                            deffer.reject();
                        }
                    );
            } else {
                deffer.resolve();
            }

            return deffer.promise();
        },

        renderPaymentProductsList: function() {
            const items = _.map(this.paymentProductItems, function(item) {
                return {
                    id: item.id,
                    label: item.displayHints.label,
                    logo: item.displayHints.logo
                };
            });

            return this.$paymentProductList.html(this.paymentProductListTemplate({productPayments: items}));
        },

        onPaymentProductClick: function(event) {
            event.preventDefault();
            const paymentProductId = $(event.currentTarget).data('product-id');

            this.renderPaymentProductFields(paymentProductId);
        },

        renderPaymentProductFields: function(paymentProductId) {
            this.getSession()
                .then(() => this.getPaymentProductDetails(paymentProductId))
                .then(() => {
                    this._renderCurrentProductPaymentFields();

                    const fields = [];
                    if (this.isTokenizationApplicable()) {
                        const creditCardList = this.getSavedCardList();
                        fields.push(_.macros('ingenico::token')({
                            paymentMethod: this.options.paymentMethod,
                            field: {
                                id: 'token',
                                displayHints: {
                                    label: __('ingenico.token.label'),
                                    placeholderLabel: __('ingenico.token.placeholder')
                                },
                                dataRestrictions: {
                                    isRequired: false
                                }
                            },
                            values: creditCardList
                        }));
                    }

                    this.$paymentGeneralFieldsElement.html(fields.join(''));
                    this.$paymentGeneralFieldsElement.find('select').inputWidget('create', 'select2');
                });
        },

        _renderCurrentProductPaymentFields: function() {
            const fields = [];
            const token = this.getToken();
            _.each(this.currentPaymentProduct.paymentProductFields, field => {
                if (token && _.indexOf(this.options.tokenRequiredFields, field.id) === -1) {
                    return;
                }
                const rendererFieldName = 'ingenico::' + field.id;
                fields.push(_.macros(rendererFieldName)({
                    paymentMethod: this.options.paymentMethod,
                    field: field
                }));
            });

            if (this.isTokenizationApplicable() && !token) {
                fields.push(_.macros('ingenico::saveForLaterUse')({
                    paymentMethod: this.options.paymentMethod,
                    field: {
                        id: 'saveForLaterUse',
                        displayHints: {
                            label: __('ingenico.saveForLaterUse.label')
                        },
                        dataRestrictions: {
                            isRequired: false
                        }
                    }
                }));
            }

            this.$paymentProductFieldsElement.html(fields.join(''));
            this.$paymentProductFieldsElement.find('select').inputWidget('create', 'select2');
        },

        getSavedCardList: function() {
            if (!this.currentPaymentProduct) {
                return [];
            }

            if (!_.has(this.options.savedCreditCardList, this.currentPaymentProduct.id)) {
                return [];
            }

            return this.options.savedCreditCardList[this.currentPaymentProduct.id];
        },

        /**
         * @param {Object} e
         */
        onSaveForLaterChange: function(e) {
            const $el = $(e.target);
            this.saveForLaterUse = $el.prop('checked');
        },

        getSaveForLaterSelector: function() {
            return this.buildFieldIdentifier('saveForLaterUse', 'field');
        },

        getTokenFieldSelector: function() {
            return this.buildFieldIdentifier('token', 'field');
        },

        isPaymentProductChanged: function(paymentProductId) {
            if (!this.currentPaymentProduct) {
                return true;
            }

            return this.currentPaymentProduct.id !== paymentProductId;
        },

        isTokenizationApplicable: function() {
            if (!this.currentPaymentProduct) {
                return false;
            }

            return this.currentPaymentProduct.paymentProductGroup === this.options.cardsGroupName &&
                this.options.saveForLaterUseEnabled;
        },

        /**
         * @param {Object} eventData
         */
        beforeTransit: function(eventData) {
            if (eventData.data.paymentMethod === this.options.paymentMethod) {
                eventData.stopped = true;

                if (this.validate()) {
                    mediator.execute('showLoading');
                    this.storeEcryptedCutomerDetailes().then(function() {
                        eventData.resume();
                    }).catch(function() {
                        mediator.execute('hideLoading');
                    });
                }
            }
        },

        buildFieldIdentifier: function(id, key) {
            return '.' + id + '-' + this.options.paymentMethod + '-' + key;
        },

        collectFormData: function() {
            const fields = [];
            _.each(this.currentPaymentProduct.paymentProductFields, field => {
                const fieldName = this.buildFieldIdentifier(field.id, 'field');
                if ($(fieldName).length) {
                    const value = $(fieldName).val();
                    fields.push({
                        field: field.id,
                        value: value
                    });
                }
            });

            return fields;
        },

        onTokenChange: function(event) {
            if (event.target.value) {
                this._saveRequiredFieldsList();
            } else {
                this._restoreRequiredFieldsList();
            }

            this._renderCurrentProductPaymentFields();
        },

        _saveRequiredFieldsList: function() {
            if (!this.currentPaymentProduct) {
                return;
            }

            const requiredFields = {};
            _.each(this.currentPaymentProduct.paymentProductFields, field => {
                if (field.dataRestrictions.isRequired &&
                    _.indexOf(this.options.tokenRequiredFields, field.id) === -1) {
                    requiredFields[field.id] = field.dataRestrictions.validationRules;
                    field.dataRestrictions.isRequired = false;
                    field.dataRestrictions.validationRules = [];
                }
            });

            this._requiredFields = requiredFields;
        },

        _restoreRequiredFieldsList: function() {
            if (!this.currentPaymentProduct) {
                return;
            }

            _.each(this.currentPaymentProduct.paymentProductFields, field => {
                if (_.has(this._requiredFields, field.id)) {
                    field.dataRestrictions.isRequired = true;
                    field.dataRestrictions.validationRules = this._requiredFields[field.id];
                }
            });

            this._requiredFields = {};
        },

        getToken: function() {
            if (this.isTokenizationApplicable()) {
                const tokenFieldIdentifier = this.buildFieldIdentifier('token', 'field');
                return $(tokenFieldIdentifier).val();
            }

            return null;
        },

        validate: function() {
            if (!this.currentPaymentProduct) {
                mediator.execute('showFlashMessage', 'error', __('ingenico.no_choosen_payment_product'));

                return false;
            }

            const fields = this.collectFormData();
            const paymentRequest = this.session.getPaymentRequest();
            paymentRequest.setPaymentProduct(this.currentPaymentProduct);

            // field payment request with data
            _.each(fields, function(item) {
                paymentRequest.setValue(item.field, item.value);
            });

            if (!paymentRequest.isValid()) {
                // as we need partial validation we do not get validation by all payment product product fields
                // just only fields in payment request should be validated
                _.each(paymentRequest.getValues(), (value, name) => {
                    const field = paymentRequest.getPaymentProduct().paymentProductFieldById[name];
                    if (field) {
                        const fieldName = this.buildFieldIdentifier(name, 'error');
                        if ($(fieldName).length) {
                            if (!field.isValid(value)) {
                                $(fieldName).removeClass('hidden');
                            } else {
                                $(fieldName).addClass('hidden');
                            }
                        }
                    }
                });

                return false;
            }

            return true;
        },


        /**
         * Crypts selected payment product's form values and storing it to DOM storage. INGA-29 basic implementation
         */
        storeEcryptedCutomerDetailes: function() {
            const deffer = $.Deferred();

            const encryptor = this.session.getEncryptor();
            const paymentRequest = this.session.getPaymentRequest();
            encryptor.encrypt(paymentRequest).then(
                encryptedString => {
                    let data = {
                        ingenicoPaymentProduct: paymentRequest.getPaymentProduct().paymentProductGroup,
                        ingenicoCustomerEncDetails: encryptedString
                    };

                    if (this.isTokenizationApplicable()) {
                        const token = this.getToken();
                        data = _.extend(data, {
                            ingenicoSaveForLaterUse: this.saveForLaterUse,
                            ingenicoToken: token
                        });
                    }

                    this.addPaymentAdditionalData(data);
                    deffer.resolve();
                },
                () => {
                    deffer.reject();
                    mediator.execute('showFlashMessage', 'error', __('ingenico.crypt_error'));
                }
            );

            return deffer.promise();
        },

        addPaymentAdditionalData: function(updateData) {
            let additionalData;
            const holder = {};

            mediator.trigger('checkout:payment:additional-data:get', holder);
            try {
                additionalData = JSON.parse(holder.additionalData);
            } catch (e) {
                additionalData = {};
            }

            for (const key in updateData) {
                additionalData[key] = updateData[key];
            }

            mediator.trigger('checkout:payment:additional-data:set', JSON.stringify(additionalData));
        },

        beforeHideFilledForm: function() {
            this.disposable = false;
        },

        beforeRestoreFilledForm: function() {
            if (this.disposable) {
                this.dispose();
            }
        },

        removeFilledForm: function() {
            // Remove hidden form js component
            if (!this.disposable) {
                this.disposable = true;
                this.dispose();
            }
        },

        dispose: function() {
            if (this.disposed || !this.disposable) {
                return;
            }

            this.$el
                .off('click.' + this.cid, this.options.selectors.paymentProductItem)
                .off('change.' + this.cid, this.getSaveForLaterSelector())
                .off('change.' + this.cid, this.getTokenFieldSelector());

            IngenicoCreditCardComponent.__super__.dispose.call(this);
        }
    });

    return IngenicoCreditCardComponent;
});
