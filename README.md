# Ingenico Extension for OroCommerce

Ingenico’s payment extension for OroCommerce enables sellers to accept online payments from customers in the OroCommerce storefront and manage all payment transactions in the OroCommerce back-office.

## Installation

The bundle can be installed for OroCommerce v4.1.*

When installing the bundle for OroCommerce v4.1.0, make sure that you remove or rename the package-json.lock file in the application root before adding the bundle's package. This is not required for OroCommerce applications after v4.1.1.

Add `ingenico-epayments/connect-extension-orocommerce` package to your installation:
```bash
composer require "ingenico-epayments/connect-extension-orocommerce"
```
In casе the package is added to an already installed application, then [platform update](https://doc.oroinc.com/backend/setup/upgrade-to-new-version/) is required.

## Configuration

For the detailed instructions on the integration configuration, see the [OroCommerce Integration with Ingenico](Resources/doc/integration.md) guide.
