define(function(require) {
    'use strict';

    var _ = require('underscore');

    _.macros('ingenico', {
        // CreditCard fields
        cardNumber: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        expiryDate: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        cvv: require('tpl-loader!ingenico/templates/macros/input-default.html'),

        // DirectDebit fields
        city: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        firstName: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        accountHolderName: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        bankCode: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        accountNumber: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        surname: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        street: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        houseNumber: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        zip: require('tpl-loader!ingenico/templates/macros/input-default.html'),
    });
});
