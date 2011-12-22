<?php
namespace Kitpages\ShopBundle\Model\User;
/**
 * This interface is used for the current user of a website.
 */
interface UserInterface {
  /**
   * reference of the product in the shop (must be unique in the shop)
   * @return string reference
   */
  public function getShopUserId();
  /**
   * name printed in the cart
   * @return string name of the line in the cart
   */
  public function getShopUserLogin();
}
?>
