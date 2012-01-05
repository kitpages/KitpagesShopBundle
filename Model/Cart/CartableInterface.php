<?php
namespace Kitpages\ShopBundle\Model\Cart;
/**
 * This interface is used for every object who can be
 * inserted in the cart.
 */
interface CartableInterface {
  /**
   * reference of the product in the shop (must be unique in the shop)
   * @return string reference
   */
  public function getShopReference();
  /**
   * name printed in the cart
   * @return string name of the line in the cart
   */
  public function getShopName();
  /**
   * description printed in the commande and in the invoice
   * @return string description of the line in the cart
   */
  public function getShopDescription();
    /**
     * extra data saved by serialization in the database
     * @return array elements given to the cart and serialized in the order
     */
    public function getShopData();
}
?>
