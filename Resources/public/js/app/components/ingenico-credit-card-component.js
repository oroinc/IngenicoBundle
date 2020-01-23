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

        sessionInfo: null,
        paymentProductsBaseInfo: null,

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

            mediator.on('checkout:payment:method:changed', this.onPaymentMethodChange, this);
            mediator.on('checkout-content:initialized', this.refreshPaymentMethod, this);
        },

        refreshPaymentMethod: function() {
            mediator.trigger('checkout:payment:method:refresh');
        },

        onPaymentMethodChange: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                this.createClientSession();
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
                        this.sessionInfo = data.sessionInfo;
                    } else {
                        mediator.execute('showFlashMessage', 'error', data.errorMessage);
                    }
                    mediator.execute('hideLoading');

                    // debug payment products data retrieval. Review after INGA-26 is implemented
                    this.requestPaymentProductsConfigurations();
                }.bind(this)
            );
        },

        requestPaymentProductsConfigurations: function() {
            if (!this.sessionInfo) {
                return;
            }

            // debug proposal data to check session can serve properly. Review after INGA-26 is implemented
            const paymentDetails = {
                totalAmount: 1000,
                currency: 'USD',
                countryCode: 'US',
                isRecurring: true,
                locale: 'en_US'
            };

            const session = new connectsdk(this.sessionInfo);
            session.getBasicPaymentItems(paymentDetails).then(function(basicPaymentItems) {
                this.paymentProductsBaseInfo = basicPaymentItems;
                console.log(this.paymentProductsBaseInfo);
            }.bind(this), function() {
                mediator.execute('showFlashMessage', 'error', 'Ingenico payment products retrival failed');
            });
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
