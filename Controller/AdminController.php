<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kitpages\ShopBundle\Model\Paginator;
use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderUser;
use Kitpages\ShopBundle\Form\FilterForm;

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


        $filterCheckBoxAllow = array(
            "order_state" => array(
                'query' => 'o.state =',
                'label' => 'state',
                'value' => array(
                    OrderHistory::STATE_PAYED => OrderHistory::STATE_PAYED,
                    OrderHistory::STATE_CREATED => OrderHistory::STATE_CREATED,
                    OrderHistory::STATE_READY_TO_PAY => OrderHistory::STATE_READY_TO_PAY,
                    OrderHistory::STATE_CANCELED => OrderHistory::STATE_CANCELED,
                )

            ),
            "order_priceWithoutVat" =>  array(
                'query' => 'o.price_without_vat >',
                'label' => ' ',
                'value' => array(
                    '0' => 'price ET not null'
                )
            )
        );

        $request = $this->get('request');

        $conn = $this->get('databaseConnection');

//        $logger = new \Doctrine\DBAL\Logging\EchoSQLLogger();
//        $conn->getConfiguration()->setSQLLogger($logger);


        // build basic form
        $builder = $this->createFormBuilder(null, array('csrf_protection'=>false));
        $builder->add(
            'filter',
            'text',
            array(
                'required' => false
            )
        );
        foreach($filterCheckBoxAllow as $key => $filterCheckBox) {
            $builder->add(
                $key,
                'choice',
                array(
                    'label' => $filterCheckBox['label'],
                    'choices' => $filterCheckBox['value'],
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true
                )
            );
        }

        // get form
        $form = $builder->getForm();

        $queryWhereString = '';
        $parameterList = array();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if($form->isValid()) {
                $dataForm = $request->get('form');
                $filterSearch = trim($dataForm['filter']);
                $querySearchString = null;
                if ($filterSearch != null) {
                    $searchFieldList = array(
                        'o.state',
                        'o.username',
                        'o.price_without_vat',
                        'o.price_including_vat',
                        'o.locale',
                        'i.reference',
                        'iu.first_name',
                        'iu.last_name',
                        'iu.address',
                        'iu.zip_code',
                        'iu.city',
                        'iu.state',
                        'iu.email',
                        'iu.phone',
                        'su.first_name',
                        'su.last_name',
                        'su.address',
                        'su.zip_code',
                        'su.city',
                        'su.state',
                        'su.email',
                        'su.phone',
                        'ol.shop_name',
                        'ol.shop_reference',
                        'ol.shop_description',
                        'o.createdAt',
                        'o.updatedAt',
                        'o.state_date'
                    );
                    $querySearchString = $this->searchSelect($filterSearch, $searchFieldList);
                }
                $countParam = 0;
                $queryCheckBoxList = array();
                foreach($filterCheckBoxAllow as $key => $filterCheckBox) {
                    if (isset($dataForm[$key])) {
                        $queryCheckBox = null;
                        foreach($dataForm[$key] as $value) {
                            $countParam++;
                            $queryCheckBox[] = $filterCheckBox['query']." :param".$countParam;
                            $parameterList["param".$countParam] = $value;
                        }
                        if ($queryCheckBox != null) {
                            $queryCheckBoxList[$key] = '('.implode(' OR ', $queryCheckBox).')';
                        }
                    }
                }

                if ($querySearchString != null) {
                    $queryWhereString = $querySearchString;
                }
                if (count($queryCheckBoxList) > 0) {
                    if ($queryWhereString != null) {
                        $queryWhereString .= ' AND ';
                    }
                    $queryWhereString .= implode(' AND ', $queryCheckBoxList);
                }
                if ($queryWhereString != null) {
                    $queryWhereString = ' WHERE '.$queryWhereString;
                }
            }
        }

        $queryString = 'FROM shop_order o
                        LEFT JOIN shop_order_line ol ON ol.order_id = o.id
                        LEFT JOIN shop_order_user iu ON iu.id = o.invoice_user_id
                        LEFT JOIN shop_invoice i ON i.id = o.invoice_id
                        LEFT JOIN shop_order_user su ON su.id = o.shipping_user_id
                        '.$queryWhereString.'
                        GROUP BY o.id';

        $queryOrderList = ' SELECT
                        o.id as id,
                        o.price_without_vat as priceWithoutVat,
                        o.price_including_vat as priceIncludingVat,
                        o.state as state,
                        o.state_date as stateDate,
                        iu.id as iu_id,
                        iu.first_name as iu_firstName,
                        iu.last_name as iu_lastName
                        '.$queryString.'
                        ORDER BY o.state_date DESC ';
        $queryCount = 'SELECT o.id '.$queryString;


        // init paginator
        $paginator = new Paginator();
        $paginator->setCurrentPage( $this->get('request')->query->get('news_page', 1) );
        $paginator->setItemCountPerPage( 20 );
        $paginator->setUrlTemplate(
            $this->generateUrl(
                "KitpagesShopBundle_admin_orderList",
                array(
                    'news_page' => '_PAGE_'
                )
            )
        );




        $stmt = $conn->executeQuery($queryCount, $parameterList);
        $resultCount=$stmt->fetchAll();

        $totalCount = count($resultCount);
        $paginator->setTotalItemCount($totalCount);

        if ($paginator->getSqlLimitOffset() != null) {
            $queryOrderList .= 'LIMIT '.$paginator->getSqlLimitOffset();
            if ($paginator->getItemCountPerPage() !== null) {
                $queryOrderList .= ', '.$paginator->getItemCountPerPage();
            }
        }


        $rsm = new \Doctrine\ORM\Query\ResultSetMapping;
        $rsm->addEntityResult('Kitpages\ShopBundle\Entity\Order', 'o');
        $rsm->addFieldResult('o', 'id', 'id');
        $rsm->addFieldResult('o', 'priceIncludingVat', 'priceIncludingVat');
        $rsm->addFieldResult('o', 'priceWithoutVat', 'priceWithoutVat');
        $rsm->addFieldResult('o', 'state', 'state');
        $rsm->addFieldResult('o', 'stateDate', 'stateDate');

        $rsm->addJoinedEntityResult('Kitpages\ShopBundle\Entity\OrderUser' , 'iu', 'o', 'invoiceUser');
        $rsm->addFieldResult('iu', 'iu_id', 'id');
        $rsm->addFieldResult('iu', 'iu_firstName', 'firstName');
        $rsm->addFieldResult('iu', 'iu_lastName', 'lastName');


        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createNativeQuery($queryOrderList, $rsm);
        $query->setParameters($parameterList);
        $orderList = $query->getResult();
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
                'form' => $form->createView(),
                'filterCheckBoxAllow' => $filterCheckBoxAllow
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




    public function searchSelect($searchString, $fieldList) {
        $sql = $this->_searchPerform($searchString,$fieldList);
        return $sql;
    }

    private function _searchSplitTerms($terms){
        $terms = preg_replace_callback("/\"(.*?)\"/", array($this,"_searchTransformTerm"), $terms);
        $terms = preg_split("/\s+|,/", $terms);
        $out = array();
        foreach($terms as $term){
            $term = preg_replace("/\{WHITESPACE-([0-9]+)\}/e", "chr(\$1)", $term);
            $term = preg_replace("/\{COMMA\}/", ",", $term);
            $out[] = $term;
        }
        return $out;
    }

    private function _searchTransformTerm($matches){
        $term = $matches[1];
        $term = preg_replace("/(\s)/e", "'{WHITESPACE-'.ord('\$1').'}'", $term);
        $term = preg_replace("/,/", "{COMMA}", $term);
        return $term;
    }

    private function _searchEscapeRlike($string){
        return preg_replace("/([.\[\]*^\$])/", '\\\$1', $string);
    }

    private function _searchDbEscapeTerms($terms){
        $out = array();
        foreach($terms as $term){
            //$out[] = '[[:<:]].*'.addSlashes($this->_searchEscapeRlike($term)).'.*[[:>:]]';
            $out[] = '[[:<:]]'.addSlashes($this->_searchEscapeRlike($term)).'[[:>:]]';
        }
        return $out;
    }
    private function _searchPerform($terms, $fieldList){
        $terms = $this->_searchSplitTerms($terms);
        $termsDb = $this->_searchDbEscapeTerms($terms);
        $parts = array();
        foreach($termsDb as $termDb){
            $subPart = array();
            foreach ($fieldList as $field) {
                $subPart[] = "$field RLIKE '$termDb'";
            }
            $parts[] = "(".implode(" OR ",$subPart).")";
        }
        $sql = "(".implode(' AND ', $parts).")";
        return $sql;
    }


}
