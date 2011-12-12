<?php
namespace Kitpages\ShopBundle\Model\Cart;
/**
 * a line in the cart
 */
class CartLine
    implements CartLineInterface
{
    ////
    // variables
    ////
    protected $quantity = 0;
    protected $cartable = null;
    protected $lineId = null;
    protected $parentLineId = null;

    ////
    // Method of the CartInterface
    ////
    /**
     * returns the quantity of the element in the cart
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    /**
     * returns the element inserted in the line
     * @return CartableInterface
     */
    public function getCartable()
    {
        return $this->cartable;
    }

    /**
     * @param int id of the line in the Cart
     */
    public function setId($lineId)
    {
        $this->lineId = $lineId;
    }

    /**
     * @return int the if of the line in the cart
     */
    public function getId()
    {
        return $this->lineId;
    }

    /**
     * @param int id of the parent of the line in the Cart (default : null)
     */
    public function setParentLineId($parentLineId)
    {
        $this->parentLineId = $parentLineId;
    }

    /**
     * @return int the if of the parent of the line in the cart (null if root line)
     */
    public function getParentLineId()
    {
        return $this->parentLineId;
    }

    ////
    // other public methods
    ////
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * sets the cartable of the cartLine
     * @param CartableInterface $cartable
     */
    public function setCartable(CartableInterface $cartable)
    {
        $this->cartable = $cartable;
    }
}
