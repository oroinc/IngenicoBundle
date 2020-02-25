define([
    'underscore',
    'oroform/js/validator/number'
], function(_, numberValidator) {
    'use strict';

    const defaultParam = {
        exactMessage: 'This value should have exactly {{ limit }} character.|' +
            'This value should have exactly {{ limit }} characters.',
        maxMessage: 'This value is too long. It should have {{ limit }} character or less.|' +
            'This value is too long. It should have {{ limit }} characters or less.',
        minMessage: 'This value is too short. It should have {{ limit }} character or more.|' +
            'This value is too short. It should have {{ limit }} characters or more.'
    };

    return function(field, value) {
        const validationRule = field.dataRestrictions.validationRuleByType.length;
        const param = _.extend(
            {},
            defaultParam,
            {min: validationRule.minLength, max: validationRule.maxLength}
        );

        return numberValidator[2](param, null, value.length);
    };
});
