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
    require('jquery.validate');

    const IngenicoCreditCardComponent = BaseComponent.extend({
        options: {
            paymentMethod: null,
            paymentDetails: {},
            paymentProductAliasesInfo: {},
            createSessionRoute: 'ingenico.create-session'
        },

        session: null,
        paymentProductItems: [],
        currentPaymentProduct: null,
        paymentProductListTemplate: paymentProductListTemplate,
        bankCodeFieldId: 'bankCode', // bank code field ID in payment product object received via Ingenico's SDK

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
                'click.' + this.cid, '.payment-product__anchor-label',
                this.showSelectedPaymentProductFields.bind(this)
            );

            mediator.on('checkout:payment:method:changed', this.onPaymentMethodChange, this);
            mediator.on('checkout-content:initialized', this.refreshPaymentMethod, this);
            mediator.on('checkout:payment:before-transit', this.beforeTransit, this);
            mediator.on('checkout:payment:before-restore-filled-form', this.beforeRestoreFilledForm, this);
            mediator.on('checkout:payment:before-hide-filled-form', this.beforeHideFilledForm, this);
            mediator.on('checkout:payment:remove-filled-form', this.removeFilledForm, this);
        },

        refreshPaymentMethod: function() {
            mediator.trigger('checkout:payment:method:changed', {paymentMethod: this.options.paymentMethod});
        },

        onPaymentMethodChange: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
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
                        {paymentIdentifier: this.options.paymentMethod}
                    ),
                    function(data) {
                        if (data.success) {
                            this.session = new connectsdk(data.sessionInfo);
                            deffer.resolve();
                        } else {
                            mediator.execute('showFlashMessage', 'error', data.errorMessage);
                            deffer.reject();
                        }

                        mediator.execute('hideLoading');
                    }.bind(this)
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
                    function(_basicPaymentItems) {
                        this.paymentProductItems = _basicPaymentItems.basicPaymentItems;
                        mediator.execute('hideLoading');
                        deffer.resolve();
                    }.bind(this),
                    function() {
                        mediator.execute('hideLoading');
                        mediator.execute('showFlashMessage', 'error', __('ingenico.api.error.get_payment_products'));
                        deffer.reject();
                    }
                );
            }

            return deffer.promise();
        },

        /**
         * Retreives payment product details with Ingenico SDK
         */
        getPaymentProductDetails: function(paymentProductId) {
            const deffer = $.Deferred();

            if (this.isPaymentProductChanged(paymentProductId)) {
                mediator.execute('showLoading');
                this.session
                    .getPaymentProduct(paymentProductId, this.options.paymentDetails)
                    .then(
                        function(paymentProduct) {
                            // Workaround to solve payment product's fields validation issues
                            // caused by improper fields setup retrieved Ingenico SDK.
                            // @INGA-40 feature related.
                            this.fixFieldsRestrictions(paymentProduct);
                            this.currentPaymentProduct = paymentProduct;
                            mediator.execute('hideLoading');
                            deffer.resolve();
                        }.bind(this),
                        function() {
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
         * Workaround to solve payment product fields validation issues
         * caused by improper fields setup received from Ingenico SDK.
         * @INGA-40 related feature woraround.
         */
        fixFieldsRestrictions: function(paymentProduct) {
            if (paymentProduct.paymentProductFieldById[this.bankCodeFieldId]) {
                this.fixBankCodeFieldRestrictions(paymentProduct.paymentProductFieldById[this.bankCodeFieldId]);
            }
        },

        /**
         * Fixes bank code field issued validation setup.
         * @INGA-40 related feature woraround.
         */
        fixBankCodeFieldRestrictions: function(bankCodeField) {
            const validationRuleByType = bankCodeField.dataRestrictions.validationRuleByType;

            if (validationRuleByType['length']) {
                // original maxLength is 8. Proper bank code for test proposes is '121000248'
                validationRuleByType.length.maxLength = 9;
            }

            if (validationRuleByType['regularExpression']) {
                // original regularExpression is '^[0-9]{1,9}$'
                validationRuleByType.regularExpression.regularExpression = '^[0-9]{1,9}$';
            }
        },

        /**
         * Renders payment product list retrieved with Ingenico SDK
         */
        renderPaymentProductsList: function() {
            const items = _.map(this.paymentProductItems, function(item) {
                return {
                    id: item.id,
                    label: item.displayHints.label,
                    logo: item.displayHints.logo
                };
            });

            return this.$el.html(this.paymentProductListTemplate({productPayments: items}));
        },

        /**
         * Payment products forms switcher(accordion like)
         */
        showSelectedPaymentProductFields: function(event) {
            event.preventDefault();

            const paymentProductAliasesInfo = this.options.paymentProductAliasesInfo;
            const paymentProductId = $(event.currentTarget).data('product-id');
            const paymentProductFieldsHolder = $(event.currentTarget).parents('.payment-product')
                .find('.payment-product__form-fields');

            this.$el.find('.payment-product__form-fields').addClass('hidden');
            if (paymentProductFieldsHolder.data('paymentProduct')) {
                this.currentPaymentProduct = paymentProductFieldsHolder.data('paymentProduct');
                paymentProductFieldsHolder.removeClass('hidden');
                return;
            }

            this.getSession()
                .then(this.getPaymentProductDetails.bind(this, paymentProductId))
                .then(function() {
                    const fields = paymentProductId === paymentProductAliasesInfo.sepaProductId
                        ? this.getSepaFieldsInfo() : this.currentPaymentProduct.paymentProductFields;
                    const renderedFields = this.renderPaymentProductFields(fields, paymentProductId);

                    paymentProductFieldsHolder.html(renderedFields.join(''))
                        .data('paymentProduct', this.currentPaymentProduct)
                        .removeClass('hidden');
                }.bind(this));
        },

        /**
         * Renders payment product fields according to guiding rules retrieved with Ingenico SDK.
         */
        renderPaymentProductFields: function(fields, paymentProductId) {
            const renderedFields = [];
            _.each(fields, function(field) {
                const rendererFieldName = 'ingenico::' + field.id;
                renderedFields.push(_.macros(rendererFieldName)({
                    paymentMethod: this.options.paymentMethod,
                    paymentProductId: paymentProductId,
                    field: field
                }));
            }.bind(this));

            return renderedFields;
        },

        isPaymentProductChanged: function(paymentProductId) {
            if (!this.currentPaymentProduct) {
                return true;
            }

            if (this.currentPaymentProduct.id !== paymentProductId) {
                return true;
            }

            return false;
        },

        /**
         * @param {Object} eventData
         */
        beforeTransit: function(eventData) {
            if (eventData.data.paymentMethod === this.options.paymentMethod) {
                eventData.stopped = true;
                const fields = this.collectFormData();
                if (this.validate(fields)) {
                    this.addPaymentAdditionalData({
                        ingenicoPaymentProduct: this.getPaymentProductAlias(this.currentPaymentProduct)
                    });

                    if (this.currentPaymentProduct.id === this.options.paymentProductAliasesInfo.sepaProductId) {
                        this.storeCollectedFields(fields, 'ingenicoSepaDetails');
                        eventData.resume();
                    } else {
                        this.storeEcryptedCutomerDetailes().then(function() {
                            eventData.resume();
                        });
                    }
                }
            }
        },

        /**
         * Payment product form field identifier builder according to currently selected payment product.
         */
        buildFieldIdentifier: function(fieldId, key) {
            return '#' + fieldId + '-' + this.currentPaymentProduct.id + '-' + this.options.paymentMethod + '-' + key;
        },

        /**
         * Collects form fields values for selected payment product.
         */
        collectFormData: function() {
            const collectedFields = [];
            const fields = this.currentPaymentProduct.id === this.options.paymentProductAliasesInfo.sepaProductId
                ? this.getSepaFieldsInfo() : this.currentPaymentProduct.paymentProductFields;

            _.each(fields, function(field) {
                const fieldName = this.buildFieldIdentifier(field.id, 'field');
                if ($(fieldName).length) {
                    const value = $(fieldName).val();

                    collectedFields.push({
                        field: field.id,
                        value: value
                    });
                }
            }.bind(this));

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
            if (this.currentPaymentProduct.id === this.options.paymentProductAliasesInfo.sepaProductId) {
                return this.validateSepaFields(fields);
            }

            const paymentRequest = this.session.getPaymentRequest();
            paymentRequest.setPaymentProduct(this.currentPaymentProduct);

            // field payment request with data
            _.each(fields, function(item) {
                paymentRequest.setValue(item.field, item.value);
            });

            if (!paymentRequest.isValid()) {
                _.each(paymentRequest.getPaymentProduct().paymentProductFields, function(field) {
                    const fieldName = this.buildFieldIdentifier(field.id, 'error');
                    const fieldValue = paymentRequest.getValue(field.id);

                    if ($(fieldName).length) {
                        if (field.getErrorCodes().length || (field.dataRestrictions.isRequired && !fieldValue)) {
                            $(fieldName).removeClass('hidden');
                        } else {
                            $(fieldName).addClass('hidden');
                        }
                    }
                }.bind(this));

                return false;
            }

            return true;
        },

        /**
         * Stores given payment product fields values into transactions additional data
         * without encryption and with given values domain.
         */
        storeCollectedFields: function(fields, valuesDomain) {
            _.each(fields, function(field) {
                const mappedField = {};
                mappedField[valuesDomain + ':' + field.field] = field.value;

                this.addPaymentAdditionalData(mappedField);
            }.bind(this));
        },

        /**
         * Crypts sensitive part of create payment request to be safely processed on server side
         */
        storeEcryptedCutomerDetailes: function() {
            const deffer = $.Deferred();

            const encryptor = this.session.getEncryptor();
            const paymentRequest = this.session.getPaymentRequest();
            encryptor.encrypt(paymentRequest).then(
                function(encryptedString) {
                    this.addPaymentAdditionalData({
                        ingenicoCustomerEncDetails: encryptedString
                    });

                    deffer.resolve();
                }.bind(this),
                function() {
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
            const paymentProductAliasesInfo = this.options.paymentProductAliasesInfo;

            if (paymentProduct.id === paymentProductAliasesInfo.achProductId) {
                return paymentProductAliasesInfo.achProductAlias;
            } else if (paymentProduct.id === paymentProductAliasesInfo.sepaProductId) {
                return paymentProductAliasesInfo.sepaProductAlias;
            }

            return paymentProduct.paymentProductGroup ? paymentProduct.paymentProductGroup : '';
        },

        /**
         * Missing SEPA form fields retrieval.
         * This data structure copycats one that is received for other payment products from Ingenico SDK.
         * @INGA-45 feature related workaround.
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
                        'NotBlank': {
                            payload: null,
                            allowNull: false,
                            normalizer: null
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
                        'NotBlank': {
                            payload: null,
                            allowNull: false,
                            normalizer: null
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
         * Validates SEPA form fields with Oro application's enhanced jQuery validation.
         * @INGA-45 feature related workaround.
         */
        validateSepaFields: function(fields) {
            const virtualForm = $('<form>');
            _.each(fields, function(item) {
                $(this.buildFieldIdentifier(item.field, 'error')).addClass('hidden');

                const fieldElementClone = $(this.buildFieldIdentifier(item.field, 'field')).clone();
                fieldElementClone.data('fieldId', item.field);
                virtualForm.append(fieldElementClone);
            }.bind(this));

            const self = this;
            const validator = virtualForm.validate({
                ignore: '',
                errorPlacement: function(error, element) {
                    const fieldErrorElement = $(self.buildFieldIdentifier(element.data('fieldId'), 'error'));
                    fieldErrorElement.html(error.html());
                    fieldErrorElement.removeClass('hidden');
                }
            });

            return validator.form();
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

            mediator.off('checkout:payment:method:changed', this.onPaymentMethodChange, this);
            mediator.off('checkout-content:initialized', this.refreshPaymentMethod, this);
            mediator.off('checkout:payment:before-transit', this.beforeTransit, this);
            mediator.off('checkout:payment:before-restore-filled-form', this.beforeRestoreFilledForm, this);
            mediator.off('checkout:payment:before-hide-filled-form', this.beforeHideFilledForm, this);
            mediator.off('checkout:payment:remove-filled-form', this.removeFilledForm, this);

            this.$el.off('click' + this.cid, '.payment-product__anchor-label');

            IngenicoCreditCardComponent.__super__.dispose.call(this);
        }
    });

    return IngenicoCreditCardComponent;
});
