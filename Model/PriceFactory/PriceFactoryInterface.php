<?php
namespace Kitpages\ShopBundle\Model\PriceFactory;

use Kitpages\ShopBundle\Model\Cart\CartInterface;
/**
 * used as a service in the DIC
 */
interface PriceFactoryInterface {
    /**
     * @param CartInterface $cart
     */
    public function __construct(CartInterface $cart);
    /**
     * @return float price of the cart
     */
    public function getCartPrice();

    /**
     * returns the price of a given line
     * @param int $lineId
     * @return float price of the line
     */
    public function getLinePrice($lineId);
}
?>
