@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroPaymentBundle:ProductsAndShoppingListsForPayments.yml

Feature: Order submission with Ingenico credit card and save and reuse feature
  In order to purchase goods using Ingenico - Credit Card payment system
  As a Customer customer
  I want to enter and complete checkout with payment via Ingenico Credit Card
  Scenario: Create new Ingenico ePayments Connect platform Integration
    Given I login as AmandaRCole@example.org the "Buyer" at "first_session" session
    And I login as administrator and use in "second_session" as "Admin"
    When I go to System/Integrations/Manage Integrations
    And I click "Create Integration"
    And I select "Ingenico ePayments Connect platform" from "Type"
    And I fill "Ingenico Integration Form" with:
      | Name               | Ingenico             |
      | Label             | Ingenico Label       |
      | Short Label       | Ingenico ShortLabel  |
      | Api Key ID         | 12345                |
      | Secret API Key     | secret               |
      | API Endpoint       | https://api.endpoint |
      | Merchant ID        | 777                  |
      | Enabled Products   | Credit cards         |
      | Payment Action     | Sale                 |
      | Status             | Active               |
      | Allow Tokenization | true                 |
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

  Scenario: Successful order payment with Ingenico Credit Cards
    Given I proceed as the Buyer
    When I open page with shopping list List 2
    And I click "Create Order"
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Billing Information" checkout step and press Continue
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Shipping Information" checkout step and press Continue
    And I check "Flat Rate" on the "Shipping Method" checkout step and press Continue
    And I check "Visa" on the checkout page
    And I fill "Ingenico Credit Card Form" with:
      | Card number        | 4012001038443335 |
      | Expiry date        | 11/2030          |
      | CVV                | 123              |
      | Save for later use | true             |
    And I click "Continue"
    And I uncheck "Delete this shopping list after submitting order" on the "Order Review" checkout step and press Submit Order
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title
    When I proceed as the Admin
    And I go to Sales/Orders
    Then I should see Paid in full in grid

  Scenario: Successful order payment with Ingenico Credit Cards payment token
    Given I proceed as the Buyer
    When I open page with shopping list List 2
    And I click "Create Order"
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Billing Information" checkout step and press Continue
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Shipping Information" checkout step and press Continue
    And I check "Flat Rate" on the "Shipping Method" checkout step and press Continue
    And I check "Visa" on the checkout page
    And I fill "Ingenico Credit Card Form" with:
      | Saved card | ************3335 |
      | CVV        | 123              |
    And I click "Continue"
    And I click "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title
    When I click "click here to review"
    Then I should see "Paid in full"
