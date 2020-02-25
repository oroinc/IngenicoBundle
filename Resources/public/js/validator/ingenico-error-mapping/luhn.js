define([
    'orotranslation/js/translator'
], function(__) {
    'use strict';

    return function(field) {
        const message = 'ingenico.errors_map.luhn.' + field.id;
        const translatedMessage = __(message);

        return translatedMessage !== message ? translatedMessage : '';
    };
});
