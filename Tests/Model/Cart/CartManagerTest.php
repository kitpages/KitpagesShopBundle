<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kitpages\ShopBundle\Tests\Model\Cart;

use Kitpages\ShopBundle\Model\Cart\CartLine;
use Kitpages\ShopBundle\Model\Cart\Cart;
use Kitpages\ShopBundle\Model\Cart\CartManager;
use Kitpages\ShopBundle\Model\PriceFactory\PriceFactory;
use Kitpages\ShopBundle\Model\Cart\CartableInterface;
use Kitpages\ShopBundle\Model\Cart\ProductInterface;
use Kitpages\ShopBundle\Tests\Model\Cart\Cartable;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\SessionStorage\ArraySessionStorage;

abstract class Product implements ProductInterface {}
//abstract class Cartable implements CartableInterface {}

class CartManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCartManager()
    {
        // mock
        $cartable = $this->getMockForAbstractClass('Kitpages\ShopBundle\Tests\Model\Cart\Cartable');
        $cartable->expects($this->any())
            ->method("getShopReference")
            ->will($this->returnValue('ref_cartable'));

        $product = $this->getMockForAbstractClass('Kitpages\ShopBundle\Tests\Model\Cart\Product');
        $product->expects($this->any())
            ->method("getShopReference")
            ->will($this->returnValue('ref_product'));
        $product->expects($this->any())
            ->method("getShopUnitPrice")
            ->will($this->returnValue(12.5));
        $product->expects($this->any())
            ->method("getShopWeight")
            ->will($this->returnValue(5.2));

        $session = new Session(new ArraySessionStorage());

        // get cart
        $cartManager = new CartManager($session);
        $cart = $cartManager->getCart();

        $line1 = $cart->addLine($cartable, 1, null);
        $line2 = $cart->addLine($product, 5, null);
        $line3 = $cart->addLine($cartable, 2, null);
        $line4 = $cart->addLine($product, 1, $line3);
        $line5 = $cart->addLine($cartable, 1);

        $this->assertEquals($cartManager->getProductCount(),6);
        $this->assertEquals($cartManager->getTotalPrice(),75.0);
        $this->assertEquals($cartManager->getWeight(),31.2);
    }
}
