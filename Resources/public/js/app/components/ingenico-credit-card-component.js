define(function(require) {
    'use strict';

    window.forge = require('node-forge');

    const _ = require('underscore');
    const $ = require('jquery');
    const BaseComponent = require('oroui/js/app/components/base/component');
    const connectsdk = require('connect-sdk-client-js');
    const mediator = require('oroui/js/mediator');
    const routing = require('routing');

    const IngenicoCreditCardComponent = BaseComponent.extend({
        options: {
            paymentMethod: null,
            createSessionRoute: 'ingenico.create-session'
        },

        sessionPromise: null,
        paymentProductPromise: null,

        session: null,
        paymentProductsInfo: null,

        /**
         * @property {jQuery}
         */
        $el: null,

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

            var fieldName = 'bankCode';
            var rendererFieldName = 'ingenico::' + fieldName;
            this.$el.html(_.macros(rendererFieldName)({
                paymentMethod: this.options.paymentMethod,
                field: {
                    id: 'bankCode',
                    displayHints: {
                        label: 'Simple field',
                        placeholderLabel: 'Placeholder text'
                    }
                }
            }));

            this.sessionPromise = $.Deferred();
            this.paymentProductPromise = $.Deferred();
            this.sessionPromise.promise();
            this.paymentProductPromise.promise();

            mediator.on('checkout:payment:method:changed', this.onPaymentMethodChange, this);
            mediator.on('checkout-content:initialized', this.refreshPaymentMethod, this);
        },

        refreshPaymentMethod: function() {
            mediator.trigger('checkout:payment:method:refresh');
        },

        onPaymentMethodChange: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                this.createClientSession();

                // debug proposes only. Review after INGA-29 is implemented
                this.requestPaymentProductsConfigurations();
                // debug proposes only. Review after INGA-29 is implemented
                this.storeEcryptedCutomerDetailes();
            }
        },

        createClientSession: function() {
            if (this.sessionInfo) {
                return;
            }

            mediator.execute('showLoading');
            $.getJSON(
                routing.generate(
                    this.options.createSessionRoute,
                    {paymentIdentifier: this.options.paymentMethod}
                ),
                function(data) {
                    if (data.success) {
                        this.session = new connectsdk(data.sessionInfo);
                        // skipping data to be passed to promise subscribers
                        this.sessionPromise.resolve();
                    } else {
                        this.sessionPromise.reject();
                        // skipping data to be passed to promise subscribers
                        mediator.execute('showFlashMessage', 'error', data.errorMessage);
                    }

                    mediator.execute('hideLoading');
                }.bind(this)
            );
        },

        requestPaymentProductsConfigurations: function() {
            if (this.paymentProductsInfo) {
                return;
            }

            $.when(this.sessionPromise).then(function() {
                // debug proposal data to check session can serve properly.
                // Review after INGA-26, INGA-29 are implemented
                const paymentDetails = {
                    totalAmount: 1000,
                    currency: 'USD',
                    countryCode: 'US',
                    isRecurring: true,
                    locale: 'en_US'
                };

                this.session.getBasicPaymentItems(paymentDetails).then(function(_basicPaymentItems) {
                    const basicPaymentItems = _basicPaymentItems.basicPaymentItems;

                    // debug console output. Review after INGA-29 is implemented
                    this.paymentProductsInfo = [];
                    for (let i = 0; i < basicPaymentItems.length; i++) {
                        this.session.getPaymentProduct(basicPaymentItems[i].id, paymentDetails).then(
                            function(paymentProduct) {
                                this.paymentProductsInfo.push(paymentProduct);
                                if (this.paymentProductsInfo.length >= basicPaymentItems.length) {
                                    this.paymentProductPromise.resolve();
                                }
                            }.bind(this),
                            function() {
                                this.paymentProductPromise.reject();
                            }
                         );
                    }
                }.bind(this), function() {
                    // skipping data to be passed to promise subscribers
                    this.paymentProductPromise.reject();
                    mediator.execute('showFlashMessage', 'error', 'Ingenico payment products retrival failed');
                });
            }.bind(this));
        },

        /**
         * Crypts selected payment product's form values and storing it to DOM storage. INGA-29 basic implementation
         */
        storeEcryptedCutomerDetailes: function() {
            $.when(this.paymentProductPromise).then(function() {
                const paymentDetails = this.getSelectedPaymentProductDetails();
                if (!paymentDetails.paymentRequest.isValid()) {
                    mediator.execute('showFlashMessage', 'error', 'Payment form data validation failed');
                    return;
                }

                const encryptor = this.session.getEncryptor();
                encryptor.encrypt(paymentDetails.paymentRequest).then(function(encryptedString) {
                    this.addPaymentAdditionalData({
                        ingenicoPaymentProduct: paymentDetails.paymentProductAlias,
                        ingenicoCustomerEncDetails: encryptedString
                    });
                }.bind(this), function(errors) {
                    mediator.execute('showFlashMessage', 'error', 'Failed to crypt payment form data');
                }.bind(this));
            }.bind(this));
        },

        /**
         * Gets selected payment product's details(form data and payment group alias). Hardcoded values for INGA-29
         */
        getSelectedPaymentProductDetails: function() {
            const paymentProduct = this.paymentProductsInfo.find(function(element) {
                if (element.id === 3) {
                    return true;
                }

                return false;
            });

            const paymentRequest = this.session.getPaymentRequest();

            paymentRequest.setPaymentProduct(paymentProduct);
            paymentRequest.setValue('cardNumber', '5424180279791732');
            paymentRequest.setValue('expiryDate', '11/20');
            paymentRequest.setValue('cvv', '321');

            return {
                paymentProductAlias: 'cards',
                paymentRequest: paymentRequest
            };
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

        dispose: function() {
            if (this.disposed) {
                return;
            }

            mediator.off('checkout:payment:method:changed', this.onPaymentMethodChange, this);
            mediator.off('checkout-content:initialized', this.refreshPaymentMethod, this);

            IngenicoCreditCardComponent.__super__.dispose.call(this);
        }
    });

    return IngenicoCreditCardComponent;
});
