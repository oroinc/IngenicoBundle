define(function(require) {
    'use strict';

    const _ = require('underscore');

    _.macros('ingenico', {
        // This list contains specific fields and their templates
        // If the field is not mentioned here default-input template will be used as fallback

        // CreditCard fields
        cvv: require('tpl-loader!ingenico/templates/macros/input-password.html'),
        saveForLaterUse: require('tpl-loader!ingenico/templates/macros/input-checkbox.html'),
        token: require('tpl-loader!ingenico/templates/macros/input-select.html'),

        // SEPA fields
        mandateDisclaimer: require('tpl-loader!ingenico/templates/macros/disclaimer-text.html'),

        // Special fields
        hidden: require('tpl-loader!ingenico/templates/macros/input-hidden.html'),

        // Default field which will be used in case there are no any specific fields by name
        default: require('tpl-loader!ingenico/templates/macros/input-default.html'),

        // Ingenico fields error messages mapping
        length_error: require('ingenico/js/validator/ingenico-error-mapping/length'),
        expirationDate_error: require('ingenico/js/validator/ingenico-error-mapping/expiration-date'),
        luhn_error: require('ingenico/js/validator/ingenico-error-mapping/luhn'),
        notBlank_error: require('ingenico/js/validator/ingenico-error-mapping/not-blank'),
        regularExpression_error: require('ingenico/js/validator/ingenico-error-mapping/regular-expression')
    });
});
