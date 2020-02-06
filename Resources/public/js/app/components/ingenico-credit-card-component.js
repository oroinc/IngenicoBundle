define(function(require) {
    'use strict';

    window.forge = require('node-forge');

    const _ = require('underscore');
    const __ = require('orotranslation/js/translator');
    const $ = require('jquery');
    const BaseComponent = require('oroui/js/app/components/base/component');
    const connectsdk = require('connect-sdk-client-js');
    const mediator = require('oroui/js/mediator');
    const routing = require('routing');
    const paymentProductListTemplate = require('tpl-loader!ingenico/templates/payment-products-list.html');

    const IngenicoCreditCardComponent = BaseComponent.extend({
        options: {
            paymentMethod: null,
            paymentDetails: {},
            createSessionRoute: 'ingenico_create_session',
            selectors: {
                body: '.payment-body',
                state: {
                    paymentProduct: '.current-payment-product',
                    paymentProductFields: '.current-payment-product-fields-values'
                }
            }
        },

        listen: {
            'checkout:payment:method:changed mediator': 'onPaymentMethodChanged',
            'checkout:payment:before-transit mediator': 'beforeTransit',
            'checkout:payment:before-hide-filled-form mediator': 'beforeHideFilledForm',
            'checkout:payment:before-restore-filled-form mediator': 'beforeRestoreFilledForm',
            'checkout:payment:remove-filled-form mediator': 'removeFilledForm'
        },

        session: null,
        paymentProductItems: [],
        currentPaymentProduct: null,
        paymentProductListTemplate: paymentProductListTemplate,

        /**
         * @property {jQuery}
         */
        $el: null,

        /**
         * @property {Boolean}
         */
        disposable: true,

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

            this.$el.on('click.' + this.cid, 'a.payment-product__item', this.renderPaymentProductFields.bind(this));
            this.$el.on('change.' + this.cid, 'input, select, checkbox, radio', () => this.saveFieldsState());

            this._initializeIngenicoPayment();
        },

        getPaymentProductState: function() {
            return this.$el.find(this.options.selectors.state.paymentProduct).val();
        },

        savePaymentProductState: function(paymentProductId) {
            this.$el.find(this.options.selectors.state.paymentProduct).val(paymentProductId);
        },

        getFieldsState: function() {
            const jsonString = this.$el.find(this.options.selectors.state.paymentProductFields).val();
            try {
                return JSON.parse(jsonString);
            } catch (e) {
                return [];
            }
        },

        saveFieldsState: function() {
            // TODO: filter function should be removed when task of ACH or SEPA merged.
            // They containing fix of validation related to # symbol in request
            const fields = _.filter(this.collectFormData(), item => item.value !== '#');
            this.$el.find(this.options.selectors.state.paymentProductFields).val(JSON.stringify(fields));
        },

        clearFieldsState: function() {
            this.$el.find(this.options.selectors.state.paymentProductFields).val('');
        },

        _initializeIngenicoPayment: function() {
            this._deferredInit();

            const productState = parseInt(this.getPaymentProductState());
            this.getSession()
                .then(() => this.getPaymentProducts())
                .then(() => {
                    if (productState) {
                        this.getPaymentProductDetails(productState)
                            .then(() => this.renderCurrentPaymentProductFields());
                    } else {
                        this.renderPaymentProductsList();
                    }

                    this._resolveDeferredInit();
                });
        },

        onPaymentMethodChanged: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                this._initializeIngenicoPayment();
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
                            this.session = new connectsdk(data.sessionInfo);
                            mediator.execute('hideLoading');
                            deffer.resolve();
                        } else {
                            this.$el
                                .find(this.options.selectors.body)
                                .html(__('ingenico.payment_method_is_not_available'));
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
                        this.$el
                            .find(this.options.selectors.body)
                            .html(__('ingenico.api.error.no_available_payment_products'));
                        mediator.execute('hideLoading');
                        deffer.reject();
                    }
                );
            }

            return deffer.promise();
        },

        getPaymentProductDetails: function(paymentProductId) {
            const deffer = $.Deferred();

            if (!this.currentPaymentProduct || this.isPaymentProductChanged(paymentProductId)) {
                // we should clear fields state when payment product was chosen before and changed to new one
                // to prevent situation when same fields in different payment product share same saved fields state
                if (this.currentPaymentProduct) {
                    this.clearFieldsState();
                }

                this.session
                    .getPaymentProduct(paymentProductId, this.options.paymentDetails)
                    .then(
                        paymentProduct => {
                            this.currentPaymentProduct = paymentProduct;

                            // save state to hidden field and clear previously saved fields state
                            this.savePaymentProductState(paymentProductId);

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

            this.$el.find(this.options.selectors.body).html(this.paymentProductListTemplate({productPayments: items}));
        },

        renderPaymentProductFields: function(event) {
            event.preventDefault();
            const paymentProductId = $(event.currentTarget).data('product-id');

            this.getSession()
                .then(() => this.getPaymentProductDetails(paymentProductId))
                .then(() => this.renderCurrentPaymentProductFields());
        },

        renderCurrentPaymentProductFields: function() {
            if (!this.currentPaymentProduct.paymentProductFields) {
                return;
            }

            const fieldsState = this.getFieldsState();
            const fields = [];
            _.each(this.currentPaymentProduct.paymentProductFields, field => {
                // look for previously saved value
                const valueItem = _.find(fieldsState, item => item.field === field.id);
                const value = valueItem ? valueItem.value : '';

                const rendererFieldName = 'ingenico::' + field.id;
                fields.push(_.macros(rendererFieldName)({
                    paymentMethod: this.options.paymentMethod,
                    field: field,
                    value: value
                }));
            });

            this.$el.find(this.options.selectors.body).html(fields.join(''));
        },

        isPaymentProductChanged: function(paymentProductId) {
            return this.currentPaymentProduct.id !== paymentProductId;
        },

        /**
         * @param {Object} eventData
         */
        beforeTransit: function(eventData) {
            if (eventData.data.paymentMethod === this.options.paymentMethod) {
                eventData.stopped = true;
                if (!this.currentPaymentProduct) {
                    return;
                }
                const fields = this.collectFormData();
                if (this.validate(fields)) {
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
                    let value = $(fieldName).val();

                    // workaround for showing validation message
                    // because empty value does not treat as error by sdk validation
                    if (!value) {
                        value = '#';
                    }
                    fields.push({
                        field: field.id,
                        value: value
                    });
                }
            });

            return fields;
        },

        validate: function(fields) {
            if (!this.currentPaymentProduct) {
                mediator.execute('showFlashMessage', 'error', __('ingenico.no_choosen_payment_product'));

                return false;
            }

            const paymentRequest = this.session.getPaymentRequest();
            paymentRequest.setPaymentProduct(this.currentPaymentProduct);

            // field payment request with data
            _.each(fields, function(item) {
                paymentRequest.setValue(item.field, item.value);
            });

            if (!paymentRequest.isValid()) {
                _.each(paymentRequest.getPaymentProduct().paymentProductFields, field => {
                    const fieldName = this.buildFieldIdentifier(field.id, 'error');
                    if ($(fieldName).length) {
                        if (field.getErrorCodes().length) {
                            $(fieldName).removeClass('hidden');
                        } else {
                            $(fieldName).addClass('hidden');
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
                    this.addPaymentAdditionalData({
                        ingenicoPaymentProduct: paymentRequest.getPaymentProduct().paymentProductGroup,
                        ingenicoCustomerEncDetails: encryptedString
                    });
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

            this.$el.off('click' + this.cid, '.payment-product__item');

            IngenicoCreditCardComponent.__super__.dispose.call(this);
        }
    });

    return IngenicoCreditCardComponent;
});
