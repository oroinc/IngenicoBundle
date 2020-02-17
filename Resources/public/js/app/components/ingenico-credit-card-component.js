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
    require('jquery.validate');

    const IngenicoCreditCardComponent = BaseComponent.extend({
        options: {
            paymentMethod: null,
            paymentDetails: {},
            createSessionRoute: 'ingenico_create_session',
            paymentProducts: {
                sepaId: 770,
            },
            selectors: {
                paymentProductChoice: '.payment-product__choice',
                paymentProductItem: '.payment-product',
                paymentProductFormFieldsHodler: '.payment-product__form-fields',
                genericInput: '.input--full'
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
        bankCodeFieldId: 'bankCode', // bank code field ID in payment product object received via Ingenico's SDK
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

            $.validator.loadMethod('ingenico/js/validator/sepa-iban');

            this.$el.on(
                'click.' + this.cid,
                this.options.selectors.paymentProductChoice,
                this.showSelectedPaymentProductFields.bind(this)
            );

            this.$el.on(
                'focusout.' + this.cid,
                this.options.selectors.genericInput,
                this.validateField.bind(this)
            );
        },

        refreshPaymentMethod: function() {
            mediator.trigger('checkout:payment:method:changed', {paymentMethod: this.options.paymentMethod});
        },

        onPaymentMethodChanged: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                this.getSession()
                    .then(this.getPaymentProducts.bind(this))
                    .then(this.renderPaymentProductsList.bind(this));
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
                            try {
                                this.session = new ConnectSdk(data.sessionInfo);
                            } catch (e) {
                                this.$el.html(__('ingenico.payment_method_is_not_available'));
                                mediator.execute('hideLoading');
                                deffer.reject();
                                return;
                            }

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
                        mediator.execute('hideLoading');
                        this.$el.html(__('ingenico.api.error.no_available_payment_products'));
                        deffer.reject();
                    }
                );
            }

            return deffer.promise();
        },

        /**
         * Retrieves payment product details with Ingenico SDK
         */
        getPaymentProductDetails: function(paymentProductId) {
            const deffer = $.Deferred();

            if (this.isPaymentProductChanged(paymentProductId)) {
                mediator.execute('showLoading');

                this.session
                    .getPaymentProduct(paymentProductId, this.options.paymentDetails)
                    .then(
                        paymentProduct => {
                            this.fixFieldsRestrictions(paymentProduct);
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

        /**
         * Workaround to solve payment product's fields validation issues
         * caused by empty validation rules from the Ingenico SDK
         */
        fixFieldsRestrictions: function(paymentProduct) {
            if (paymentProduct.paymentProductFieldById[this.bankCodeFieldId]) {
                this.fixBankCodeFieldRestrictions(paymentProduct.paymentProductFieldById[this.bankCodeFieldId]);
            }
        },

        /**
         * Fixes bank code field issued validation setup.
         */
        fixBankCodeFieldRestrictions: function(bankCodeField) {
            const validationRuleByType = bankCodeField.dataRestrictions.validationRuleByType;

            if (validationRuleByType['length']) {
                // maxLength returned from the SDK is 8, but it should be 9
                validationRuleByType.length.maxLength = 9;
            }

            if (validationRuleByType['regularExpression']) {
                // original regularExpression is '^[0-8]{1,8}$'
                validationRuleByType.regularExpression.regularExpression = '^[0-9]{1,9}$';
            }
        },

        /**
         * Renders payment products list retrieved with Ingenico SDK
         */
        renderPaymentProductsList: function() {
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

            return this.$el.html(this.paymentProductListTemplate(templateVars));
        },

        /**
         * Payment products forms switcher(accordion like).
         * Also renders selected payment product's form if it's not
         */
        showSelectedPaymentProductFields: function(event) {
            const choiceElement = $(event.currentTarget);
            const paymentProductId = choiceElement.data('product-id');
            const paymentProductFieldsHolder = $(event.currentTarget)
                .parents(this.options.selectors.paymentProductItem)
                .find(this.options.selectors.paymentProductFormFieldsHodler);

            this.$el.find(this.options.selectors.paymentProductChoice).attr('area-expanded', false)
                .removeAttr('aria-disabled');
            this.$el.find(this.options.selectors.paymentProductFormFieldsHodler)
                .addClass('hidden');

            if (paymentProductFieldsHolder.data('paymentProduct')) {
                this.currentPaymentProduct = paymentProductFieldsHolder.data('paymentProduct');
                paymentProductFieldsHolder.removeClass('hidden');
                choiceElement.attr('area-expanded', true)
                    .attr('aria-disabled', true);

                return;
            }

            this.getSession()
                .then(() => this.getPaymentProductDetails(paymentProductId))
                .then(() => {
                    const fields = paymentProductId === this.options.paymentProducts.sepaId
                        ? this.getSepaFieldsInfo() : this.currentPaymentProduct.paymentProductFields;
                    const renderedFields = this.renderPaymentProductFields(fields, paymentProductId);

                    paymentProductFieldsHolder.html(renderedFields.join(''))
                        .data('paymentProduct', this.currentPaymentProduct)
                        .removeClass('hidden');

                    choiceElement.attr('area-expanded', true)
                        .attr('aria-disabled', true);
                });
        },

        /**
         * Renders payment product fields according to guiding rules retrieved with Ingenico SDK.
         */
        renderPaymentProductFields: function(fields, paymentProductId) {
            const renderedFields = [];
            _.each(fields, field => {
                const rendererFieldName = 'ingenico::' + field.id;
                renderedFields.push(_.macros(rendererFieldName)({
                    paymentMethod: this.options.paymentMethod,
                    paymentProductId: paymentProductId,
                    field: field,
                    fieldElementId: this.buildFieldIdentifier(field.id, 'field', paymentProductId),
                    fieldErrorElementId: this.buildFieldIdentifier(field.id, 'error', paymentProductId)
                }));
            });

            return renderedFields;
        },

        isPaymentProductChanged: function(paymentProductId) {
            if (!this.currentPaymentProduct) {
                return true;
            }

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
                    this.clearAdditionalData();
                    this.addPaymentAdditionalData({
                        ingenicoPaymentProduct: this.getPaymentProductAlias(this.currentPaymentProduct)
                    });

                    if (this.currentPaymentProduct.id === this.options.paymentProducts.sepaId) {
                        this.storeCollectedFields(fields, 'ingenicoSepaDetails');
                        this.addPaymentAdditionalData({ingenicoCustomerEncDetails: null});

                        eventData.resume();
                    } else {
                        this.storeEcryptedCutomerDetailes().then(function() {
                            eventData.resume();
                        }).catch(function() {
                            mediator.execute('hideLoading');
                        });
                    }
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
            const fields = this.currentPaymentProduct.id === this.options.paymentProducts.sepaId
                ? this.getSepaFieldsInfo() : this.currentPaymentProduct.paymentProductFields;

            _.each(fields, field => {
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

        /**
         * Validates fulfilled form data for currently selected payment product using Ingenico SDK.
         * For SEPA payment product Oro application's validation tools are used.
         */
        validate: function(fields) {
            if (!this.currentPaymentProduct) {
                mediator.execute('showFlashMessage', 'error', __('ingenico.no_choosen_payment_product'));

                return false;
            }

            // SEPA payment product form data is validated with internal tools.
            if (this.currentPaymentProduct.id === this.options.paymentProducts.sepaId) {
                return this.validateSepaFields(fields);
            }

            const paymentRequest = this.session.getPaymentRequest();
            paymentRequest.setPaymentProduct(this.currentPaymentProduct);

            // field payment request with data
            _.each(fields, function(item) {
                paymentRequest.setValue(item.field, item.value);
            });

            const isValid = paymentRequest.isValid();
            // showing new errors for collected fields only (case when form field looses focus)
            _.each(paymentRequest.getValues(), (value, name) => {
                // Payment request is stored inside the session.
                // session.getPaymentRequest() returns the same instance each time and it has all fields
                // even from the another payment products
                // We need to verify that this field is applicable for current payment product
                const field = paymentRequest.getPaymentProduct().paymentProductFieldById[name];
                if (!field) {
                    return;
                }

                if (!field.isValid(value) || (field.dataRestrictions.isRequired && value === "")) {
                    this.addError(name);
                } else {
                    this.removeError(name);
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
            const fieldElementId = this.buildFieldIdentifier(fieldId, 'field');
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
                $('#' + fieldElementId).after(this.errorHintTemplate(templateOptions));
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
         * Stores given payment product fields values into transactions additional data
         * without encryption and with given values domain.
         */
        storeCollectedFields: function(fields, valuesDomain) {
            _.each(fields, field => {
                const mappedField = {};
                mappedField[valuesDomain + ':' + field.field] = field.value;

                this.addPaymentAdditionalData(mappedField);
            });
        },

        /**
         * Crypts sensitive part of create payment request to be safely processed on server side
         */
        storeEcryptedCutomerDetailes: function() {
            const deffer = $.Deferred();

            const encryptor = this.session.getEncryptor();
            const paymentRequest = this.session.getPaymentRequest();
            encryptor.encrypt(paymentRequest).then(
                encryptedString => {
                    this.addPaymentAdditionalData({
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
         * Gets payment product group alias for given payment product
         * so it will be handled with dedicated processor on server side.
         */
        getPaymentProductAlias: function(paymentProduct) {
            return paymentProduct.paymentProductGroup ? paymentProduct.paymentProductGroup : paymentProduct.id;
        },

        /**
         * Missing SEPA form fields retrieval.
         * Ingenico SDK doesn't return any fields for SEPA DD. Render them manually.
         * This data structure copycats one that is received for other payment products from Ingenico SDK.
         */
        getSepaFieldsInfo: function() {
            return [
                {
                    id: 'iban',
                    dataRestrictions: {
                        isRequired: true
                    },
                    displayHints: {
                        label: __('ingenico.sepa.fields.iban.label'),
                        placeholderLabel: __('ingenico.sepa.fields.iban.placeholder')
                    },
                    dataValidation: {
                        'ingenico-sepa-iban': {
                            payload: null
                        },
                        NotBlank: {
                            payload: null,
                            allowNull: false,
                            normalizer: null
                        },
                        Length: {
                            // Max length for iban field from the Ingenico API doc
                            max: 50
                        }
                    }
                },
                {
                    id: 'accountHolderName',
                    dataRestrictions: {
                        isRequired: true
                    },
                    displayHints: {
                        label: __('ingenico.sepa.fields.accountHolderName.label'),
                        placeholderLabel: __('ingenico.sepa.fields.accountHolderName.placeholder')
                    },
                    dataValidation: {
                        NotBlank: {
                            payload: null,
                            allowNull: false,
                            normalizer: null
                        },
                        Length: {
                            // Max length for accountHolderName field from the Ingenico API doc
                            max: 30
                        }
                    }
                },
                {
                    id: 'mandateDisclaimer',
                    dataRestrictions: {
                        isRequired: false
                    },
                    displayHints: {
                        label: __('ingenico.sepa.fields.mandateDisclaimer.label'),
                        placeholderLabel: ''
                    },
                    dataValidation: {}
                }
            ];
        },

        /**
         * Validates SEPA form fields because Ingenico SDK doesn't provide any validators for SEPA
         */
        validateSepaFields: function(fields) {
            const virtualForm = $('<form>');
            _.each(fields, item => {
                this.removeError(item.field);

                const fieldElementClone = $('#' + this.buildFieldIdentifier(item.field, 'field')).clone();
                fieldElementClone.data('fieldId', item.field);
                virtualForm.append(fieldElementClone);
            });

            const validator = virtualForm.validate({
                ignore: '',
                errorPlacement: (error, element) => {
                    this.addError(element.data('fieldId'), error.html());
                }
            });

            return validator.form();
        },

        clearAdditionalData: function() {
            mediator.trigger('checkout:payment:additional-data:set', JSON.stringify({}));
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

            this.$el.off('.' + this.cid);

            IngenicoCreditCardComponent.__super__.dispose.call(this);
        }
    });

    return IngenicoCreditCardComponent;
});
