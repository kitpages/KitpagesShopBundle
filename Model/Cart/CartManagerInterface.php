<?php
namespace Kitpages\ShopBundle\Model\Cart;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Kitpages\ShopBundle\Model\PriceFactory\PriceFactoryInterface;
use Kitpages\ShopBundle\Model\PriceFactory\PriceFactory;
use Kitpages\ShopBundle\Model\Cart\CartInterface;

/**
 * This is a service in the DIC used to do operations on a cart.
 */
interface CartManagerInterface
{
    /**
     * constructor of the service
     * @param Session current session
     * @param EventDispatcherInterface $dispatcher
     * @param PriceFactoryInterface|null $priceFactory
     * @param CartInterface|null $cart
     */
    public function __construct(
        Session $session,
        EventDispatcherInterface $dispatcher,
        PriceFactoryInterface $priceFactory = null,
        CartInterface $cart = null
    );

    /**
     * returns the weight of the cart
     * @return float total weight of the cart
     */
    public function getWeight();

    /**
     * returns the product count of the cart
     * @return int product count in the cart
     */
    public function getProductCount();

    /**
     * returns the price of line in the cart
     * @param int $lineId
     * @return float price of a given line
     */
    public function getLinePrice($lineId);

    /**
     * returns the vat of the line in the cart
     * @param int $lineId
     * @param string $countryCode (ex : FR, US, BE)
     * @return float vat of a given line
     */
    public function getLineVat($lineId, $countryCode);

    /**
     * returns the total price of the cart
     * @return float price of the cart
     */
    public function getTotalPrice();

    /**
     * returns the current cart saved in session
     * @return CartInterface cart saved in session
     */
    public function getCart();
}

?>
