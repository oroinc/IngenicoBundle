# IngenicoBundle

IngenicoBundle provides integration with [Ingenico](https://www.ingenico.com/epayments) Payment Gateway.

The bundle helps admin users to enable and configure **Ingenico ePayments Connect** [payment method](https://github.com/oroinc/orocommerce/tree/master/src/Oro/Bundle/PaymentBundle) in order for customers to pay for orders with SEPA direct debit, ACH payments, Credit and Debit cards.

## Installation

The bundle can be installed for OroCommerce v4.1.*

When installing the bundle for OroCommerce v4.1.0, make sure that you remove or rename the package-json.lock file in the application root before adding the bundle's package. This is not required for OroCommerce applications after v4.1.1.

Add `ingenico-epayments/connect-extension-orocommerce` package to your installation:
```bash
composer require "ingenico-epayments/connect-extension-orocommerce"
```
In casе the package is added to an already installed application, then [platform update](https://doc.oroinc.com/backend/setup/upgrade-to-new-version/) is required.

## Setting Up the Integration

Navigate to the "System -> Integrations" and click "Create Integration".

Select the "Ingenico ePayments Connect platform" integration type and fill in the fields below:

 - *Api Key ID*: must be set to your Ingenico API Key ID
 - *Secret API Key*: must be set to your Ingenico Secret API Key
 - *API Endpoint*: must be set to corresponding Ingenico API Endpoint
 - *Merchant ID*: must be set to your Ingenico Merchant ID
 - *Enabled Products*: must be set and point to the payment products to be available
 - *Payment Action*: must be set and point to the payment action type performed for Credit Cards payments
 - *Direct Debit Text*: must be filled with short merchant info to be used with Direct Debit payment options (required for ACH and SEPA payment products) 
