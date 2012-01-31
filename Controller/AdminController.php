<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kitpages\ShopBundle\Model\Paginator;
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

        $request = $this->get('request');
        // build basic form
        $builder = $this->createFormBuilder(null, array('csrf_protection'=>false));
        $builder->add(
            'filter',
            'text',
            array(
                'required' => false
            )
        );
        // get form
        $form = $builder->getForm();

        $em = $this->getDoctrine()->getEntityManager();
        $filter = '';
        if ($request->getMethod() == 'GET') {
            $form->bindRequest($request);
            if($form->isValid()) {
                $dataForm = $request->get('form');
                $filter = trim($dataForm['filter']);
            }
        }
        $queryOrderList = $em
            ->createQuery('
                SELECT o
                FROM KitpagesShopBundle:Order o
                LEFT JOIN o.orderLineList ol
                LEFT JOIN o.invoiceUser iu
                LEFT JOIN o.invoice i
                LEFT JOIN o.shippingUser su
                WHERE  o.state LIKE :filter
                OR o.username LIKE :filter
                OR o.priceWithoutVat LIKE :filter
                OR o.priceIncludingVat LIKE :filter
                OR o.locale LIKE :filter
                OR i.reference LIKE :filter
                OR iu.firstName LIKE :filter
                OR iu.lastName LIKE :filter
                OR iu.address LIKE :filter
                OR iu.zipCode LIKE :filter
                OR iu.city LIKE :filter
                OR iu.state LIKE :filter
                OR iu.email LIKE :filter
                OR iu.phone LIKE :filter
                OR su.firstName LIKE :filter
                OR su.lastName LIKE :filter
                OR su.address LIKE :filter
                OR su.zipCode LIKE :filter
                OR su.city LIKE :filter
                OR su.state LIKE :filter
                OR su.email LIKE :filter
                OR su.phone LIKE :filter
                OR ol.shopName LIKE :filter
                OR ol.shopReference LIKE :filter
                OR ol.shopDescription LIKE :filter
                OR o.createdAt LIKE :filter
                OR o.updatedAt LIKE :filter
                OR o.stateDate LIKE :filter
                GROUP BY o.id
                ORDER BY o.stateDate DESC
            ')
            ->setParameter("filter", '%'.$filter.'%');
        $queryCount = $em
            ->createQuery('
                SELECT count(o.id)
                FROM KitpagesShopBundle:Order o
                LEFT JOIN o.orderLineList ol
                LEFT JOIN o.invoiceUser iu
                LEFT JOIN o.invoice i
                LEFT JOIN o.shippingUser su
                WHERE o.state LIKE :filter
                OR o.username LIKE :filter
                OR o.priceWithoutVat LIKE :filter
                OR o.priceIncludingVat LIKE :filter
                OR o.locale LIKE :filter
                OR i.reference LIKE :filter
                OR iu.firstName LIKE :filter
                OR iu.lastName LIKE :filter
                OR iu.address LIKE :filter
                OR iu.zipCode LIKE :filter
                OR iu.city LIKE :filter
                OR iu.state LIKE :filter
                OR iu.email LIKE :filter
                OR iu.phone LIKE :filter
                OR su.firstName LIKE :filter
                OR su.lastName LIKE :filter
                OR su.address LIKE :filter
                OR su.zipCode LIKE :filter
                OR su.city LIKE :filter
                OR su.state LIKE :filter
                OR su.email LIKE :filter
                OR su.phone LIKE :filter
                OR ol.shopName LIKE :filter
                OR ol.shopReference LIKE :filter
                OR ol.shopDescription LIKE :filter
                OR o.createdAt LIKE :filter
                OR o.updatedAt LIKE :filter
                OR o.stateDate LIKE :filter
                GROUP BY o.id
            ')
            ->setParameter("filter", '%'.$filter.'%');

        // init paginator
        $paginator = new Paginator();
        $paginator->setCurrentPage( $this->get('request')->query->get('news_page', 1) );
        $paginator->setItemCountPerPage( 20 );
        $paginator->setUrlTemplate(
            $this->generateUrl(
                "KitpagesShopBundle_admin_orderList",
                array(
                    'news_page' => '_PAGE_',
                    'form[filter]' => $filter
                )
            )
        );

        //$totalCount = $queryCount->getSingleScalarResult();
        $totalCount = count($queryCount->getResult());
        $paginator->setTotalItemCount($totalCount);

        if ($paginator->getItemCountPerPage() !== null) {
            $queryOrderList->setMaxResults($paginator->getItemCountPerPage());
        }
        if ($paginator->getSqlLimitOffset() != null) {
            $queryOrderList->setFirstResult($paginator->getSqlLimitOffset());
        }
//echo $queryOrderList->getSqlQuery();
        $orderList = $queryOrderList->getResult();

        $paginatorHtml = $this->renderView(
            "KitpagesShopBundle::pager.html.twig",
            array(
                'paginator' =>$paginator
            )
        );

        $displayOrderRoute = $this->container->getParameter('kitpages_shop.order_display_route_name');

        return $this->render(
            'KitpagesShopBundle:Admin:orderList.html.twig',
            array(
                'orderList' => $orderList,
                'displayOrderRoute' => $displayOrderRoute,
                'paginatorHtml' => $paginatorHtml,
                'form' => $form->createView()
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

    /**
     * display the order list
     * @param none
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function statisticAction(Order $order)
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
