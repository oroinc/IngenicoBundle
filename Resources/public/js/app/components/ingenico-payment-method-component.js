define(function(require) {
    'use strict';

    const _ = require('underscore');
    const mediator = require('oroui/js/mediator');
    const BaseComponent = require('oroui/js/app/components/base/component');

    const IngenicoPaymentMethodComponent = BaseComponent.extend({
        /**
         * @property {Object}
         */
        options: {
            paymentMethod: null
        },

        listen: {
            'checkout:place-order:response mediator': 'handleSubmit'
        },

        /**
         * @inheritDoc
         */
        constructor: function IngenicoPaymentMethodComponent(options) {
            IngenicoPaymentMethodComponent.__super__.constructor.call(this, options);
        },

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = _.defaults(options || {}, this.options);
        },

        /**
         * @param {Object} eventData
         */
        handleSubmit: function(eventData) {
            if (eventData.responseData.paymentMethod === this.options.paymentMethod) {
                eventData.stopped = true;

                const responseData = eventData.responseData;

                if (responseData.purchaseSuccessful) {
                    mediator.execute('redirectTo', {url: responseData.returnUrl}, {redirect: true});
                } else {
                    mediator.execute('redirectTo', {url: responseData.errorUrl}, {redirect: true});
                }
            }
        }
    });

    return IngenicoPaymentMethodComponent;
});

