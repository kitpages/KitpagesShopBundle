# language: en
Feature: cart
  In order to get products bought by the visitor in a list

  Scenario: Add product to the cart
    Given I have a product named "tshirt"
    When I add "tshirt"
    Then I should get one "CartLine"

