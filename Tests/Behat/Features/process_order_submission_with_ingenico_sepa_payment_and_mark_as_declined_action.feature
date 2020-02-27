@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroPaymentBundle:ProductsAndShoppingListsForPayments.yml

Feature: Order submission with Ingenico SEPA Direct Debit payment and marking it then as declined
  In order to purchase goods using Ingenico - SEPA Direct Debit payment system
  As a Customer
  I want to enter and complete checkout without registration with payment via Ingenico SEPA Direct Debit
  and then to mark order as declined

  Scenario: Create new Ingenico ePayments Connect platform Integration
    Given I login as AmandaRCole@example.org the "Buyer" at "first_session" session
    And I login as administrator and use in "second_session" as "Admin"
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
      | Enabled Products  | SEPA Direct Debit    |
      | Payment Action    | Sale                 |
      | Status            | Active               |
      | Derect Debit Text | Merchant info        |
    And I save and close form
    Then I should see "Integration saved" flash message
    And I should see Ingenico in grid

  Scenario: Create new Payment Rule for Ingenico ePayments Connect platform integration
    Given I go to System/Payment Rules
    And I click "Create Payment Rule"
    And I check "Enabled"
    And I fill in "Name" with "Ingenico"
    And I fill in "Sort Order" with "1"
    And I select "Ingenico" from "Method"
    And I click "Add Method Button"
    And I save and close form
    Then I should see "Payment rule has been saved" flash message

  Scenario: Successful order payment with Ingenico SEPA Direct Debit
    Given I proceed as the Buyer
    When I open page with shopping list List 2
    And I click "Create Order"
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Billing Information" checkout step and press Continue
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Shipping Information" checkout step and press Continue
    And I check "Flat Rate" on the "Shipping Method" checkout step and press Continue
    And I check "SEPA direct debit" on the checkout page
    And I fill "Ingenico SEPA Form" with:
      | IBAN                | NL08 INGB 0000000 555 |
      | Account Holder Name | John Doe              |
      | Debtor's surname    | Smith                 |
    And I click "Continue"
    And I click "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title
    When I proceed as the Admin
    And I go to Sales/Orders
    When I click view "Pending payment" in grid
    Then I should see order with:
      | Payment Method | Ingenico        |
      | Payment Status | Pending payment |
    And I click "Mark as Declined" on row "Pending" in grid "Order Payment Transaction Grid"
    When I click "Yes, Decline"
    Then I should see "The payment has been marked successfully as declined." flash message
    And I should see order with:
      | Payment Status | Payment declined |
    And I should see following "Order Payment Transaction Grid" grid:
      | Payment Method | Type          | Successful |
      | Ingenico       | Purchase      | No        |
      | Ingenico       | SEPA Pending  | No        |
