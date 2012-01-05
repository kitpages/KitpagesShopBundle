<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderUser;

use Kitano\PaymentBundle\Model\Transaction;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;



class OrderController extends Controller
{
    public function createAction()
    {
        $orderManager = $this->get('kitpages_shop.orderManager');
        $logger = $this->get('logger');
        $logger->debug("create order, user=".$this->get('security.context')->getToken()->getUser());
        // create order from cart
        $order = $orderManager->createOrder();
        $order->setLocale($this->get('session')->getLocale());
        if($this->get('security.context')->isGranted('ROLE_USER')) {
            $order->setUsername($this->get('security.context')->getToken()->getUsername());
        }
        // persist order
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($order);
        $em->flush();


        // redirect to the next page
        $displayOrderRoute = $this->container->getParameter('kitpages_shop.order_display_route_name');
        return $this->redirect(
            $this->generateUrl(
                $displayOrderRoute,
                array('orderId'=> $order->getId())
            )
        );
    }

    public function displayOrderAction(
        Order $order,
        OrderUser $invoiceUser = null,
        OrderUser $shippingUser = null
    )
    {
        if (! $this->get('security.context')->isGranted('ROLE_USER') ) {
            return new Response('The user should be authenticated on this page');
        }

        if (
            ($order->getUsername() != null) &&
            ($order->getUsername() != $this->get('security.context')->getToken()->getUsername())
        ) {
            return new Response('You are not allowed to see this order');
        }

        $em = $this->getDoctrine()->getEntityManager();

        if ($order->getUsername() == null) {
            $order->setUsername($this->get('security.context')->getToken()->getUsername());
        }
        if ($invoiceUser instanceof OrderUser) {
            $order->setInvoiceUser($invoiceUser);
            $invoiceUser->setInvoiceOrder($order);
        }
        if ($shippingUser instanceof OrderUser) {
            $order->setShippingUser($shippingUser);
            $shippingUser->setShippingOrder($order);
        }

        $orderHistory = new OrderHistory();
        $orderHistory->setUsername($this->get('security.context')->getToken()->getUsername());
        $orderHistory->setOrder($order);
        $orderHistory->setState("ready_to_pay");
        $orderHistory->setNote("order complete and displayed to the user");
        $orderHistory->setStateDate(new \DateTime());
        $orderHistory->setPriceIncludingVat($order->getPriceIncludingVat());
        $orderHistory->setPriceWithoutVat($order->getPriceWithoutVat());
        $order->addOrderHistory($orderHistory);
        $order->setStateFromHistory();

        // build transaction
        $transaction = new Transaction(
            $order->getId(),
            $order->getPriceIncludingVat(),
            new \DateTime(),
            "EUR",
            $order->getInvoiceUser()->getCountryCode()
        );
        $em->persist($transaction);
        $em->flush();

        // generate link
        $linkToPayment = $this->getPaymentSystem()->renderLinkToPayment($transaction);

        return $this->render(
            'KitpagesShopBundle:Order:displayOrder.html.twig',
            array(
                'order' => $order,
                'linkToPayment' => $linkToPayment
            )
        );

    }

    /**
     * @return \Ano\Bundle\PaymentBundle\PaymentSystem\CreditCardInterface
     */
    public function getPaymentSystem()
    {
        return $this->get($this->container->getParameter('payment.service'));
    }

}
