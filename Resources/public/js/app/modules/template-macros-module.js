define(function(require) {
    'use strict';

    const _ = require('underscore');

    _.macros('ingenico', {
        // CreditCard fields
        cardNumber: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        expiryDate: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        cvv: require('tpl-loader!ingenico/templates/macros/input-password.html'),
        saveForLaterUse: require('tpl-loader!ingenico/templates/macros/input-checkbox.html'),
        token: require('tpl-loader!ingenico/templates/macros/input-select.html'),

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

        // SEPA fields
        iban: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        debtorSurname: require('tpl-loader!ingenico/templates/macros/input-default.html'),
        mandateDisclaimer: require('tpl-loader!ingenico/templates/macros/disclaimer-text.html'),

        hidden: require('tpl-loader!ingenico/templates/macros/input-hidden.html'),

        // Ingenico fields error messages mapping
        length_error: require('ingenico/js/validator/ingenico-error-mapping/length'),
        expirationDate_error: require('ingenico/js/validator/ingenico-error-mapping/expiration-date'),
        luhn_error: require('ingenico/js/validator/ingenico-error-mapping/luhn'),
        notBlank_error: require('ingenico/js/validator/ingenico-error-mapping/not-blank'),
        regularExpression_error: require('ingenico/js/validator/ingenico-error-mapping/regular-expression')
    });
});
