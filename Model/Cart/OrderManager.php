<?php
namespace Kitpages\ShopBundle\Model\Cart;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderLine;
use Kitpages\ShopBundle\Model\Cart\CartInterface;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class OrderManager
{
    protected $isCartIncludingVat = null;
    protected $cartManager = null;
    protected $doctrine = null;
    protected $logger = null;

    public function __construct(
        Registry $doctrine,
        LoggerInterface $logger,
        CartManagerInterface $cartManager,
        $isCartIncludingVat
    )
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->cartManager = $cartManager;
        $this->isCartIncludingVat = $isCartIncludingVat;
    }


    /**
     * @param CartInterface $cart
     * @param OrderUser|null $invoiceUser
     * @param OrderUser|null $shippingUser
     * @return Order $order
     */
    public function createOrder(
        $userId = null,
        $userLogin = null,
        OrderUser $invoiceUser = null,
        OrderUser $shippingUser = null

    )
    {
        $this->logger->debug("in create order");
        $cart = $this->cartManager->getCart();
        $price = $this->cartManager->getTotalPrice();

        // create order
        $order = new Order();
        $this->setOrderPrice($order, $price);
        $order->setRandomKey($this->getNewRandomKey());

        // create first orderHistory
        $orderHistory = new OrderHistory();
        $orderHistory->setUserId($userId);
        $orderHistory->setUserLogin($userLogin);
        $orderHistory->setOrder($order);
        $orderHistory->setState("created");
        $orderHistory->setStateDate(new \DateTime());
        $orderHistory->setPriceIncludingVat($order->getPriceIncludingVat());
        $orderHistory->setPriceWithoutVat($order->getPriceWithoutVat());
        $order->addOrderHistory($orderHistory);
        $order->setStateFromHistory();

        // create lines
        $lineList = $cart->getLineList();
        foreach ($lineList as $line)
        {
            $orderLine = new OrderLine();
            $orderLine->setOrder($order);
            $orderLine->setCartLineId($line->getId());
            $orderLine->setCartParentLineId($line->getParentLineId());
            $orderLine->setQuantity($line->getQuantity());
            $orderLine->setShopName($line->getCartable()->getShopName());
            $orderLine->setShopDescription($line->getCartable()->getShopDescription());
            $orderLine->setShopReference($line->getCartable()->getShopReference());
            if ($this->isCartIncludingVat) {
                $orderLine->setPriceIncludingVat($this->cartManager->getLinePrice($line->getId()));
            } else {
                $orderLine->setPriceWithoutVat($this->cartManager->getLinePrice($line->getId()));
            }
            $order->addOrderLine($orderLine);
        }

        // add users
        if (!is_null($invoiceUser)) {
            $order->setInvoiceUser($invoiceUser);
        }
        if (!is_null($shippingUser)) {
            $order->setShippingUser($shippingUser);
        }
        // persist order
        $em = $this->doctrine->getEntityManager();
        $em->persist($order);
        $em->flush();

        return $order;
    }

    protected function getNewRandomKey() {
        $em = $this->doctrine->getEntityManager();
        $repo = $em->getRepository("KitpagesShopBundle:Order");
        $keyExists = true;
        while ($keyExists == true ) {
            $key = uniqid("order-", true);
            $order = $repo->findOneBy(array('randomKey' => $key));
            if ($order == null) {
                $keyExists = false;
            }
        }
        return $key;
    }

    protected function setOrderPrice(Order $order, $price)
    {
        if ($this->isCartIncludingVat) {
            $order->setPriceIncludingVat($price);
        }
        else {
            $order->setPriceWithoutVat($price);
        }
    }
}
