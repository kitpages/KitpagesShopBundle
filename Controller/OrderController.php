<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class OrderController extends Controller
{
    public function createAction()
    {
        $cartManager = $this->get('kitpages_shop.cartManager');
        $cart = $cartManager->getCart();
        // create order from cart

        // redirect to the next page
        $displayOrderRoute
        return $this->redirect(
            $this->generateUrl($displayOrderRoute)
        );
    }
}
