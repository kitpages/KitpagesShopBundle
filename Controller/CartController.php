<?php

namespace Kitpages\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kitpages\ShopBundle\Model\Cart\CartManagerInterface;
use Kitpages\ShopBundle\Model\Cart\CartInterface;


class CartController extends Controller
{
    /**
     * display the cart
     * @param string $size ('big'|'medium'|'small')
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function displayCartAction($size = "big")
    {
        $size = strtolower($size);
        if (!in_array($size, array('big', 'medium', 'small'))) {
            $size = "big";
        }
        $size = ucfirst($size);
        $cartManager = $this->get('kitpages_shop.cartManager');
        $cart = $cartManager->getCart();

//        $logger = $this->get('logger');
//        $logger->debug("display cart : cart=".print_r($cart,true));

        // build view object
        $displayCart = array(
            "totalPrice" => $cartManager->getTotalPrice(),
            "productCount" => $cartManager->getProductCount(),
            'weight' => $cartManager->getWeight(),
            'lineList' => array(
            )
        );
        foreach ($cart->getLineList() as $line) {
            $displayCart['lineList'][$line->getId()] = array(
                "shopName" => $line->getCartable()->getShopName(),
                "shopDescription" => $line->getCartable()->getShopDescription(),
                "shopReference" => $line->getCartable()->getShopReference(),
                "quantity" => $line->getQuantity(),
                "price" => $cartManager->getLinePrice($line->getId()),
                "deleteLinkUrl" => $this->generateUrl(
                    "KitpagesShopBundle_cart_deleteLine",
                    array(
                        "lineId"=>$line->getId()
                    )
                )
            );
        }
        return $this->render(
            'KitpagesShopBundle:Cart:display'.$size.'Cart.html.twig',
            array(
                'cart' => $displayCart,
                'cartModel' => $cart,
                'cartManagerModel' => $cartManager
            )
        );
    }
}
