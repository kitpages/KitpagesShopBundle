<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderUser;

use Kitano\PaymentBundle\Entity\Transaction;

use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $order->setLocale($this->get('request')->getLocale());
        if(
            $this->get('security.context')->isGranted('ROLE_SHOP_USER')
        ) {
            $order->setUsername($this->get('security.context')->getToken()->getUsername());
        }
        // persist order
        $em = $this->getDoctrine()->getManager();
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
        Request $request,
        $orderId,
        OrderUser $invoiceUser = null,
        OrderUser $shippingUser = null
    )
    {

        if (
            ! $this->get('security.context')->isGranted('ROLE_SHOP_USER')
        ) {
            return $this->forward('KitpagesShopBundle:Order:forbidden', array(
                'kitpages_target' => $this->generateUrl(
                    'KitpagesShopBundle_order_forbidden',
                    array('orderId'=> $orderId)
                )
            ));
        }

        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository("KitpagesShopBundle:Order")->find($orderId);

        if (
            ($order->getUsername() != null) &&
            ($order->getUsername() != $this->get('security.context')->getToken()->getUsername())
        ) {
            return new Response('You are not allowed to see this order');
        }

        // modify ready_to_pay or created orders (and not payed or canceled orders)
        if (
            ($order->getState() == OrderHistory::STATE_READY_TO_PAY) ||
            ($order->getState() == OrderHistory::STATE_CREATED)
        ) {

            $em = $this->getDoctrine()->getManager();

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
            $em->flush();
            $form = $this->get('kitpages_shop.paymentManager')->getChoosePaymentForm($order);

            if ('POST' === $request->getMethod()) {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $this->get("payment.plugin_controller")->createPaymentInstruction($instruction = $form->getData());

                    $order->setPaymentInstruction($instruction);
                    $this->get("doctrine.orm.entity_manager")->persist($order);
                    $this->get("doctrine.orm.entity_manager")->flush($order);

                    return new RedirectResponse($this->get('router')->generate('KitpagesShopBundle_payment_complete', array(
                        'orderId' => $order->getId(),
                    )));
                }
            }

            return $this->render(
                'KitpagesShopBundle:Order:displayOrder.html.twig',
                array(
                    'order' => $order,
                    'form' => $form->createView()
                )
            );
        }
        // don't touch payed or canceled order
        if (
            ($order->getState() == OrderHistory::STATE_PAYED) ||
            ($order->getState() == OrderHistory::STATE_CANCELED)
        ) {
            return $this->render(
                'KitpagesShopBundle:Order:displayOrder.html.twig',
                array(
                    'order' => $order
                )
            );
        }
    }

    public function termsAction()
    {
        return $this->render(
            'KitpagesShopBundle:Order:terms.html.twig'
        );
    }

    public function forbiddenAction()
    {
        return $this->render(
            'KitpagesShopBundle:Order:forbidden.html.twig'
        );
    }

}
