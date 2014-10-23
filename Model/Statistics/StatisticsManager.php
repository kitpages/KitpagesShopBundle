<?php
namespace Kitpages\ShopBundle\Model\Statistics;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Kitpages\ShopBundle\Entity\OrderHistory;

class StatisticsManager
{

    public function __construct(
        Registry $doctrine,
        $database_connection
    )
    {
        $this->doctrine = $doctrine;
        $this->database_Connection = $database_connection;
    }

    public function sales(
        $parameterSelectList = array(),
        $parameterWhereList = array(),
        $parameterGroupByList = array(),
        $parameterOrderByList = array()
    )
    {
        $parameterWhereListIni = array(
            'state' => OrderHistory::STATE_PAYED,
            'stateDateStart' => null,
            'stateDateEnd' => null,
            'shopReferenceList' => null,
            'invoideUserIdList' => null,
        );

        $parameterWhereList = array_merge($parameterWhereListIni, $parameterWhereList);

        $parameterList = array();
        $parameterTypeList = array();
        $queryGroupByList = array();
        $queryOrderByList = array();
        $querySelectList = array();
        $queryJoinList = array();
        $queryWhereList = array();

        foreach($parameterSelectList as $parameterSelect) {
            if($parameterSelect == 'shopCategory') {
                $queryJoinList['orderLine'] = " JOIN shop_order_line ol ON ol.order_id = o.id ";
                $querySelectList[] = "ol.shop_category as shopCategory";
            }
            if($parameterSelect == 'shopName') {
                $queryJoinList['orderLine'] = " JOIN shop_order_line ol ON ol.order_id = o.id ";
                $querySelectList[] = "ol.shop_name as shopName";
            }
            if($parameterSelect == 'shopReferenceQantity') {
                $queryJoinList['orderLine'] = " JOIN shop_order_line ol ON ol.order_id = o.id ";
                $querySelectList[] = "SUM(ol.quantity) as shopReferenceQantity";
            }
            if($parameterSelect == 'orderPriceWithoutVatTotal') {
                $querySelectList[] = "SUM(o.price_without_vat) orderPriceWithoutVatTotal";
            }
            if($parameterSelect == 'orderLinePriceWithoutVatTotal') {
                $queryJoinList['orderLine'] = " JOIN shop_order_line ol ON ol.order_id = o.id ";
                $querySelectList[] = "SUM(ol.price_without_vat) orderLinePriceWithoutVatTotal";
            }
            if($parameterSelect == 'priceIncludingVatTotal') {
                $querySelectList[] = "SUM(o.price_including_vat) priceTotalIncludingVat";
            }
        }

        foreach($parameterGroupByList as $parameterGroupBy => $valueGroupBy) {
            if($parameterGroupBy == 'stateDate') {
                $querySelectList[] = "DATE_FORMAT(o.state_date, '".$valueGroupBy."') as stateDate";
                $queryGroupByList[] = "stateDate";
            }
            if($parameterGroupBy == 'shopReference') {
                $queryJoinList['orderLine'] = " JOIN shop_order_line ol ON ol.order_id = o.id ";
                $querySelectList[] = "ol.shop_reference as shopReference";
                $queryGroupByList[] = "shopReference";
            }
            if($parameterGroupBy == 'shopCategory') {
                $queryJoinList['orderLine'] = " JOIN shop_order_line ol ON ol.order_id = o.id ";
                $querySelectList[] = "ol.shop_category as shopCategory";
                $queryGroupByList[] = "shopCategory";
            }
        }

        foreach($parameterOrderByList as $parameterOrderBy => $direction) {
            $queryOrderByList[] = $parameterOrderBy." ".$direction;
        }

        $conn = $this->database_Connection;
//        $logger = new \Doctrine\DBAL\Logging\EchoSQLLogger();
//        $conn->getConfiguration()->setSQLLogger($logger);



        if ($parameterWhereList['invoideUserIdList'] !== null) {
            if (!is_array($parameterWhereList['invoideUserIdList']) ) {
                $parameterWhere['invoideUserIdList'] = array($parameterWhereList['invoideUserIdList']);
            }
            $queryJoinList['invoiceUser'] = " JOIN shop_invoice iu ON iu.id = o.invoice_user_id ";
            $queryWhereList[] = " iu.id IN (?) ";
            $parameterList[] = $parameterWhereList['invoideUserIdList'];
            $parameterTypeList[] = \Doctrine\DBAL\Connection::PARAM_INT_ARRAY;
        }
        if ($parameterWhereList['stateDateStart'] !== null) {
            $queryWhereList[] = " o.state_date >= ? ";
            $parameterList[] = $parameterWhereList['stateDateStart'];
            $parameterTypeList[] = 'datetime';
        }
        if ($parameterWhereList['stateDateEnd'] !== null) {
            $queryWhereList[] = " o.state_date < ? ";
            $parameterList[] = $parameterWhereList['stateDateEnd'];
            $parameterTypeList[] = 'datetime';
        }

        if ($parameterWhereList['state'] !== null) {
            $queryWhereList[] = " o.state = ? ";
            $parameterList[] = $parameterWhereList['state'];
            $parameterTypeList[] = \PDO::PARAM_STR;
        }

        if ($parameterWhereList['shopReferenceList'] !== null) {
            if (!is_array($parameterWhereList['shopReferenceList']) ) {
                $parameterWhereList['shopReferenceList'] = array($parameterWhereList['shopReferenceList']);
            }
            $queryJoinList['orderLine'] = " JOIN shop_order_line ol ON ol.order_id = o.id ";
            $queryWhereList[] = " ol.shopReference in (?) ";
            $parameterList[] = $parameterWhereList['shopReferenceList'];
            $parameterTypeList[] = \Doctrine\DBAL\Connection::PARAM_INT_ARRAY;
        }

        $queryString = "
        SELECT ".
            implode(',', $querySelectList)."
        FROM shop_order o ".
        implode(' ', $queryJoinList);

        if(count($queryWhereList) > 0) {
            $queryString .= " WHERE ".implode(' AND ', $queryWhereList);
        }

        if(count($queryGroupByList) > 0) {
            $queryString .= " GROUP BY ".implode(',', $queryGroupByList);
        }

        if(count($queryOrderByList) > 0) {
            $queryString .= " ORDER BY ".implode(',', $queryOrderByList);
        }

        $stmt = $conn->executeQuery($queryString, $parameterList, $parameterTypeList);
        $result=$stmt->fetchAll();
        return $result;
    }


}