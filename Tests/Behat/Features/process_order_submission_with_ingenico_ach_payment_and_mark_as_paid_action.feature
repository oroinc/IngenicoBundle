@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroPaymentBundle:ProductsAndShoppingListsForPayments.yml
@fixture-IngenicoBundle:CustomerUSBillingAddressFixture.yml

Feature: Order submission with Ingenico ACH payment and marking it then as paid
  In order to purchase goods using Ingenico - Ach payment system
  As a Customer
  I want to enter and complete checkout without registration with payment via Ingenico ACH
  and then to mark order as fully paid

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
      | Enabled Products  | ACH                  |
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

  Scenario: Successful order payment with Ingenico ACH
    Given I proceed as the Buyer
    When I open page with shopping list List 2
    And I click "Create Order"
    And I select "ORO, First avenue, HOLLYWOOD FL US 33019" on the "Billing Information" checkout step and press Continue
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Shipping Information" checkout step and press Continue
    And I check "Flat Rate" on the "Shipping Method" checkout step and press Continue
    And I should see "Ingenico Label"
    And I check "ACH" on the checkout page
    And I fill "Ingenico ACH Form" with:
      | Account Holder Name | John Doe   |
      | Bank Code           | 091000019  |
      | Account Number      | 1091000019 |
    And I click "Continue"
    And I click "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title
    When I proceed as the Admin
    And I go to Sales/Orders
    When I click view "Pending payment" in grid
    Then I should see order with:
      | Payment Method | Ingenico        |
      | Payment Status | Pending payment |
    And I click "Mark as Paid" on row "Pending" in grid "Order Payment Transaction Grid"
    When I click "Yes, Mark as Paid"
    Then I should see "The payment has been marked successfully as paid." flash message
    And I should see order with:
      | Payment Status | Paid in full |
    And I should see following "Order Payment Transaction Grid" grid:
      | Payment Method | Type          | Successful |
      | Ingenico       | Purchase      | Yes        |
      | Ingenico       | ACH Pending   | Yes        |
