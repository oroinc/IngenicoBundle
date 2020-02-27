/* eslint-disable max-len */
const ConnectSdkMock = function(sessionInfo) {
    this.sessionInfo = sessionInfo;
    this.paymentRequest = new PaymentRequestMock();
    this.encryptor = new EncryptorMock();

    this.getBasicPaymentItems = function() {
        return new Promise(function(resolve, reject) {
            const isSessionEquals = isEquivalent({
                assetUrl: 'https:\/\/payment.pay1.preprod.secured-by-ingenico.com\/',
                clientApiUrl: 'https:\/\/ams1.preprod.api-ingenico.com\/client',
                clientSessionId: 'f6d8aeab4abb40000018a81558751ad2',
                customerId: '00000-000000bf027e4391ad2999946b0dd7e9',
                region: 'EU'
            }, this.sessionInfo);
            if (!isSessionEquals) {
                reject(new Error('sessionInfo didn\'t match expected data'));
            } else {
                resolve({
                    basicPaymentItems: [
                        {
                            json: {
                                deviceFingerprintEnabled: false,
                                allowsRecurring: false,
                                allowsTokenization: true,
                                autoTokenized: false,
                                displayHints: {
                                    displayOrder: 1,
                                    label: 'SEPA direct debit',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_770_v1.png'
                                },
                                id: 770,
                                maxAmount: 11135390,
                                mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                                paymentMethod: 'directDebit',
                                usesRedirectionTo3rdParty: false,
                                type: 'product'
                            },
                            accountsOnFile: [],
                            accountOnFileById: {},
                            allowsRecurring: false,
                            allowsTokenization: true,
                            autoTokenized: false,
                            displayHints: {
                                json: {
                                    displayOrder: 1,
                                    label: 'SEPA direct debit',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_770_v1.png'
                                },
                                displayOrder: 1,
                                label: 'SEPA direct debit',
                                logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_770_v1.png'
                            },
                            id: 770,
                            maxAmount: 11135390,
                            paymentMethod: 'directDebit',
                            mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                            usesRedirectionTo3rdParty: false
                        },
                        {
                            json: {
                                deviceFingerprintEnabled: false,
                                allowsRecurring: false,
                                allowsTokenization: true,
                                authenticationIndicator: {
                                    name: 'AUTHENTICATIONINDICATOR',
                                    value: '0'
                                },
                                autoTokenized: false,
                                displayHints: {
                                    displayOrder: 2,
                                    label: 'Visa',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                                },
                                id: 1,
                                maxAmount: 11135390,
                                mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                                paymentMethod: 'card',
                                paymentProductGroup: 'cards',
                                usesRedirectionTo3rdParty: false,
                                type: 'product'
                            },
                            accountsOnFile: [],
                            accountOnFileById: {},
                            allowsRecurring: false,
                            allowsTokenization: true,
                            autoTokenized: false,
                            displayHints: {
                                json: {
                                    displayOrder: 2,
                                    label: 'Visa',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                                },
                                displayOrder: 2,
                                label: 'Visa',
                                logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                            },
                            id: 1,
                            maxAmount: 11135390,
                            paymentMethod: 'card',
                            mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                            usesRedirectionTo3rdParty: false,
                            paymentProductGroup: 'cards'
                        },
                        {
                            json: {
                                deviceFingerprintEnabled: false,
                                allowsRecurring: false,
                                allowsTokenization: true,
                                authenticationIndicator: {
                                    name: 'AUTHENTICATIONINDICATOR',
                                    value: '0'
                                },
                                autoTokenized: false,
                                displayHints: {
                                    displayOrder: 3,
                                    label: 'American Express',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_2_v2.png'
                                },
                                id: 2,
                                maxAmount: 11135390,
                                mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                                paymentMethod: 'card',
                                paymentProductGroup: 'cards',
                                usesRedirectionTo3rdParty: false,
                                type: 'product'
                            },
                            accountsOnFile: [],
                            accountOnFileById: {},
                            allowsRecurring: false,
                            allowsTokenization: true,
                            autoTokenized: false,
                            displayHints: {
                                json: {
                                    displayOrder: 3,
                                    label: 'American Express',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_2_v2.png'
                                },
                                displayOrder: 3,
                                label: 'American Express',
                                logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_2_v2.png'
                            },
                            id: 2,
                            maxAmount: 11135390,
                            paymentMethod: 'card',
                            mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                            usesRedirectionTo3rdParty: false,
                            paymentProductGroup: 'cards'
                        },
                        {
                            json: {
                                deviceFingerprintEnabled: false,
                                allowsRecurring: false,
                                allowsTokenization: true,
                                authenticationIndicator: {
                                    name: 'AUTHENTICATIONINDICATOR',
                                    value: '0'
                                },
                                autoTokenized: false,
                                displayHints: {
                                    displayOrder: 4,
                                    label: 'MasterCard',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_3_v3.png'
                                },
                                id: 3,
                                maxAmount: 11135390,
                                mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                                paymentMethod: 'card',
                                paymentProductGroup: 'cards',
                                usesRedirectionTo3rdParty: false,
                                type: 'product'
                            },
                            accountsOnFile: [],
                            accountOnFileById: {},
                            allowsRecurring: false,
                            allowsTokenization: true,
                            autoTokenized: false,
                            displayHints: {
                                json: {
                                    displayOrder: 4,
                                    label: 'MasterCard',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_3_v3.png'
                                },
                                displayOrder: 4,
                                label: 'MasterCard',
                                logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_3_v3.png'
                            },
                            id: 3,
                            maxAmount: 11135390,
                            paymentMethod: 'card',
                            mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                            usesRedirectionTo3rdParty: false,
                            paymentProductGroup: 'cards'
                        },
                        {
                            json: {
                                deviceFingerprintEnabled: false,
                                allowsRecurring: false,
                                allowsTokenization: true,
                                autoTokenized: false,
                                displayHints: {
                                    displayOrder: 5,
                                    label: 'ACH',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_730_v1.png'
                                },
                                id: 730,
                                maxAmount: 11135390,
                                mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                                paymentMethod: 'directDebit',
                                usesRedirectionTo3rdParty: false,
                                type: 'product'
                            },
                            accountsOnFile: [],
                            accountOnFileById: {},
                            allowsRecurring: false,
                            allowsTokenization: true,
                            autoTokenized: false,
                            displayHints: {
                                json: {
                                    displayOrder: 5,
                                    label: 'ACH',
                                    logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_730_v1.png'
                                },
                                displayOrder: 5,
                                label: 'ACH',
                                logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_730_v1.png'
                            },
                            id: 730,
                            maxAmount: 11135390,
                            paymentMethod: 'directDebit',
                            mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                            usesRedirectionTo3rdParty: false
                        }
                    ]
                });
            }
        }.bind(this));
    };

    this.getPaymentProduct = function(paymentProductId) {
        return new Promise(function(resolve, reject) {
            const data = {
                770: {
                    json: {
                        deviceFingerprintEnabled: false,
                        allowsRecurring: false,
                        allowsTokenization: true,
                        autoTokenized: false,
                        displayHints: {
                            displayOrder: 1,
                            label: 'SEPA direct debit',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_770_v1.png'
                        },
                        fields: [],
                        id: 770,
                        maxAmount: 11135390,
                        mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                        paymentMethod: 'directDebit',
                        usesRedirectionTo3rdParty: false,
                        type: 'product'
                    },
                    accountsOnFile: [],
                    accountOnFileById: {},
                    allowsRecurring: false,
                    allowsTokenization: true,
                    autoTokenized: false,
                    displayHints: {
                        json: {
                            displayOrder: 1,
                            label: 'SEPA direct debit',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_770_v1.png'
                        },
                        displayOrder: 1,
                        label: 'SEPA direct debit',
                        logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_770_v1.png'
                    },
                    id: 770,
                    maxAmount: 11135390,
                    paymentMethod: 'directDebit',
                    mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                    usesRedirectionTo3rdParty: false,
                    paymentProductFields: [],
                    paymentProductFieldById: {}
                },
                1: {
                    json: {
                        deviceFingerprintEnabled: false,
                        allowsRecurring: false,
                        allowsTokenization: true,
                        authenticationIndicator: {
                            name: 'AUTHENTICATIONINDICATOR',
                            value: '0'
                        },
                        autoTokenized: false,
                        displayHints: {
                            displayOrder: 2,
                            label: 'Visa',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                        },
                        fields: [
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'cardNumber',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'luhn',
                                    'regularExpression'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'expiryDate',
                                type: 'tel',
                                validators: [
                                    'expirationDate',
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVV',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                        label: 'Please enter your CVV code as shown in the image'
                                    }
                                },
                                id: 'cvv',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            }
                        ],
                        id: 1,
                        maxAmount: 11135390,
                        mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                        paymentMethod: 'card',
                        paymentProductGroup: 'cards',
                        usesRedirectionTo3rdParty: false,
                        type: 'product'
                    },
                    accountsOnFile: [],
                    accountOnFileById: {},
                    allowsRecurring: false,
                    allowsTokenization: true,
                    autoTokenized: false,
                    displayHints: {
                        json: {
                            displayOrder: 2,
                            label: 'Visa',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                        },
                        displayOrder: 2,
                        label: 'Visa',
                        logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                    },
                    id: 1,
                    maxAmount: 11135390,
                    paymentMethod: 'card',
                    mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                    usesRedirectionTo3rdParty: false,
                    paymentProductGroup: 'cards',
                    // This array will be autofilled by data from paymentProductFieldById
                    paymentProductFields: [],
                    paymentProductFieldById: {
                        cardNumber: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'cardNumber',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'luhn',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                displayOrder: 10,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Card number',
                                mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                obfuscate: false,
                                placeholderLabel: '**** **** **** ****',
                                preferredInputType: 'IntegerKeyboard',
                                alwaysShow: false,
                                wildcardMask: '{{****}} {{****}} {{****}} {{****}} {{***}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 19,
                                                minLength: 12
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 19,
                                        minLength: 12
                                    },
                                    {
                                        json: {
                                            type: 'luhn',
                                            attributes: {}
                                        },
                                        type: 'luhn',
                                        errorMessageId: 'luhn'
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{12,19}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{12,19}$'
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 19,
                                                minLength: 12
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 19,
                                        minLength: 12
                                    },
                                    luhn: {
                                        json: {
                                            type: 'luhn',
                                            attributes: {}
                                        },
                                        type: 'luhn',
                                        errorMessageId: 'luhn'
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{12,19}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{12,19}$'
                                    }
                                }
                            },
                            id: 'cardNumber',
                            type: 'tel'
                        },
                        expiryDate: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'expiryDate',
                                type: 'tel',
                                validators: [
                                    'expirationDate',
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                displayOrder: 20,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Expiry date',
                                mask: '{{99}}/{{99}}',
                                obfuscate: false,
                                placeholderLabel: 'MM/YY',
                                preferredInputType: 'IntegerKeyboard',
                                alwaysShow: false,
                                wildcardMask: '{{**}}/{{**}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'expirationDate',
                                            attributes: {}
                                        },
                                        type: 'expirationDate',
                                        errorMessageId: 'expirationDate'
                                    },
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 4
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 4
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                    }
                                ],
                                validationRuleByType: {
                                    expirationDate: {
                                        json: {
                                            type: 'expirationDate',
                                            attributes: {}
                                        },
                                        type: 'expirationDate',
                                        errorMessageId: 'expirationDate'
                                    },
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 4
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 4
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                    }
                                }
                            },
                            id: 'expiryDate',
                            type: 'tel'
                        },
                        cvv: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVV',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                        label: 'Please enter your CVV code as shown in the image'
                                    }
                                },
                                id: 'cvv',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVV',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                        label: 'Please enter your CVV code as shown in the image'
                                    }
                                },
                                displayOrder: 24,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'CVV',
                                mask: '{{9999}}',
                                obfuscate: false,
                                placeholderLabel: '123',
                                preferredInputType: 'IntegerKeyboard',
                                tooltip: {
                                    json: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                        label: 'Please enter your CVV code as shown in the image'
                                    },
                                    image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                    label: 'Please enter your CVV code as shown in the image'
                                },
                                alwaysShow: true,
                                wildcardMask: '{{****}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 3
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 3
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{3,4}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{3,4}$'
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 3
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 3
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{3,4}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{3,4}$'
                                    }
                                }
                            },
                            id: 'cvv',
                            type: 'tel'
                        }
                    }
                },
                2: {
                    json: {
                        deviceFingerprintEnabled: false,
                        allowsRecurring: false,
                        allowsTokenization: true,
                        authenticationIndicator: {
                            name: 'AUTHENTICATIONINDICATOR',
                            value: '0'
                        },
                        autoTokenized: false,
                        displayHints: {
                            displayOrder: 2,
                            label: 'Visa',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                        },
                        fields: [
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'cardNumber',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'luhn',
                                    'regularExpression'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'expiryDate',
                                type: 'tel',
                                validators: [
                                    'expirationDate',
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVV',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                        label: 'Please enter your CVV code as shown in the image'
                                    }
                                },
                                id: 'cvv',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            }
                        ],
                        id: 1,
                        maxAmount: 11135390,
                        mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                        paymentMethod: 'card',
                        paymentProductGroup: 'cards',
                        usesRedirectionTo3rdParty: false,
                        type: 'product'
                    },
                    accountsOnFile: [],
                    accountOnFileById: {},
                    allowsRecurring: false,
                    allowsTokenization: true,
                    autoTokenized: false,
                    displayHints: {
                        json: {
                            displayOrder: 2,
                            label: 'Visa',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                        },
                        displayOrder: 2,
                        label: 'Visa',
                        logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_1_v2.png'
                    },
                    id: 1,
                    maxAmount: 11135390,
                    paymentMethod: 'card',
                    mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                    usesRedirectionTo3rdParty: false,
                    paymentProductGroup: 'cards',
                    // This array will be autofilled by data from paymentProductFieldById
                    paymentProductFields: [],
                    paymentProductFieldById: {
                        cardNumber: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'cardNumber',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'luhn',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                displayOrder: 10,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Card number',
                                mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                obfuscate: false,
                                placeholderLabel: '**** **** **** ****',
                                preferredInputType: 'IntegerKeyboard',
                                alwaysShow: false,
                                wildcardMask: '{{****}} {{****}} {{****}} {{****}} {{***}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 19,
                                                minLength: 12
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 19,
                                        minLength: 12
                                    },
                                    {
                                        json: {
                                            type: 'luhn',
                                            attributes: {}
                                        },
                                        type: 'luhn',
                                        errorMessageId: 'luhn'
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{12,19}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{12,19}$'
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 19,
                                                minLength: 12
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 19,
                                        minLength: 12
                                    },
                                    luhn: {
                                        json: {
                                            type: 'luhn',
                                            attributes: {}
                                        },
                                        type: 'luhn',
                                        errorMessageId: 'luhn'
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{12,19}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{12,19}$'
                                    }
                                }
                            },
                            id: 'cardNumber',
                            type: 'tel'
                        },
                        expiryDate: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'expiryDate',
                                type: 'tel',
                                validators: [
                                    'expirationDate',
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                displayOrder: 20,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Expiry date',
                                mask: '{{99}}/{{99}}',
                                obfuscate: false,
                                placeholderLabel: 'MM/YY',
                                preferredInputType: 'IntegerKeyboard',
                                alwaysShow: false,
                                wildcardMask: '{{**}}/{{**}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'expirationDate',
                                            attributes: {}
                                        },
                                        type: 'expirationDate',
                                        errorMessageId: 'expirationDate'
                                    },
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 4
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 4
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                    }
                                ],
                                validationRuleByType: {
                                    expirationDate: {
                                        json: {
                                            type: 'expirationDate',
                                            attributes: {}
                                        },
                                        type: 'expirationDate',
                                        errorMessageId: 'expirationDate'
                                    },
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 4
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 4
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                    }
                                }
                            },
                            id: 'expiryDate',
                            type: 'tel'
                        },
                        cvv: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVV',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                        label: 'Please enter your CVV code as shown in the image'
                                    }
                                },
                                id: 'cvv',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVV',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                        label: 'Please enter your CVV code as shown in the image'
                                    }
                                },
                                displayOrder: 24,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'CVV',
                                mask: '{{9999}}',
                                obfuscate: false,
                                placeholderLabel: '123',
                                preferredInputType: 'IntegerKeyboard',
                                tooltip: {
                                    json: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                        label: 'Please enter your CVV code as shown in the image'
                                    },
                                    image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_v1.png',
                                    label: 'Please enter your CVV code as shown in the image'
                                },
                                alwaysShow: true,
                                wildcardMask: '{{****}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 3
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 3
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{3,4}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{3,4}$'
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 3
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 3
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{3,4}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{3,4}$'
                                    }
                                }
                            },
                            id: 'cvv',
                            type: 'tel'
                        }
                    }
                },
                3: {
                    json: {
                        deviceFingerprintEnabled: false,
                        allowsRecurring: false,
                        allowsTokenization: true,
                        authenticationIndicator: {
                            name: 'AUTHENTICATIONINDICATOR',
                            value: '0'
                        },
                        autoTokenized: false,
                        displayHints: {
                            displayOrder: 4,
                            label: 'MasterCard',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_3_v3.png'
                        },
                        fields: [
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'cardNumber',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'luhn',
                                    'regularExpression'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'expiryDate',
                                type: 'tel',
                                validators: [
                                    'expirationDate',
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVC2',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_3_v2.png',
                                        label: 'Please enter your CVC2 code as shown in the image'
                                    }
                                },
                                id: 'cvv',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            }
                        ],
                        id: 3,
                        maxAmount: 11135390,
                        mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                        paymentMethod: 'card',
                        paymentProductGroup: 'cards',
                        usesRedirectionTo3rdParty: false,
                        type: 'product'
                    },
                    accountsOnFile: [],
                    accountOnFileById: {},
                    allowsRecurring: false,
                    allowsTokenization: true,
                    autoTokenized: false,
                    displayHints: {
                        json: {
                            displayOrder: 4,
                            label: 'MasterCard',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_3_v3.png'
                        },
                        displayOrder: 4,
                        label: 'MasterCard',
                        logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_3_v3.png'
                    },
                    id: 3,
                    maxAmount: 11135390,
                    paymentMethod: 'card',
                    mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                    usesRedirectionTo3rdParty: false,
                    paymentProductGroup: 'cards',
                    // This array will be autofilled by data from paymentProductFieldById
                    paymentProductFields: [],
                    paymentProductFieldById: {
                        cardNumber: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'cardNumber',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'luhn',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Card number',
                                    mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                    obfuscate: false,
                                    placeholderLabel: '**** **** **** ****',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                displayOrder: 10,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Card number',
                                mask: '{{9999}} {{9999}} {{9999}} {{9999}} {{999}}',
                                obfuscate: false,
                                placeholderLabel: '**** **** **** ****',
                                preferredInputType: 'IntegerKeyboard',
                                alwaysShow: false,
                                wildcardMask: '{{****}} {{****}} {{****}} {{****}} {{***}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 19,
                                            minLength: 12
                                        },
                                        luhn: {},
                                        regularExpression: {
                                            regularExpression: '^[0-9]{12,19}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 19,
                                                minLength: 12
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 19,
                                        minLength: 12
                                    },
                                    {
                                        json: {
                                            type: 'luhn',
                                            attributes: {}
                                        },
                                        type: 'luhn',
                                        errorMessageId: 'luhn'
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{12,19}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{12,19}$'
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 19,
                                                minLength: 12
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 19,
                                        minLength: 12
                                    },
                                    luhn: {
                                        json: {
                                            type: 'luhn',
                                            attributes: {}
                                        },
                                        type: 'luhn',
                                        errorMessageId: 'luhn'
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{12,19}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{12,19}$'
                                    }
                                }
                            },
                            id: 'cardNumber',
                            type: 'tel'
                        },
                        expiryDate: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'expiryDate',
                                type: 'tel',
                                validators: [
                                    'expirationDate',
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 20,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Expiry date',
                                    mask: '{{99}}/{{99}}',
                                    obfuscate: false,
                                    placeholderLabel: 'MM/YY',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                displayOrder: 20,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Expiry date',
                                mask: '{{99}}/{{99}}',
                                obfuscate: false,
                                placeholderLabel: 'MM/YY',
                                preferredInputType: 'IntegerKeyboard',
                                alwaysShow: false,
                                wildcardMask: '{{**}}/{{**}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        expirationDate: {},
                                        length: {
                                            maxLength: 4,
                                            minLength: 4
                                        },
                                        regularExpression: {
                                            regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'expirationDate',
                                            attributes: {}
                                        },
                                        type: 'expirationDate',
                                        errorMessageId: 'expirationDate'
                                    },
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 4
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 4
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                    }
                                ],
                                validationRuleByType: {
                                    expirationDate: {
                                        json: {
                                            type: 'expirationDate',
                                            attributes: {}
                                        },
                                        type: 'expirationDate',
                                        errorMessageId: 'expirationDate'
                                    },
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 4
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 4
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^(?:0[1-9]|1[0-2])[0-9]{2}$'
                                    }
                                }
                            },
                            id: 'expiryDate',
                            type: 'tel'
                        },
                        cvv: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVC2',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_3_v2.png',
                                        label: 'Please enter your CVC2 code as shown in the image'
                                    }
                                },
                                id: 'cvv',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: true,
                                    displayOrder: 24,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'CVC2',
                                    mask: '{{9999}}',
                                    obfuscate: false,
                                    placeholderLabel: '123',
                                    preferredInputType: 'IntegerKeyboard',
                                    tooltip: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_3_v2.png',
                                        label: 'Please enter your CVC2 code as shown in the image'
                                    }
                                },
                                displayOrder: 24,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'CVC2',
                                mask: '{{9999}}',
                                obfuscate: false,
                                placeholderLabel: '123',
                                preferredInputType: 'IntegerKeyboard',
                                tooltip: {
                                    json: {
                                        image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_3_v2.png',
                                        label: 'Please enter your CVC2 code as shown in the image'
                                    },
                                    image: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/ppf_cvv_3_v2.png',
                                    label: 'Please enter your CVC2 code as shown in the image'
                                },
                                alwaysShow: true,
                                wildcardMask: '{{****}}'
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 4,
                                            minLength: 3
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{3,4}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 3
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 3
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{3,4}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{3,4}$'
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 4,
                                                minLength: 3
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 4,
                                        minLength: 3
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{3,4}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{3,4}$'
                                    }
                                }
                            },
                            id: 'cvv',
                            type: 'tel'
                        }
                    }
                },
                730: {
                    json: {
                        deviceFingerprintEnabled: false,
                        allowsRecurring: false,
                        allowsTokenization: true,
                        autoTokenized: false,
                        displayHints: {
                            displayOrder: 5,
                            label: 'ACH',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_730_v1.png'
                        },
                        fields: [
                            {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 40,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'City',
                                    obfuscate: false,
                                    placeholderLabel: 'City',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'city',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 15,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'First name',
                                    obfuscate: false,
                                    placeholderLabel: 'First name',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'firstName',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 30,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Account holder name',
                                    obfuscate: false,
                                    placeholderLabel: 'John Doe',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'accountHolderName',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 8,
                                            minLength: 1
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{1,8}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 30,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Bank code',
                                    obfuscate: false,
                                    placeholderLabel: 'Bank code',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'bankCode',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 10,
                                            minLength: 1
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{1,10}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 80,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Account number',
                                    obfuscate: false,
                                    placeholderLabel: 'Account number',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'accountNumber',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 35,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 400,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Surname',
                                    obfuscate: false,
                                    placeholderLabel: 'Surname',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'surname',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 50,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 500,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Street',
                                    obfuscate: false,
                                    placeholderLabel: 'Street',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'street',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 15,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 600,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'House number',
                                    obfuscate: false,
                                    placeholderLabel: 'House number',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'houseNumber',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 10,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 700,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Zip code',
                                    obfuscate: false,
                                    placeholderLabel: 'Zip code',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'zip',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            }
                        ],
                        id: 730,
                        maxAmount: 11135390,
                        mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                        paymentMethod: 'directDebit',
                        usesRedirectionTo3rdParty: false,
                        type: 'product'
                    },
                    accountsOnFile: [],
                    accountOnFileById: {},
                    allowsRecurring: false,
                    allowsTokenization: true,
                    autoTokenized: false,
                    displayHints: {
                        json: {
                            displayOrder: 5,
                            label: 'ACH',
                            logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_730_v1.png'
                        },
                        displayOrder: 5,
                        label: 'ACH',
                        logo: 'https://payment.pay1.preprod.secured-by-ingenico.com/templates/master/global/css/img/ppimages/pp_logo_730_v1.png'
                    },
                    id: 730,
                    maxAmount: 11135390,
                    paymentMethod: 'directDebit',
                    mobileIntegrationLevel: 'OPTIMISED_SUPPORT',
                    usesRedirectionTo3rdParty: false,
                    // This array will be autofilled by data from paymentProductFieldById
                    paymentProductFields: [],
                    paymentProductFieldById: {
                        city: {
                            json: {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 40,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'City',
                                    obfuscate: false,
                                    placeholderLabel: 'City',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'city',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'City',
                                    obfuscate: false,
                                    placeholderLabel: 'City',
                                    preferredInputType: 'StringKeyboard'
                                },
                                displayOrder: 10,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'City',
                                obfuscate: false,
                                placeholderLabel: 'City',
                                preferredInputType: 'StringKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 40,
                                            minLength: 0
                                        }
                                    }
                                },
                                isRequired: false,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 40,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 40,
                                        minLength: 0
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 40,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 40,
                                        minLength: 0
                                    }
                                }
                            },
                            id: 'city',
                            type: 'text'
                        },
                        firstName: {
                            json: {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 15,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'First name',
                                    obfuscate: false,
                                    placeholderLabel: 'First name',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'firstName',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'First name',
                                    obfuscate: false,
                                    placeholderLabel: 'First name',
                                    preferredInputType: 'StringKeyboard'
                                },
                                displayOrder: 10,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'First name',
                                obfuscate: false,
                                placeholderLabel: 'First name',
                                preferredInputType: 'StringKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 15,
                                            minLength: 0
                                        }
                                    }
                                },
                                isRequired: false,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 15,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 15,
                                        minLength: 0
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 15,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 15,
                                        minLength: 0
                                    }
                                }
                            },
                            id: 'firstName',
                            type: 'text'
                        },
                        accountHolderName: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 30,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Account holder name',
                                    obfuscate: false,
                                    placeholderLabel: 'John Doe',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'accountHolderName',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 10,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Account holder name',
                                    obfuscate: false,
                                    placeholderLabel: 'John Doe',
                                    preferredInputType: 'StringKeyboard'
                                },
                                displayOrder: 10,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Account holder name',
                                obfuscate: false,
                                placeholderLabel: 'John Doe',
                                preferredInputType: 'StringKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 30,
                                            minLength: 0
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 30,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 30,
                                        minLength: 0
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 30,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 30,
                                        minLength: 0
                                    }
                                }
                            },
                            id: 'accountHolderName',
                            type: 'text'
                        },
                        bankCode: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 8,
                                            minLength: 1
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{1,8}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 30,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Bank code',
                                    obfuscate: false,
                                    placeholderLabel: 'Bank code',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'bankCode',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 30,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Bank code',
                                    obfuscate: false,
                                    placeholderLabel: 'Bank code',
                                    preferredInputType: 'StringKeyboard'
                                },
                                displayOrder: 30,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Bank code',
                                obfuscate: false,
                                placeholderLabel: 'Bank code',
                                preferredInputType: 'StringKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 8,
                                            minLength: 1
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{1,8}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 8,
                                                minLength: 1
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 8,
                                        minLength: 1
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{1,8}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{1,8}$'
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 8,
                                                minLength: 1
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 8,
                                        minLength: 1
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{1,8}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{1,8}$'
                                    }
                                }
                            },
                            id: 'bankCode',
                            type: 'tel'
                        },
                        accountNumber: {
                            json: {
                                dataRestrictions: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 10,
                                            minLength: 1
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{1,10}$'
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 80,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Account number',
                                    obfuscate: false,
                                    placeholderLabel: 'Account number',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                id: 'accountNumber',
                                type: 'tel',
                                validators: [
                                    'length',
                                    'regularExpression'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 80,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Account number',
                                    obfuscate: false,
                                    placeholderLabel: 'Account number',
                                    preferredInputType: 'IntegerKeyboard'
                                },
                                displayOrder: 80,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Account number',
                                obfuscate: false,
                                placeholderLabel: 'Account number',
                                preferredInputType: 'IntegerKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: true,
                                    validators: {
                                        length: {
                                            maxLength: 10,
                                            minLength: 1
                                        },
                                        regularExpression: {
                                            regularExpression: '^[0-9]{1,10}$'
                                        }
                                    }
                                },
                                isRequired: true,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 10,
                                                minLength: 1
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 10,
                                        minLength: 1
                                    },
                                    {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{1,10}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{1,10}$'
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 10,
                                                minLength: 1
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 10,
                                        minLength: 1
                                    },
                                    regularExpression: {
                                        json: {
                                            type: 'regularExpression',
                                            attributes: {
                                                regularExpression: '^[0-9]{1,10}$'
                                            }
                                        },
                                        type: 'regularExpression',
                                        errorMessageId: 'regularExpression',
                                        regularExpression: '^[0-9]{1,10}$'
                                    }
                                }
                            },
                            id: 'accountNumber',
                            type: 'tel'
                        },
                        surname: {
                            json: {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 35,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 400,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Surname',
                                    obfuscate: false,
                                    placeholderLabel: 'Surname',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'surname',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 400,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Surname',
                                    obfuscate: false,
                                    placeholderLabel: 'Surname',
                                    preferredInputType: 'StringKeyboard'
                                },
                                displayOrder: 400,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Surname',
                                obfuscate: false,
                                placeholderLabel: 'Surname',
                                preferredInputType: 'StringKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 35,
                                            minLength: 0
                                        }
                                    }
                                },
                                isRequired: false,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 35,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 35,
                                        minLength: 0
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 35,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 35,
                                        minLength: 0
                                    }
                                }
                            },
                            id: 'surname',
                            type: 'text'
                        },
                        street: {
                            json: {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 50,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 500,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Street',
                                    obfuscate: false,
                                    placeholderLabel: 'Street',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'street',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 500,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Street',
                                    obfuscate: false,
                                    placeholderLabel: 'Street',
                                    preferredInputType: 'StringKeyboard'
                                },
                                displayOrder: 500,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Street',
                                obfuscate: false,
                                placeholderLabel: 'Street',
                                preferredInputType: 'StringKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 50,
                                            minLength: 0
                                        }
                                    }
                                },
                                isRequired: false,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 50,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 50,
                                        minLength: 0
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 50,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 50,
                                        minLength: 0
                                    }
                                }
                            },
                            id: 'street',
                            type: 'text'
                        },
                        houseNumber: {
                            json: {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 15,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 600,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'House number',
                                    obfuscate: false,
                                    placeholderLabel: 'House number',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'houseNumber',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 600,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'House number',
                                    obfuscate: false,
                                    placeholderLabel: 'House number',
                                    preferredInputType: 'StringKeyboard'
                                },
                                displayOrder: 600,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'House number',
                                obfuscate: false,
                                placeholderLabel: 'House number',
                                preferredInputType: 'StringKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 15,
                                            minLength: 0
                                        }
                                    }
                                },
                                isRequired: false,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 15,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 15,
                                        minLength: 0
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 15,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 15,
                                        minLength: 0
                                    }
                                }
                            },
                            id: 'houseNumber',
                            type: 'text'
                        },
                        zip: {
                            json: {
                                dataRestrictions: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 10,
                                            minLength: 0
                                        }
                                    }
                                },
                                displayHints: {
                                    alwaysShow: false,
                                    displayOrder: 700,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Zip code',
                                    obfuscate: false,
                                    placeholderLabel: 'Zip code',
                                    preferredInputType: 'StringKeyboard'
                                },
                                id: 'zip',
                                type: 'text',
                                validators: [
                                    'length'
                                ]
                            },
                            displayHints: {
                                json: {
                                    alwaysShow: false,
                                    displayOrder: 700,
                                    formElement: {
                                        type: 'text'
                                    },
                                    label: 'Zip code',
                                    obfuscate: false,
                                    placeholderLabel: 'Zip code',
                                    preferredInputType: 'StringKeyboard'
                                },
                                displayOrder: 700,
                                formElement: {
                                    json: {
                                        type: 'text'
                                    },
                                    type: 'text',
                                    valueMapping: []
                                },
                                label: 'Zip code',
                                obfuscate: false,
                                placeholderLabel: 'Zip code',
                                preferredInputType: 'StringKeyboard',
                                alwaysShow: false,
                                wildcardMask: ''
                            },
                            dataRestrictions: {
                                json: {
                                    isRequired: false,
                                    validators: {
                                        length: {
                                            maxLength: 10,
                                            minLength: 0
                                        }
                                    }
                                },
                                isRequired: false,
                                validationRules: [
                                    {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 10,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 10,
                                        minLength: 0
                                    }
                                ],
                                validationRuleByType: {
                                    length: {
                                        json: {
                                            type: 'length',
                                            attributes: {
                                                maxLength: 10,
                                                minLength: 0
                                            }
                                        },
                                        type: 'length',
                                        errorMessageId: 'length',
                                        maxLength: 10,
                                        minLength: 0
                                    }
                                }
                            },
                            id: 'zip',
                            type: 'text'
                        }
                    }
                }
            };
            const paymentProduct = data[paymentProductId];

            Object.keys(paymentProduct.paymentProductFieldById)
                .map((key, index) => paymentProduct.paymentProductFieldById[key] = new FieldMock(paymentProduct.paymentProductFieldById[key]));

            // Fill paymentProductFields array by the data from paymentProductFieldById
            Object.keys(paymentProduct.paymentProductFieldById)
                .map((key, index) => paymentProduct.paymentProductFields.push(paymentProduct.paymentProductFieldById[key]));
            resolve(paymentProduct);
        });
    };

    this.getPaymentRequest = function() {
        return this.paymentRequest;
    };

    this.getEncryptor = function() {
        return this.encryptor;
    };
};

