<?php
namespace Kitpages\ShopBundle\Model\Cart;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderLine;

use Symfony\Component\HttpFoundation\Session;

class OrderManager
{

    public function __construct()
    {
    }

    public function createOrder(
        CartableInterface $cart,
        OrderUser $invoiceUser = null,
        OrderUser $shippingUser = null

    )
    {
        $cart = $this->cartManager->getCart();
        $order = new Order();

    }
}
