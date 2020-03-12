define(function(require) {
    'use strict';

    window.forge = require('node-forge');

    const _ = require('underscore');
    const __ = require('orotranslation/js/translator');
    const $ = require('jquery');
    const BaseComponent = require('oroui/js/app/components/base/component');
    const ConnectSdk = require('connect-sdk-client-js');
    const ConnectSdkMock = require('ingenico/js/app/stub/connect-sdk-client-mock');
    const mediator = require('oroui/js/mediator');
    const routing = require('routing');
    const paymentProductListTemplate = require('tpl-loader!ingenico/templates/payment-products-list.html');
    const errorHintTemplate = require('tpl-loader!ingenico/templates/error-hint.html');
    require('jquery.validate');

    const IngenicoPaymentComponent = BaseComponent.extend({
        options: {
            paymentMethod: null,
            paymentDetails: {},
            createSessionRoute: 'ingenico_create_session',
            saveForLaterUseEnabled: false,
            savedCreditCardList: [],
            tokenRequiredFields: ['cvv'],
            selectors: {
                body: '.payment-body',
                state: {
                    paymentProduct: '.current-payment-product',
                    paymentProductFields: '.current-payment-product-fields-values'
                },
                paymentProductChoice: '.payment-product__choice',
                paymentProductItem: '.payment-product',
                paymentProductFormFieldsHolder: '.payment-product__form-fields',
                genericInput: '.input--full',
                genericInputContainer: '.form-row',
                issuedWidgetClassPrefix: 'ingenico-widget-issued__'
            },
            paymentProducts: {},
            testMode: false
        },

        listen: {
            'checkout:payment:method:changed mediator': 'onPaymentMethodChanged',
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
        hiddenFieldTemplateMacro: 'hidden',
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
         * @property {Object}
         */
        connectSdk: null,

        /**
         * Contains saved original validation rules from the SDK in case of using tokenization
         * This validation rules will be restored to SDK in case customer doesn't want to use saved (tokenized) card
         *
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

            $.validator.loadMethod('ingenico/js/validator/sepa-iban');

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

            this.$el.on(
                'keyup.' + this.cid,
                this.options.selectors.genericInput,
                this.formatField.bind(this)
            );

            this._initializeIngenicoPayment();
        },

        refreshPaymentMethod: function() {
            mediator.trigger('checkout:payment:method:refresh');
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
            // we should initialize only selected payment methods
            if (!this.$el.is(':visible')) {
                return;
            }

            this.connectSdk = this.options.testMode ? ConnectSdkMock.default : ConnectSdk;

            const ingenicoWidgetIssuedClass = this.options.selectors.issuedWidgetClassPrefix +
                this.options.paymentMethod;

            // ommitting initilization in case if payment form is restored
            if (!$('.' + ingenicoWidgetIssuedClass).length) {
                // assigning listener here since it's impossible to dispose properly new(unnecessary) page component
                // for form container received via AJAX after existing form and its already initialized page component
                // are restored
                mediator.on('checkout:payment:before-transit', this.beforeTransit.bind(this));

                this._deferredInit();

                this.getSession()
                    .then(() => this.getPaymentProducts())
                    .then(() => {
                        if (!this.paymentProductItems.length) {
                            return;
                        }

                        this.renderPaymentProductsList();
                        const paymentProductId = parseInt(this.getPaymentProductState());
                        const paymentProductInList = paymentProductId
                            ? _.find(this.paymentProductItems, item => item.id === paymentProductId) : false;
                        if (paymentProductInList) {
                            return this.showSelectedPaymentProductFields(paymentProductId);
                        }
                    })
                    .always(() => this._resolveDeferredInit());

                this.$el.addClass(ingenicoWidgetIssuedClass);
            }
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
                        mediator.execute('hideLoading');
                        if (data.success) {
                            try {
                                this.session = new this.connectSdk(data.sessionInfo);
                            } catch (e) {
                                this.$el
                                    .find(this.options.selectors.body)
                                    .html(__('ingenico.payment_method_is_not_available'));
                                deffer.reject();
                                return;
                            }

                            deffer.resolve();
                        } else {
                            this.$el
                                .find(this.options.selectors.body)
                                .html(__('ingenico.payment_method_is_not_available'));
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
         * Retrieves payment product details with Ingenico SDK
         */
        getPaymentProductDetails: function(paymentProductId) {
            const deffer = $.Deferred();

            if (!this.currentPaymentProduct || this.isPaymentProductChanged(paymentProductId)) {
                // we should clear fields state when payment product was chosen before and changed to new one
                // to prevent situation when same fields in different payment product share same saved fields state
                if (this.currentPaymentProduct) {
                    this.clearFieldsState();
                }

                mediator.execute('showLoading');
                this.session
                    .getPaymentProduct(paymentProductId, this.options.paymentDetails)
                    .then(
                        paymentProduct => {
                            this.fixFieldsRestrictions(paymentProduct);
                            this.currentPaymentProduct = paymentProduct;

                            // save selected payment product state
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

            this.$el.find(this.options.selectors.body).html(this.paymentProductListTemplate(templateVars));
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

                // save selected payment product state
                this.savePaymentProductState(paymentProductId);

                deffer.resolve();

                return deffer.promise();
            }

            mediator.execute('showLoading');
            this.getSession()
                .then(() => this.getPaymentProductDetails(paymentProductId))
                .then(() => {
                    const fields = this.isCurrentProductSEPA()
                        ? this.getSEPAFieldsInfo() : this.currentPaymentProduct.paymentProductFields;

                    const renderedFields = this.renderPaymentProductFields(fields, paymentProductId);

                    paymentProductFieldsHolder.html(renderedFields.join(''))
                        .data('paymentProduct', this.currentPaymentProduct)
                        .removeClass('hidden');

                    paymentProductFieldsHolder.find('select').inputWidget('create', 'select2');

                    if (this.isTokenizationApplicable()) {
                        // Clean up saved required fields validation rules on initialization
                        delete this._requiredFields[this.currentPaymentProduct.id];
                        this.$el.on('change.' + this.cid, this.getTokenFieldSelector(), this.onTokenChange.bind(this));
                    }

                    choiceElement.attr('area-expanded', true)
                        .attr('aria-disabled', true);

                    deffer.resolve();
                }, () => deffer.reject())
                .always(() => mediator.execute('hideLoading'));

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

                const rendererFieldName = 'ingenico::' +
                    (!field._passThroughValue ? field.id : this.hiddenFieldTemplateMacro);

                const options = {
                    paymentMethod: this.options.paymentMethod,
                    paymentProductId: paymentProductId,
                    field: field,
                    value: value,
                    fieldElementId: this.buildFieldIdentifier(field.id, 'field', paymentProductId),
                    fieldErrorElementId: this.buildFieldIdentifier(field.id, 'error', paymentProductId)
                };

                renderedFields.push(this.getRenderedField(rendererFieldName, options));
            });

            // add extra fields related to tokenization
            if (this.isTokenizationApplicable()) {
                const creditCardList = this.getSavedCardList();
                if (creditCardList) {
                    renderedFields.unshift(_.macros('ingenico::token')({
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
                        paymentProductId: paymentProductId,
                        fieldElementId: this.buildFieldIdentifier('token', 'field', paymentProductId),
                        values: creditCardList
                    }));
                }

                const checkedItem = _.find(fieldsState, item => item.field === 'saveForLaterUse');
                const checked = checkedItem ? checkedItem.value : false;
                renderedFields.push(_.macros('ingenico::saveForLaterUse')({
                    paymentMethod: this.options.paymentMethod,
                    field: {
                        id: 'saveForLaterUse',
                        displayHints: {
                            label: __('ingenico.saveForLaterUse.label')
                        },
                        dataRestrictions: {
                            isRequired: false
                        }
                    },
                    checked: checked,
                    paymentProductId: paymentProductId,
                    fieldElementId: this.buildFieldIdentifier('saveForLaterUse', 'field', paymentProductId)
                }));
            }

            return renderedFields;
        },

        getRenderedField: function(rendererFieldName, options) {
            let renderedField;

            try {
                renderedField = _.macros(rendererFieldName)(options);
            } catch (e) {
                renderedField = _.macros('ingenico::default')(options);
            }

            return renderedField;
        },

        isPaymentProductChanged: function(paymentProductId) {
            return this.currentPaymentProduct.id !== paymentProductId;
        },

        getSavedCardList: function() {
            if (!this.currentPaymentProduct) {
                return null;
            }

            if (!_.has(this.options.savedCreditCardList, this.currentPaymentProduct.id)) {
                return null;
            }

            return this.options.savedCreditCardList[this.currentPaymentProduct.id];
        },

        getSaveForLaterSelector: function() {
            return '#' + this.buildFieldIdentifier('saveForLaterUse', 'field');
        },

        getTokenFieldSelector: function() {
            return '#' + this.buildFieldIdentifier('token', 'field');
        },

        isTokenizationApplicable: function() {
            if (!this.currentPaymentProduct) {
                return false;
            }

            return this.currentPaymentProduct.paymentProductGroup === this.options.paymentProducts.cardsGroup &&
                this.options.saveForLaterUseEnabled;
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
                    this.clearAdditionalData();
                    this.addPaymentAdditionalData({
                        ingenicoPaymentProduct: this.getPaymentProductAlias(this.currentPaymentProduct)
                    });

                    if (this.isCurrentProductSEPA()) {
                        this.storeCollectedFields(fields, 'ingenicoSEPADetails');

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

            if (!this.currentPaymentProduct) {
                return collectedFields;
            }

            const fields = this.isCurrentProductSEPA()
                ? this.getSEPAFieldsInfo() : this.currentPaymentProduct.paymentProductFields;

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

            if (this.isTokenizationApplicable()) {
                // Save state of "Save for Later"
                const $saveForLater = $(this.getSaveForLaterSelector());
                if ($saveForLater.length) {
                    collectedFields.push({
                        field: 'saveForLaterUse',
                        value: $saveForLater.prop('checked')
                    });
                }
            }

            return collectedFields;
        },

        onTokenChange: function(event) {
            if (event.target.value) {
                this._saveRequiredFieldsList();
                $(this.getSaveForLaterSelector()).prop('checked', false);
            } else {
                this._restoreRequiredFieldsList();
            }
        },

        _saveRequiredFieldsList: function() {
            // Skip if current payment product is not defined
            // or we already saved original validation rules from the SDK
            // (in case customer changed from one tokenized card to another)
            if (!this.currentPaymentProduct || this._requiredFields[this.currentPaymentProduct.id]) {
                return;
            }

            const requiredFields = {};
            _.each(this.currentPaymentProduct.paymentProductFields, field => {
                if (field.dataRestrictions.isRequired &&
                    _.indexOf(this.options.tokenRequiredFields, field.id) === -1) {
                    requiredFields[field.id] = field.dataRestrictions.validationRules;
                    field.dataRestrictions.isRequired = false;
                    field.dataRestrictions.validationRules = [];
                    this.currentPaymentProduct.paymentProductFieldById[field.id].isRequired = false;
                    this.currentPaymentProduct.paymentProductFieldById[field.id].validationRules = [];

                    this._hideField(field.id);
                }
            });

            this._hideField('saveForLaterUse');

            this._requiredFields[this.currentPaymentProduct.id] = requiredFields;
        },

        _restoreRequiredFieldsList: function() {
            if (!this.currentPaymentProduct) {
                return;
            }

            const requiredFields = this._requiredFields[this.currentPaymentProduct.id];
            _.each(this.currentPaymentProduct.paymentProductFields, field => {
                if (_.has(this._requiredFields[this.currentPaymentProduct.id], field.id)) {
                    field.dataRestrictions.isRequired = true;
                    field.dataRestrictions.validationRules = requiredFields[field.id];
                    this.currentPaymentProduct.paymentProductFieldById[field.id].isRequired = true;
                    this.currentPaymentProduct.paymentProductFieldById[field.id]
                        .validationRules = requiredFields[field.id];

                    this._showField(field.id);
                }
            });

            this._showField('saveForLaterUse');
            delete this._requiredFields[this.currentPaymentProduct.id];
        },

        _hideField: function(fieldName) {
            $('#' + this.buildFieldIdentifier(fieldName, 'field'))
                .parents(this.options.selectors.genericInputContainer)
                .addClass('hidden');
        },

        _showField: function(fieldName) {
            $('#' + this.buildFieldIdentifier(fieldName, 'field'))
                .parents(this.options.selectors.genericInputContainer)
                .removeClass('hidden');
        },

        /**
         * Validates fulfilled form data for currently selected payment product using Ingenico SDK.
         * For SEPA payment product Oro application's validation tools are used.
         */
        validate: function(fields) {
            if (!this.currentPaymentProduct) {
                mediator.execute('showFlashMessage', 'error', __('ingenico.no_selected_payment_product'));

                return false;
            }

            // SEPA payment product form data is validated with internal tools.
            if (this.isCurrentProductSEPA()) {
                return this.validateSEPAFields(fields);
            }

            const paymentRequest = this.session.getPaymentRequest();
            paymentRequest.setPaymentProduct(this.currentPaymentProduct);

            // cleaning up payment request values since single instance is in use
            const paymentRequestValues = paymentRequest.getValues();
            for (const fieldId in paymentRequestValues) {
                if ({}.hasOwnProperty.call(paymentRequestValues, fieldId)) {
                    delete paymentRequestValues[fieldId];
                }
            }

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

                // Payment request is stored inside the session.
                // session.getPaymentRequest() returns the same instance each time and it has all fields
                // even from the another payment products
                // We need to verify that this field is applicable for current payment product
                const field = paymentRequest.getPaymentProduct().paymentProductFieldById[paymentField.id];
                if (!field) {
                    return;
                }

                const fieldValue = paymentRequest.getValue(paymentField.id);
                const fieldErrorCodes = field.getErrorCodes(fieldValue);

                // The field is required, but it's value is empty
                // Catch this manually as connectsdk doesn't cover this case
                const isEmptyButRequired = field.dataRestrictions.isRequired && fieldValue === '';

                if (fieldErrorCodes.length || isEmptyButRequired) {

                    // Use "not blank" error message if applicable
                    // otherwise show first error message from the validation error list
                    const errorMapperName = 'ingenico::' +
                        (isEmptyButRequired ? 'notBlank' : fieldErrorCodes[0]) +
                        '_error';

                    let errorMessage;
                    try {
                        errorMessage = _.macros(errorMapperName)(field, fieldValue);
                    } catch (e) {
                        console.error(e);
                        errorMessage = '';
                    }

                    this.addError(paymentField.id, errorMessage);
                } else {
                    this.removeError(paymentField.id);
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
         * Formats edited field value with ingenico SDK formatter on keyup event
         */
        formatField: function(event) {
            if (!this.currentPaymentProduct) {
                // Do nothing in case we cannot format the field
                return;
            }

            const paymentProductFields = this.currentPaymentProduct.paymentProductFieldById;
            const fieldElement = $(event.currentTarget);
            const fieldElementId = fieldElement.data('field-id');

            if ({}.hasOwnProperty.call(paymentProductFields, fieldElementId)) {
                const field = paymentProductFields[fieldElementId];
                const maskedValue = field.applyMask(fieldElement.val());

                fieldElement.val(maskedValue.formattedValue);
            }
        },

        /**
         * Add error hint below field with validation message
         */
        addError: function(fieldId, message) {
            const fieldElementId = this.buildFieldIdentifier(fieldId, 'field');
            const fieldErrorElementId = this.buildFieldIdentifier(fieldId, 'error');

            this.removeError(fieldId);

            const errorMessage = message ? message : __('ingenico.general_error');
            const fieldErrorElementClass = this.buildFieldIdentifier(fieldId, 'error');
            const templateOptions = {
                fieldErrorElementId: fieldErrorElementId,
                fieldErrorElementClass: fieldErrorElementClass,
                errorMessage: errorMessage
            };

            // notice: error container should be <p> not <span> as it conflicts with jquery.validation
            $('#' + fieldElementId).after(this.errorHintTemplate(templateOptions));
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
                    let data = {
                        ingenicoCustomerEncDetails: encryptedString
                    };

                    if (this.isTokenizationApplicable()) {
                        const token = $(this.getTokenFieldSelector()).val();
                        const saveForLaterUse = $(this.getSaveForLaterSelector()).prop('checked');
                        data = _.extend(data, {
                            ingenicoSaveForLaterUse: saveForLaterUse,
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
        getSEPAFieldsInfo: function() {
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
                    id: 'debtorSurname',
                    _passThroughValue: this.options.paymentDetails.debtorSurname,
                    dataRestrictions: {
                        isRequired: true
                    },
                    displayHints: {
                        label: __('ingenico.sepa.fields.debtorSurname.label'),
                        placeholderLabel: __('ingenico.sepa.fields.debtorSurname.placeholder')
                    },
                    dataValidation: {
                        NotBlank: {
                            payload: null,
                            allowNull: false,
                            normalizer: null
                        },
                        Length: {
                            // Max length for debtorSurname field from the Ingenico API doc
                            max: 70
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
        validateSEPAFields: function(fields) {
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

        isCurrentProductSEPA: function() {
            return this.currentPaymentProduct.id === this.options.paymentProducts.sepaId;
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
            mediator.off('checkout:payment:before-transit', this.beforeTransit.bind(this));

            IngenicoPaymentComponent.__super__.dispose.call(this);
        }
    });

    return IngenicoPaymentComponent;
});
