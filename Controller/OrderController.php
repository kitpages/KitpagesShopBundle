<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class OrderController extends Controller
{
    public function createOrderAction($name)
    {
        return $this->render('KitpagesShopBundle:Default:index.html.twig', array('name' => $name));
    }
}