const PaymentRequestMock = function() {
    this.paymentProduct = null;
    this.values = [];

    this.setPaymentProduct = function(paymentProduct) {
        this.paymentProduct = paymentProduct;
    };

    this.getPaymentProduct = function() {
        return this.paymentProduct;
    };

    this.setValue = function(key, value) {
        this.values[key] = value;
    };

    this.getValue = function(key) {
        return this.values[key];
    };

    this.getValues = function() {
        return this.values;
    };

    this.isValid = function() {
        return true;
    };
};

const FieldMock = function(data) {
    for (const key in data) {
        this[key] = data[key];
    }

    this.isValid = function() {
        return true;
    };

    this.getErrorCodes = function() {
        return [];
    };

    this.applyMask = function(value) {
        return {formattedValue: value};
    }
};

const EncryptorMock = function() {
    this.encrypt = function(paymentRequest) {
        return new Promise(function(resolve, reject) {
            const paymentProduct = paymentRequest.getPaymentProduct();
            const paymentProductIdentifier = paymentProduct.paymentProductGroup
                ? paymentProduct.paymentProductGroup : paymentProduct.id;
            return resolve(paymentProductIdentifier);
        });
    };
};

function isEquivalent(a, b) {
    // Create arrays of property names
    const aProps = Object.getOwnPropertyNames(a);
    const bProps = Object.getOwnPropertyNames(b);

    // If number of properties is different,
    // objects are not equivalent
    if (aProps.length !== bProps.length) {
        return false;
    }

    for (let i = 0; i < aProps.length; i++) {
        const propName = aProps[i];

        // If values of same property are not equal,
        // objects are not equivalent
        if (a[propName] !== b[propName]) {
            return false;
        }
    }

    // If we made it this far, objects
    // are considered equivalent
    return true;
}

export default ConnectSdkMock;
