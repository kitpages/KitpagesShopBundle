<?php
namespace Kitpages\ShopBundle\Model\PriceFactory;

use Kitpages\ShopBundle\Model\Cart\CartInterface;
use Kitpages\ShopBundle\Model\Cart\CartLineInterface;
use Kitpages\ShopBundle\Model\Cart\ProductInterface;

class PriceFactory
    implements PriceFactoryInterface
{
    /**
     * @var \Kitpages\ShopBundle\Model\Cart\CartInterface|null
     */
    protected $cart = null;
    /**
     * @param CartInterface $cart
     */
    public function __construct(CartInterface $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return float price of the cart
     */
    public function getCartPrice()
    {
        $lineList = $this->cart->getLineList();
        $totalPrice = 0;
        foreach ($lineList as $line) {
            $totalPrice += $this->getLinePrice($line->getId());
        }
        return $totalPrice;
    }

    /**
     * returns the price of a given line
     * @param int $lineId
     * @return float price of the line
     */
    public function getLinePrice($lineId)
    {
        $line = $this->cart->getLine($lineId);
        if (! $line instanceof CartLineInterface) {
            return 0;
        }
        $cartable = $line->getCartable();
        if ($cartable instanceof ProductInterface) {
            return $cartable->getShopUnitPrice() * $line->getQuantity();
        }
        return 0;
    }
}
