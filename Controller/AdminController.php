<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderUser;

class AdminController extends Controller
{
    /**
     * display the admin navigation
     * @param none
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function widgetNavigationAction()
    {
        return $this->render(
            'KitpagesShopBundle:Admin:navigation.html.twig'
        );
    }

    /**
     * display the order list
     * @param none
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function orderListAction()
    {
        $orderList = array();

        $em = $this->getDoctrine()->getEntityManager();
//        $orderList = $em->getRepository('KitpagesShopBundle:Order')->findByState(OrderHistory::STATE_PAYED);
        $orderList = $em
            ->createQuery('
                SELECT o
                FROM KitpagesShopBundle:Order o
                WHERE o.state = :state
                ORDER BY o.stateDate DESC
            ')
            ->setParameter("state", OrderHistory::STATE_PAYED)
            ->getResult();


        $displayOrderRoute = $this->container->getParameter('kitpages_shop.order_display_route_name');

        return $this->render(
            'KitpagesShopBundle:Admin:orderList.html.twig',
            array(
                'orderList' => $orderList,
                'displayOrderRoute' => $displayOrderRoute
            )
        );
    }

    /**
     * display the order list
     * @param none
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function orderHistoryAction(Order $order)
    {

        $orderHistoryList = $order->getOrderHistoryList();

        return $this->render(
            'KitpagesShopBundle:Admin:orderHistory.html.twig',
            array(
                'order' => $order,
                'orderHistoryList' => $orderHistoryList
            )
        );
    }

}
