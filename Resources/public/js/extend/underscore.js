define(function(require) {
    const _ = require('underscore');
    require('oroui/js/extend/underscore');
    _.mixin({
        /**
         * Registers macros with Name Space, e.g.
         *     _.macros('oroui', {
         *         renderPhone: require('tpl!oroui/templates/macros/phone.html')
         *     });
         *
         * Imports macros object from Name Space, accessible inside templates
         *     <% var ui = _.macros('oroui'); %>
         *     <% ui.renderPhone({phone: '+012345556789', title: '+01 (234) 555-67-89'}) %>
         *
         * Also it is possible to import single macro
         *     <% _.macros('oroui::renderPhone')({phone: '+012345556789', title: '+01 (234) 555-67-89'}) %>
         *
         * @param NS {string} Name Space or full macro name "{{NS}}::{{macroName}}"
         * @param templates {Object<string, function>?}
         * @return {Object<string, function(Object): string>|function(Object): string|undefined}
         */
        macros: _.extend(function macros(NS, templates) {
            let matches;
            let result;
            if (arguments.length === 2) {
                // setter
                _.macros.registry[NS] = templates;
            } else {
                // getter
                result = _.macros.registry[NS];
                if (!result && (matches = NS.match(/^(\w+)::(\w+)$/))) {
                    result = _.macros.registry[matches[1]][matches[2]];
                }
                if (!result) {
                    throw new Error('NS or macro "' + NS + '" is not found');
                }
                return result;
            }
        }, {registry: {}})
    });
    return _;
});
