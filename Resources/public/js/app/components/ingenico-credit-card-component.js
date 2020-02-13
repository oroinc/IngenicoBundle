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
    const errorHintTemplate = require('tpl-loader!ingenico/templates/error-hint.html');

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
                },
                paymentProductChoice: '.payment-product__choice',
                paymentProductItem: '.payment-product',
                paymentProductFormFieldsHolder: '.payment-product__form-fields',
                genericInput: '.input--full'
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
        errorHintTemplate: errorHintTemplate,

        /**
         * @property {jQuery}
         */
        $el: null,

        /**
         * @property {Boolean}
         */
        disposable: true,

        /**
         * @property {Boolean}
         */
        rendered: false,

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

            this.$el.on('change.' + this.cid, this.options.selectors.genericInput, this.saveFieldsState.bind(this));

            this.$el.on(
                'click.' + this.cid,
                this.options.selectors.paymentProductChoice,
                this.onPaymentProductItemClick.bind(this)
            );

            this.$el.on(
                'focusout.' + this.cid,
                this.options.selectors.genericInput,
                this.validateField.bind(this)
            );

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
            const fields = this.collectFormData();
            this.$el.find(this.options.selectors.state.paymentProductFields).val(JSON.stringify(fields));
        },

        clearFieldsState: function() {
            this.$el.find(this.options.selectors.state.paymentProductFields).val('');
        },

        _initializeIngenicoPayment: function() {
            this._deferredInit();

            this.getSession()
                .then(() => this.getPaymentProducts(), () => this._resolveDeferredInit())
                .then(() => {
                    if (!this.paymentProductItems.length) {
                        return;
                    }

                    this.renderPaymentProductsList();
                    const paymentProductId = parseInt(this.getPaymentProductState());
                    const paymentProductInList = paymentProductId
                        ? _.find(this.paymentProductItems, item => item.id === paymentProductId) : false;
                    if (paymentProductInList) {
                        this.showSelectedPaymentProductFields(paymentProductId)
                            .then(() => this._resolveDeferredInit());
                    } else {
                        this._resolveDeferredInit();
                    }
                });
        },

        onPaymentMethodChanged: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                this._initializeIngenicoPayment();
            }
        },

        /**
         * Gets Ingenico session to operate with Ingeinco JS SDK.
         * The session represents checkout amount, currency, locale values
         * and Ingenico's expected payment product to be used.
         */
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

        /**
         * Retrieves payment products general details with Ingenico SDK by properly setup Ingenico session.
         */
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

        /**
         * Retrieves payment product details with Ingenico SDK.
         */
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

                            // save chosen payment product state
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

        /**
         * Renders payment products list retrieved with Ingenico SDK.
         */
        renderPaymentProductsList: function() {
            // don't allow the same content to be displayed a couple of times
            if (this.rendered) {
                return;
            }

            const items = _.map(this.paymentProductItems, function(item) {
                return {
                    id: item.id,
                    label: item.displayHints.label,
                    logo: item.displayHints.logo
                };
            });

            const templateVars = {
                paymentProducts: items,
                paymentMethod: this.options.paymentMethod
            };

            this.$el.find(this.options.selectors.body).html(this.paymentProductListTemplate(templateVars));
            this.rendered = true;
        },

        /**
         * Payment products forms switcher(accordion like).
         */
        onPaymentProductItemClick: function(event) {
            const paymentProductId = $(event.currentTarget).data('product-id');
            this.showSelectedPaymentProductFields(paymentProductId);
        },

        /**
         * Renders selected payment product's form if it's not.
         */
        showSelectedPaymentProductFields: function(paymentProductId) {
            const deffer = $.Deferred();

            const element = _.first(_.filter(
                this.$el.find(this.options.selectors.paymentProductChoice),
                el => $(el).data('product-id') === paymentProductId
            ));
            const choiceElement = $(element);

            // fix checkbox checked state when function calls not from element click event
            choiceElement.prop('checked', true);

            const paymentProductFieldsHolder = choiceElement
                .parents(this.options.selectors.paymentProductItem)
                .find(this.options.selectors.paymentProductFormFieldsHolder);

            this.$el.find(this.options.selectors.paymentProductChoice).attr('area-expanded', false)
                .removeAttr('aria-disabled');
            this.$el.find(this.options.selectors.paymentProductFormFieldsHolder)
                .addClass('hidden');

            if (paymentProductFieldsHolder.data('paymentProduct')) {
                this.currentPaymentProduct = paymentProductFieldsHolder.data('paymentProduct');
                paymentProductFieldsHolder.removeClass('hidden');
                choiceElement.attr('area-expanded', true)
                    .attr('aria-disabled', true);

                // update product fields state
                this.saveFieldsState();

                // save chosen payment product state
                this.savePaymentProductState(paymentProductId);

                deffer.resolve();

                return deffer.promise();
            }

            this.getSession()
                .then(() => this.getPaymentProductDetails(paymentProductId))
                .then(() => {
                    const renderedFields = this.renderPaymentProductFields(
                        this.currentPaymentProduct.paymentProductFields,
                        paymentProductId
                    );

                    paymentProductFieldsHolder.html(renderedFields.join(''))
                        .data('paymentProduct', this.currentPaymentProduct)
                        .removeClass('hidden');

                    choiceElement.attr('area-expanded', true)
                        .attr('aria-disabled', true);

                    deffer.resolve();
                }, () => deffer.reject());

            return deffer.promise();
        },

        /**
         * Renders payment product fields according to guiding rules retrieved with Ingenico SDK.
         */
        renderPaymentProductFields: function(fields, paymentProductId) {
            const renderedFields = [];
            const fieldsState = this.getFieldsState();
            _.each(fields, field => {
                // look for previously saved value
                const valueItem = _.find(fieldsState, item => item.field === field.id);
                const value = valueItem ? valueItem.value : '';

                const rendererFieldName = 'ingenico::' + field.id;
                renderedFields.push(_.macros(rendererFieldName)({
                    paymentMethod: this.options.paymentMethod,
                    paymentProductId: paymentProductId,
                    field: field,
                    value: value,
                    fieldElementId: this.buildFieldIdentifier(field.id, 'field', paymentProductId),
                    fieldErrorElementId: this.buildFieldIdentifier(field.id, 'error', paymentProductId)
                }));
            });

            return renderedFields;
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

        /**
         * Payment product form field identifier builder according to currently selected payment product.
         */
        buildFieldIdentifier: function(fieldId, key, productId) {
            return fieldId + '-' + (productId ? productId : this.currentPaymentProduct.id) +
                '-' + this.options.paymentMethod + '-' + key;
        },

        /**
         * Collects form fields values for selected payment product.
         */
        collectFormData: function() {
            const collectedFields = [];
            if (!this.currentPaymentProduct) {
                return collectedFields;
            }

            _.each(this.currentPaymentProduct.paymentProductFields, field => {
                const fieldName = '#' + this.buildFieldIdentifier(field.id, 'field');
                if ($(fieldName).length) {
                    const value = $(fieldName).val();

                    collectedFields.push({
                        field: field.id,
                        value: value
                    });
                }
            });

            return collectedFields;
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

            const isValid = paymentRequest.isValid();
            // showing new errors for collected fields only (case when form field looses focus)
            _.each(paymentRequest.getPaymentProduct().paymentProductFields, paymentField => {
                const canShowErrors = _.find(fields, function(item) {
                    if (paymentField.id === item.field) {
                        return true;
                    }
                });
                if (!canShowErrors) {
                    return;
                }

                const field = paymentRequest.getPaymentProduct().paymentProductFieldById[paymentField.id];
                if (field) {
                    const fieldValue = paymentRequest.getValue(paymentField.id);
                    if (!field.isValid(fieldValue)) {
                        this.addError(paymentField.id);
                    } else {
                        this.removeError(paymentField.id);
                    }
                }
            });

            return isValid;
        },

        /**
         * Validates single field on its 'focusout' event.
         */
        validateField: function(event) {
            const fieldElement = $(event.currentTarget);
            const fields = [{
                field: fieldElement.data('field-id'),
                value: fieldElement.val()
            }];

            return this.validate(fields);
        },

        /**
         * Add error hint below field with validation message
         */
        addError: function(fieldId, message) {
            const fieldElementId = '#' + this.buildFieldIdentifier(fieldId, 'field');
            const fieldErrorElementId = this.buildFieldIdentifier(fieldId, 'error');
            const fieldErrorElement = $('#' + fieldErrorElementId);
            if (!$(fieldErrorElement).length) {
                const errorMessage = message ? message : __('ingenico.general_error');
                const fieldErrorElementClass = this.buildFieldIdentifier(fieldId, 'error');
                const templateOptions = {
                    fieldErrorElementId: fieldErrorElementId,
                    fieldErrorElementClass: fieldErrorElementClass,
                    errorMessage: errorMessage
                };
                // notice: error container should be <p> not <span> as it conflicts with jquery.validation
                $(fieldElementId).after(this.errorHintTemplate(templateOptions));
            }
        },

        /**
         * Remove error hint from field
         */
        removeError: function(fieldId) {
            const fieldErrorElementId = '#' + this.buildFieldIdentifier(fieldId, 'error');
            $(fieldErrorElementId).remove();
        },

        /**
         * Crypts selected payment product's form values and storing it to DOM storage.
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

        /**
         * Allows to expand payment transaction additional data with given object's values.
         * This data is sent on payment method step submission.
         */
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
                if ({}.hasOwnProperty.call(updateData, key)) {
                    additionalData[key] = updateData[key];
                }
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

            this.$el.off('change.' + this.cid, this.options.selectors.genericInput);
            this.$el.off('click.' + this.cid, this.options.selectors.paymentProductChoice);
            this.$el.off('focusout.' + this.cid, this.options.selectors.genericInput);

            IngenicoCreditCardComponent.__super__.dispose.call(this);
        }
    });

    return IngenicoCreditCardComponent;
});
