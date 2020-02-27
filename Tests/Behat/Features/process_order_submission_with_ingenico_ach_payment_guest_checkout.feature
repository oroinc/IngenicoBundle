@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroPaymentBundle:ProductsAndShoppingListsForPayments.yml

Feature: Order submission with Ingenico ACH payment option
  In order to purchase goods using Ingenico - ACH payment system
  As a Guest customer
  I want to enter and complete checkout without registration with payment via Ingenico ACH

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Guest | second_session |

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

  Scenario: Enable guest shopping list setting
    Given I go to System/ Configuration
    And I follow "Commerce/Sales/Shopping List" on configuration sidebar
    And uncheck "Use default" for "Enable guest shopping list" field
    And I check "Enable guest shopping list"
    When I save form
    Then I should see "Configuration saved" flash message
    And the "Enable guest shopping list" checkbox should be checked

  Scenario: Enable guest checkout setting
    Given I follow "Commerce/Sales/Checkout" on configuration sidebar
    And uncheck "Use default" for "Enable Guest Checkout" field
    And I check "Enable Guest Checkout"
    When I save form
    Then the "Enable Guest Checkout" checkbox should be checked

  Scenario: Create Shopping List as unauthorized user
    Given I proceed as the Guest
    And I am on homepage
    And type "SKU123" in "search"
    And I click "Search Button"
    And I click "product1"
    When I click "Add to Shopping List"
    Then I should see "Product has been added to" flash message
    When I open shopping list widget
    And I click "View List"
    Then I should see "product1"

  Scenario: Successful order payment with Ingenico ACH
    Given I click "Create Order"
    When I click "Continue as a Guest"
    And I fill form with:
      | First Name      | Tester1         |
      | Last Name       | Testerson       |
      | Email           | tester@test.com |
      | Street          | First avenue    |
      | City            | Hollywood       |
      | Country         | United States   |
      | State           | Florida         |
      | Zip/Postal Code | 33019           |
    And I click "Ship to This Address"
    And I click "Continue"
    And I check "Flat Rate" on the "Shipping Method" checkout step and press Continue
    And I check "ACH" on the checkout page
    And I fill "Ingenico ACH Form" with:
      | Account Holder Name | John Doe   |
      | Bank Code           | 091000019  |
      | Account Number      | 1091000019 |
    And I click "Continue"
    And I uncheck "Save my data and create an account" on the checkout page
    And I click "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title

  Scenario: Check order status in admin panel
    When I proceed as the Admin
    And I go to Sales/Orders
    Then I should see Pending payment in grid
