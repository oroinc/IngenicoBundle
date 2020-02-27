@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroPaymentBundle:ProductsAndShoppingListsForPayments.yml

Feature: Order submission with Ingenico credit card sale payment action on Single Page Checkout
  In order to purchase goods using Ingenico - Credit Card payment system on Single Page Checkout
  As a Customer
  I want to enter and complete single page checkout with payment via Ingenico Credit Card

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Buyer | second_session |
    And I activate "Single Page Checkout" workflow

  Scenario: Create new Ingenico Credit Cards integration
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
      | Enabled Products  | Credit cards         |
      | Payment Action    | Sale                 |
      | Status            | Active               |
    And I save and close form
    Then I should see "Integration saved" flash message
    And I should see Ingenico in grid

  Scenario: Create new Payment Rule for Ingenico Credit Cards integration
    Given I go to System/Payment Rules
    And I click "Create Payment Rule"
    And I check "Enabled"
    And I fill in "Name" with "Ingenico"
    And I fill in "Sort Order" with "1"
    And I select "Ingenico" from "Method"
    And I click "Add Method Button"
    And I save and close form
    Then I should see "Payment rule has been saved" flash message

  Scenario: Successful order payment with Ingenico Credit Cards
    Given There are products in the system available for order
    And I proceed as the Buyer
    And I signed in as AmandaRCole@example.org on the store frontend
    And I open page with shopping list List 2
    When I click "Create Order"
    And I check "Visa" on the checkout page
    And I fill "Ingenico Credit Card Form" with:
      | Card number | 4012001038443335 |
      | Expiry date | 11/2030          |
      | CVV         | 123              |
    And I click "Submit Order"
    Then I should see "Thank You For Your Purchase!"

  Scenario: Check order status on frontstore order view page
    When I click "click here to review"
    Then I should see "Paid in full"
