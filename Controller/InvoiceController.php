<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\Invoice;
use Kitano\PaymentBundle\Model\Transaction;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;



class InvoiceController extends Controller
{
    public function invoiceDisplayAction($orderId)
    {
        if (
            ! $this->get('security.context')->isGranted('ROLE_SHOP_USER')
        ) {
            return new Response('The user should be authenticated on this page');
        }
        $doctrine = $this->get("doctrine");
        $em = $doctrine->getEntityManager();
        $repo = $em->getRepository("KitpagesShopBundle:Order");
        $order = $repo->find($orderId);
        if (! $order instanceof Order) {
            throw new Exception("InvoiceController : unknown order for orderId=".$orderId);
        }
        if ($order->getState() != OrderHistory::STATE_PAYED) {
            throw new Exception("InvoiceController : order is not payed for orderId=".$orderId);
        }
        if ($order->getUsername() != $this->get('security.context')->getToken()->getUsername() ) {
            $response = new Response('You are not allowed to see this order', '403');
            return $response;
        }
        $invoice = $order->getInvoice();
        $response = new Response($invoice->getContentHtml());
        return $response;

    }

    public function displayOrderAction(
        Order $order,
        OrderUser $invoiceUser = null,
        OrderUser $shippingUser = null
    )
    {
        if (
            ! $this->get('security.context')->isGranted('ROLE_SHOP_USER')
        ) {
            return new Response('The user should be authenticated on this page');
        }

        if (
            ! $this->get('security.context')->isGranted('ROLE_SHOP_ADMIN') &&
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

        // calculate VAT
        $orderManager = $this->get('kitpages_shop.orderManager');
        $orderManager->addVat($order);

        // complete order
        $orderHistory = new OrderHistory();
        $orderHistory->setUsername($this->get('security.context')->getToken()->getUsername());
        $orderHistory->setOrder($order);
        $orderHistory->setState(OrderHistory::STATE_READY_TO_PAY);
        $orderHistory->setNote("order complete and displayed to the user");
        $orderHistory->setStateDate(new \DateTime());
        $orderHistory->setPriceIncludingVat($order->getPriceIncludingVat());
        $orderHistory->setPriceWithoutVat($order->getPriceWithoutVat());
        $order->addOrderHistory($orderHistory);
        $em->flush(); // hack in order to have an id in the orderHistory...
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
