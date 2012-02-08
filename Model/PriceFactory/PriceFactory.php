<?php
namespace Kitpages\ShopBundle\Model\PriceFactory;

use Kitpages\ShopBundle\Model\Cart\CartInterface;
use Kitpages\ShopBundle\Model\Cart\CartLineInterface;
use Kitpages\ShopBundle\Model\Cart\ProductInterface;
use Kitpages\ShopBundle\Model\Discount\DiscountFreeProduct;

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
        if ($cartable instanceof DiscountFreeProduct) {
            // get productList ordered by price and price > 0
            $productList = $this->cart->getLineList();
            $orderedList = array();
            foreach ($productList as $product) {
                if (! $product->getCartable() instanceof ProductInterface) {
                    continue;
                }
                if ($product->getCartable()->getShopUnitPrice() == 0) {
                    continue;
                }
                $orderedList[] = $product;
            }
            // order the list according to shopUnitPrice
            usort($orderedList, function($item1, $item2) {
                if ($item1->getCartable()->getShopUnitPrice() < $item2->getCartable()->getShopUnitPrice()) {
                    return -1;
                }
                if ($item1->getCartable()->getShopUnitPrice() == $item2->getCartable()->getShopUnitPrice()) {
                    return 0;
                }
                if ($item1->getCartable()->getShopUnitPrice() > $item2->getCartable()->getShopUnitPrice()) {
                    return 1;
                }
                return null;
            });
            // get all products
            $discount = $cartable;
            $priceList = array();
            foreach ($orderedList as $product) {
                for ($i = 0 ; $i < $product->getQuantity() ; $i ++) {
                    $priceList[] = $product->getCartable()->getShopUnitPrice();
                }
            }
            $freeProductCount = (int) (count($priceList) / $discount->getProductCountToActivate() );

            // calculating discount
            $price = 0;
            for ($i = 0; $i < $freeProductCount ; $i++) {
                $price -= $priceList[$i];
            }
            return $price;
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
