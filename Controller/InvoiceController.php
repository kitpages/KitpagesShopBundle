<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\Invoice;

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
        $em = $doctrine->getManager();
        $repo = $em->getRepository("KitpagesShopBundle:Order");
        $order = $repo->find($orderId);
        if (! $order instanceof Order) {
            throw new \Exception("InvoiceController : unknown order for orderId=".$orderId);
        }
        if ($order->getState() != OrderHistory::STATE_PAYED) {
            throw new \Exception("InvoiceController : order is not payed for orderId=".$orderId);
        }
        if (
            ! $this->get('security.context')->isGranted('ROLE_SHOP_ADMIN') &&
            $order->getUsername() != $this->get('security.context')->getToken()->getUsername()
        ) {
            $response = new Response('You are not allowed to see this order', '403');
            return $response;
        }
        $invoice = $order->getInvoice();
        $response = new Response($invoice->getContentHtml());
        return $response;

    }


}
