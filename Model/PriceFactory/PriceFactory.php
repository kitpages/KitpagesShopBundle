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
     * @param string $countryCode
     * @return float VAT of the cart
     */
    public function getCartVat($countryCode)
    {
        $lineList = $this->cart->getLineList();
        $totalVat = 0;
        foreach ($lineList as $line) {
            $totalVat += $this->getLineVat($line->getId(), $countryCode);
        }
        return $totalVat;
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

    /**
     * returns the VAT of a given line
     * @param int $lineId
     * @param string $countryCode
     * @return float price of the line
     */
    public function getLineVat($lineId, $countryCode)
    {
        $line = $this->cart->getLine($lineId);
        if (! $line instanceof CartLineInterface) {
            return 0;
        }
        $cartable = $line->getCartable();
        if ($cartable instanceof ProductInterface) {
            return $cartable->getShopUnitVat($countryCode) * $line->getQuantity();
        }
        return 0;
    }
}
