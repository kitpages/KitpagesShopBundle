<?php
namespace Kitpages\ShopBundle\Model\Cart;
/**
 * This interface is used for the cart
 */
interface CartInterface
{
    /**
     * @param CartableInterface $item : item to add
     * @param int $quantity
     * @param CartLineInterface $parentLine : parent line of this new product
     * @return CartLineInterface the line added
     */
    public function addLine(
        CartableInterface $item,
        $quantity = 1,
        CartLineInterface $parentLine = null
    );

    /**
     * remove a ligne
     * @param int $lineId id of the line
     */
    public function deleteLine($lineId);

    /**
     * remove all lines from the cart
     */
    public function emptyCart();

    /**
     * returns a given line from the cart
     * @param int $lineId
     * @return CartLineInterface
     */
    public function getLine($lineId);

    /**
     * returns all the lines of the cart
     * @return array of CartLineInterface
     */
    public function getLineList();
}
?>
