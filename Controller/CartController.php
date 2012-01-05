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
                "shopData" => $line->getCartable()->getShopData(),
                "quantity" => $line->getQuantity(),
                "price" => $cartManager->getLinePrice($line->getId()),
                "deleteLinkUrl" => $this->generateUrl(
                    "KitpagesShopBundle_cart_deleteLine",
                    array(
                        "lineId"=>$line->getId(),
                        "kitpages_shop_target_url" => $_SERVER["REQUEST_URI"]
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

    /**
     * remove a line from the cart and redirect to the target URL
     * @param int $lineId
     */
    public function deleteLineAction($lineId)
    {
        $targetUrl = $this->getRequest()->query->get('kitpages_shop_target_url');
        $cartManager = $this->get('kitpages_shop.cartManager');
        $cart = $cartManager->getCart();
        $cart->deleteLine($lineId);
        return $this->redirect($targetUrl);
    }
}
