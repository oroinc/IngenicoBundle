define(function(require) {
    'use strict';

    window.forge = require('node-forge');

    const _ = require('underscore');
    const __ = require('orotranslation/js/translator');
    const $ = require('jquery');
    const BaseComponent = require('oroui/js/app/components/base/component');
    const connectsdk = require('connect-sdk-client-js');
    const mediator = require('oroui/js/mediator');
    const routing = require('routing');
    const paymentProductListTemplate = require('tpl-loader!ingenico/templates/payment-products-list.html');

    const IngenicoCreditCardComponent = BaseComponent.extend({
        options: {
            paymentMethod: null,
            paymentDetails: {},
            createSessionRoute: 'ingenico.create-session'
        },

        session: null,
        paymentProductItems: [],
        currentPaymentProduct: null,
        paymentProductListTemplate: paymentProductListTemplate,

        /**
         * @property {jQuery}
         */
        $el: null,

        /**
         * @property {Boolean}
         */
        disposable: true,

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

            this.$el.on('click.' + this.cid, 'a.payment-product__item', this.renderPaymentProductFields.bind(this));

            mediator.on('checkout:payment:method:changed', this.onPaymentMethodChange, this);
            mediator.on('checkout-content:initialized', this.refreshPaymentMethod, this);
            mediator.on('checkout:payment:before-transit', this.beforeTransit, this);
            mediator.on('checkout:payment:before-restore-filled-form', this.beforeRestoreFilledForm, this);
            mediator.on('checkout:payment:before-hide-filled-form', this.beforeHideFilledForm, this);
            mediator.on('checkout:payment:remove-filled-form', this.removeFilledForm, this);
        },

        refreshPaymentMethod: function() {
            mediator.trigger('checkout:payment:method:changed', {paymentMethod: this.options.paymentMethod});
        },

        onPaymentMethodChange: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                this.getSession()
                    .then(this.getPaymentProducts.bind(this))
                    .then(this.renderPaymentProductsList.bind(this));
            }
        },

        getSession: function() {
            const deffer = $.Deferred();

            if (this.session) {
                deffer.resolve();
            } else {
                mediator.execute('showLoading');
                $.getJSON(
                    routing.generate(
                        this.options.createSessionRoute,
                        {paymentIdentifier: this.options.paymentMethod}
                    ),
                    function(data) {
                        if (data.success) {
                            this.session = new connectsdk(data.sessionInfo);
                            deffer.resolve();
                        } else {
                            mediator.execute('showFlashMessage', 'error', data.errorMessage);
                            deffer.reject();
                        }

                        mediator.execute('hideLoading');
                    }.bind(this)
                );
            }

            return deffer.promise();
        },

        getPaymentProducts: function() {
            const deffer = $.Deferred();

            if (this.paymentProductItems.length) {
                deffer.resolve();
            } else {
                mediator.execute('showLoading');
                this.session.getBasicPaymentItems(this.options.paymentDetails).then(
                    function(_basicPaymentItems) {
                        this.paymentProductItems = _basicPaymentItems.basicPaymentItems;
                        mediator.execute('hideLoading');
                        deffer.resolve();
                    }.bind(this),
                    function() {
                        mediator.execute('hideLoading');
                        mediator.execute('showFlashMessage', 'error', __('ingenico.api.error.get_payment_products'));
                        deffer.reject();
                    }
                );
            }

            return deffer.promise();
        },

        getPaymentProductDetails: function(paymentProductId) {
            const deffer = $.Deferred();

            if (this.isPaymentProductChanged(paymentProductId)) {
                this.session
                    .getPaymentProduct(paymentProductId, this.options.paymentDetails)
                    .then(
                        function(paymentProduct) {
                            this.currentPaymentProduct = paymentProduct;
                            mediator.execute('hideLoading');
                            deffer.resolve();
                        }.bind(this),
                        function() {
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

        renderPaymentProductsList: function() {
            const items = _.map(this.paymentProductItems, function(item) {
                return {
                    id: item.id,
                    label: item.displayHints.label,
                    logo: item.displayHints.logo
                };
            });

            return this.$el.html(this.paymentProductListTemplate({productPayments: items}));
        },

        renderPaymentProductFields: function(event) {
            event.preventDefault();
            const paymentProductId = $(event.currentTarget).data('product-id');

            this.getSession()
                .then(this.getPaymentProductDetails.bind(this, paymentProductId))
                .then(function() {
                    const fields = [];
                    _.each(this.currentPaymentProduct.paymentProductFields, function(field) {
                        const rendererFieldName = 'ingenico::' + field.id;
                        fields.push(_.macros(rendererFieldName)({
                            paymentMethod: this.options.paymentMethod,
                            field: field
                        }));
                    }.bind(this));

                    this.$el.html(fields.join(''));
                }.bind(this));
        },

        isPaymentProductChanged: function(paymentProductId) {
            if (!this.currentPaymentProduct) {
                return true;
            }

            if (this.currentPaymentProduct.id !== paymentProductId) {
                return true;
            }

            return false;
        },

        /**
         * @param {Object} eventData
         */
        beforeTransit: function(eventData) {
            if (eventData.data.paymentMethod === this.options.paymentMethod) {
                eventData.stopped = true;
                const fields = this.collectFormData();
                if (this.validate(fields)) {
                    this.storeEcryptedCutomerDetailes().then(function() {
                        eventData.resume();
                    });
                }
            }
        },

        buildFieldIdentifier: function(id, key) {
            return '.' + id + '-' + this.options.paymentMethod + '-' + key;
        },

        collectFormData: function() {
            const fields = [];
            _.each(this.currentPaymentProduct.paymentProductFields, function(field) {
                const fieldName = this.buildFieldIdentifier(field.id, 'field');
                if ($(fieldName).length) {
                    let value = $(fieldName).val();

                    // workaround for showing validation message
                    // because empty value does not treat as error by sdk validation
                    if (!value) {
                        value = '#';
                    }
                    fields.push({
                        field: field.id,
                        value: value
                    });
                }
            }.bind(this));

            return fields;
        },

        validate: function(fields) {
            if (!this.currentPaymentProduct) {
                mediator.execute('showFlashMessage', 'error', __('ingenico.no_choosen_payment_product'));

                return false;
            }

            const paymentRequest = this.session.getPaymentRequest();
            paymentRequest.setPaymentProduct(this.currentPaymentProduct);

            // field payment request with data
            _.each(fields, function(item) {
                paymentRequest.setValue(item.field, item.value);
            });

            if (!paymentRequest.isValid()) {
                _.each(paymentRequest.getPaymentProduct().paymentProductFields, function(field) {
                    const fieldName = this.buildFieldIdentifier(field.id, 'error');
                    if ($(fieldName).length) {
                        if (field.getErrorCodes().length) {
                            $(fieldName).removeClass('hidden');
                        } else {
                            $(fieldName).addClass('hidden');
                        }
                    }
                }.bind(this));

                return false;
            }

            return true;
        },


        /**
         * Crypts selected payment product's form values and storing it to DOM storage. INGA-29 basic implementation
         */
        storeEcryptedCutomerDetailes: function() {
            const deffer = $.Deferred();

            const encryptor = this.session.getEncryptor();
            const paymentRequest = this.session.getPaymentRequest();
            encryptor.encrypt(paymentRequest).then(
                function(encryptedString) {
                    this.addPaymentAdditionalData({
                        ingenicoPaymentProduct: paymentRequest.getPaymentProduct().paymentProductGroup,
                        ingenicoCustomerEncDetails: encryptedString
                    });
                    deffer.resolve();
                }.bind(this),
                function() {
                    deffer.reject();
                    mediator.execute('showFlashMessage', 'error', __('ingenico.crypt_error'));
                }
            );

            return deffer.promise();
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

            mediator.off('checkout:payment:method:changed', this.onPaymentMethodChange, this);
            mediator.off('checkout-content:initialized', this.refreshPaymentMethod, this);
            mediator.off('checkout:payment:before-transit', this.beforeTransit, this);
            mediator.off('checkout:payment:before-restore-filled-form', this.beforeRestoreFilledForm, this);
            mediator.off('checkout:payment:before-hide-filled-form', this.beforeHideFilledForm, this);
            mediator.off('checkout:payment:remove-filled-form', this.removeFilledForm, this);

            this.$el.off('click' + this.cid, '.payment-product__item');

            IngenicoCreditCardComponent.__super__.dispose.call(this);
        }
    });

    return IngenicoCreditCardComponent;
});
