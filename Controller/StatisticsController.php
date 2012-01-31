<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kitpages\ShopBundle\Model\Paginator;
use Kitpages\ShopBundle\Entity\Order;
use Kitpages\ShopBundle\Entity\OrderHistory;
use Kitpages\ShopBundle\Entity\OrderUser;

class StatisticsController extends Controller
{
    /**
     * display the admin navigation
     * @param none
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function indexAction()
    {

        $request = $this->get('request');
        // build basic form
        $builder = $this->createFormBuilder(null);
        $builder->add(
            'date',
            'date',
            array(
                'input'  => 'timestamp',
                'widget' => 'choice',
                'data' => time(),
                'format' => 'YYYY-MMMM-dd',
            )
        );
        // get form
        $form = $builder->getForm();

        $date = new \DateTime("2012-01-12");

        $year = $date->format('Y');
        $month = $date->format('M');

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
             $form->bindRequest($request);

             if ($form->isValid()) {
                 $dataForm = $request->request->get('form');
                 $year = $dataForm['date']['year'];
                 $month = $dataForm['date']['month'];
             }
        }


        $statisticsManager = $this->get('kitpages_shop.statisticsManager');

        /************************************
        ********** sales per month **********
        *************************************/
        $dateStart = new \DateTime("$year-01-01 00:00:00");
        $dateEnd = new \DateTime("$year-01-01 00:00:00");
        $dateEnd = $dateEnd->add(\DateInterval::createFromDateString('1 year'));

        $queryWhere = array(
            'stateDateStart' => $dateStart,
            'stateDateEnd' => $dateEnd
        );
        $queryGroupBy = array(
            'stateDate' => '%Y-%m'
        );
        $queryOrderBy = array(
            'stateDate' => 'ASC'
        );

        $dataStatisticSalesPerMonth = $statisticsManager->sales(array(), $queryWhere, $queryGroupBy, $queryOrderBy);
        foreach($dataStatisticSalesPerMonth as $data) {
            $dataDate = new \DateTime($data['stateDate']);
            $dataStatisticList['salesPerMonth'][$dataDate->format('m')] = $data['priceTotalWithoutVat'];
        }

        /************************************
        **** sales per day for one month ****
        *************************************/
        $dateStart = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = $dateEnd->add(\DateInterval::createFromDateString('1 month'));

        $queryWhere = array(
            'stateDateStart' => $dateStart,
            'stateDateEnd' => $dateEnd
        );
        $queryGroupBy = array(
            'stateDate' => '%Y-%m-%d'
        );
        $queryOrderBy = array(
            'stateDate' => 'ASC'
        );

        $dataStatisticSalesPerDay = $statisticsManager->sales(array(), $queryWhere, $queryGroupBy, $queryOrderBy);

        foreach($dataStatisticSalesPerDay as $data) {
            $dataDate = new \DateTime($data['stateDate']);
            $dataStatisticList['salesPerDay'][$dataDate->format('d')] = $data['priceTotalWithoutVat'];
        }

        /************************************
        *** top ten products of the month ***
        *************************************/
        $dateStart = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = $dateEnd->add(\DateInterval::createFromDateString('1 month'));


        $querySelect = array(
            'shopName' => 'shopName',
            'shopReferenceQantity' => 'shopReferenceQantity'
        );
        $queryWhere = array(
            'stateDateStart' => $dateStart,
            'stateDateEnd' => $dateEnd
        );
        $queryGroupBy = array(
            'stateDate' => '%Y %m',
            'shopReference' => 'shopReference'
        );
        $queryOrderBy = array(
            'stateDate' => 'ASC'
        );

        $dataStatisticListSalesTopTen = $statisticsManager->sales($querySelect, $queryWhere, $queryGroupBy, $queryOrderBy);

        foreach($dataStatisticListSalesTopTen as $data) {
            $dataStatisticList['salesTopTen'][$data['shopName']] = $data['priceTotalWithoutVat'];
            $dataStatisticList['salesTopTenQuantity'][$data['shopName']] = $data['shopReferenceQantity'];
        }

        $other = array_slice($dataStatisticList['salesTopTen'], 11);
        $dataStatisticList['salesTopTen'] = array_slice($dataStatisticList['salesTopTen'], 0, 10);
        if (count($other) > 0) {
            $dataStatisticList['salesTopTen']['other'] = array_sum($other);
        }
        arsort($dataStatisticList['salesTopTen']);

        $other = array_slice($dataStatisticList['salesTopTenQuantity'], 11);
        $dataStatisticList['salesTopTenQuantity'] = array_slice($dataStatisticList['salesTopTenQuantity'], 0, 10);
        if (count($other) > 0) {
            $dataStatisticList['salesTopTenQuantity']['other'] = array_sum($other);
        }
        arsort($dataStatisticList['salesTopTenQuantity']);



        /************************************
        ************* sale by category ******
        *************************************/
        $dateStart = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = new \DateTime("$year-$month-01 00:00:00");
        $dateEnd = $dateEnd->add(\DateInterval::createFromDateString('1 month'));


        $queryWhere = array(
            'stateDateStart' => $dateStart,
            'stateDateEnd' => $dateEnd
        );
        $queryGroupBy = array(
            'stateDate' => '%Y %m',
            'shopCategory' => 'shopCategory'
        );
        $queryOrderBy = array(
            'stateDate' => 'ASC'
        );

        $dataStatisticListSalesPerCatehory = $statisticsManager->sales($querySelect, $queryWhere, $queryGroupBy, $queryOrderBy);

        foreach($dataStatisticListSalesPerCatehory as $data) {
            $dataStatisticList['salesPerCategory'][$data['shopCategory']] = $data['priceTotalWithoutVat'];
        }

        arsort($dataStatisticList['salesPerCategory']);





        return $this->render(
            'KitpagesShopBundle:Admin:statistic.html.twig',
            array(
                'form' => $form->createView(),
                'dataStatisticList' => $dataStatisticList
            )
        );
    }



}
