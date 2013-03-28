<?php
namespace Kitpages\ShopBundle\Model\Cart;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Kitpages\ShopBundle\Model\PriceFactory\PriceFactoryInterface;
use Kitpages\ShopBundle\Model\PriceFactory\PriceFactory;
use Kitpages\ShopBundle\Model\Cart\CartInterface;
use Kitpages\ShopBundle\Model\Cart\Cart;
use Kitpages\ShopBundle\Event\ShopEvent;
use Kitpages\ShopBundle\KitpagesShopEvents;

class CartManager
    implements CartManagerInterface
{
    /** @var PriceFactoryInterface|null */
    protected $priceFactory = null;
    /** @var null|Session */
    protected $session = null;
    /** @var null|EventDispatcherInterface */
    protected $dispatcher = null;
    /**
     * constructor of the service
     * @param Session current session
     * @param PriceFactoryInterface|null $priceFactory
     * @param CartInterface|null $cart
     */
    public function __construct(
        Session $session,
        EventDispatcherInterface $dispatcher,
        PriceFactoryInterface $priceFactory = null,
        CartInterface $cart = null
    )
    {
        $this->dispatcher = $dispatcher;
        $this->session = $session;
        if ($cart === null) {
            $cart = $this->session->get('kitpages_shop_cart', new Cart());

        }

        if ($priceFactory === null) {
            $priceFactory = new PriceFactory($cart);
        }
        $this->priceFactory = $priceFactory;

        if (! $this->session->get("kitpages_shop_cart")) {
            $event = new ShopEvent();
            $event->set("cart", $cart);
            $event->set("priceFactory", $priceFactory);
            $this->dispatcher->dispatch(KitpagesShopEvents::AFTER_CART_INIT, $event);
            $this->priceFactory = $event->get("priceFactory");
        }

        $this->session->set('kitpages_shop_cart', $cart);

    }

    ////
    // mandatory methods
    ////
    /**
     * returns the weight of the cart
     * @return float total weight of the cart
     */
    public function getWeight()
    {
        $lineList = $this->getCart()->getLineList();
        $totalWeight = 0;
        foreach ($lineList as $line) {
            $cartable = $line->getCartable();
            if ($cartable instanceof ProductInterface) {
                $totalWeight += $cartable->getShopWeight() * $line->getQuantity();
            }
        }
        return $totalWeight;
    }

    /**
     * returns the product count of the cart
     * @return int product count in the cart
     */
    public function getProductCount()
    {
        $lineList = $this->getCart()->getLineList();
        $totalCount = 0;
        foreach ($lineList as $line) {
            $cartable = $line->getCartable();
            if ($cartable instanceof ProductInterface) {
                $totalCount += $line->getQuantity();
            }
        }
        return $totalCount;
    }

    /**
     * returns the price of line in the cart
     * @param int $lineId id of the cart line
     * @return float price of a given line
     */
    public function getLinePrice($lineId)
    {
        return $this->priceFactory->getLinePrice($lineId);
    }

    /**
     * returns the VAT of line in the cart
     * @param int $lineId id of the cart line
     * @return float price of a given line
     */
    public function getLineVat($lineId, $countryCode)
    {
        return $this->priceFactory->getLineVat($lineId, $countryCode);
    }

    /**
     * returns the total price of the cart
     * @return float price of the cart
     */
    public function getTotalPrice()
    {
        return $this->priceFactory->getCartPrice();
    }
    /**
     * returns the total price of the cart
     * @param string $countryCode (ex: "FR", "US")
     * @return float price of the cart
     */
    public function getTotalVat($countryCode)
    {
        return $this->priceFactory->getCartVat($countryCode);
    }

    public function getCart()
    {
        return $this->session->get("kitpages_shop_cart");
    }

}
