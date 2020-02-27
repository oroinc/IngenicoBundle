@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroPaymentBundle:ProductsAndShoppingListsForPayments.yml
@fixture-IngenicoBundle:CustomerUSBillingAddressFixture.yml

Feature: Order submission with Ingenico ACH payment option on Single Page Checkout
  In order to purchase goods using Ingenico - ACH payment system on Single Page Checkout
  As a Customer
  I want to enter and complete single page checkout with payment via Ingenico ACH

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Buyer | second_session |
    And I activate "Single Page Checkout" workflow

  Scenario: Create new Ingenico ACH integration
    Given I proceed as the Admin
    And I login as administrator
    When I go to System/Integrations/Manage Integrations
    And I click "Create Integration"
    And I select "Ingenico ePayments Connect platform" from "Type"
    And I fill "Ingenico Integration Form" with:
      | Name              | Ingenico             |
      | Label             | Ingenico Label       |
      | Short Label       | Ingenico ShortLabel  |
      | Api Key ID        | 12345                |
      | Secret API Key    | secret               |
      | API Endpoint      | https://api.endpoint |
      | Merchant ID       | 777                  |
      | Enabled Products  | ACH                  |
      | Payment Action    | Sale                 |
      | Status            | Active               |
      | Derect Debit Text | Merchant info        |
    And I save and close form
    Then I should see "Integration saved" flash message
    And I should see Ingenico in grid

  Scenario: Create new Payment Rule for Ingenico ACH integration
    Given I go to System/Payment Rules
    And I click "Create Payment Rule"
    And I check "Enabled"
    And I fill in "Name" with "Ingenico"
    And I fill in "Sort Order" with "1"
    And I select "Ingenico" from "Method"
    And I click "Add Method Button"
    And I save and close form
    Then I should see "Payment rule has been saved" flash message

  Scenario: Successful order payment with Ingenico ACH
    Given There are products in the system available for order
    And I proceed as the Buyer
    And I signed in as AmandaRCole@example.org on the store frontend
    And I open page with shopping list List 2
    When I click "Create Order"
    And I select "ORO, First avenue, HOLLYWOOD FL US 33019" from "Select Billing Address"
    And I check "ACH" on the checkout page
    And I fill "Ingenico ACH Form" with:
      | Account Holder Name | John Doe   |
      | Bank Code           | 091000019  |
      | Account Number      | 1091000019 |
    And I click "Submit Order"
    Then I should see "Thank You For Your Purchase!"

  Scenario: Check order status on frontstore order view page
    When I click "click here to review"
    Then I should see "Pending payment"
