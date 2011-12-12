<?php
namespace Kitpages\ShopBundle\Model\Cart;
/**
 * This interface is used a line in the cart
 */
interface CartLineInterface {
    /**
     * returns the quantity of the element in the cart
     * @return int
     */
    public function getQuantity();
    /**
     * returns the element inserted in the line
     * @return CartableInterface
     */
    public function getCartable();

    /**
     * @param int id of the line in the Cart
     */
    public function setId($lineId);

    /**
     * @return int the if of the line in the cart
     */
    public function getId();

    /**
     * @param int id of the parent of the line in the Cart (default : null)
     */
    public function setParentLineId($lineId);

    /**
     * @return int the if of the parent of the line in the cart (null if root line)
     */
    public function getParentLineId();

}
?>
