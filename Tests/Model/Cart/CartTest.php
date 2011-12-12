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
use Kitpages\ShopBundle\Model\Cart\CartableInterface;
use Kitpages\ShopBundle\Tests\Model\Cart\Cartable;

abstract class Cartable implements CartableInterface {}

class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testAddLine()
    {
        // mock
        $cartable = $this->getMockForAbstractClass('Kitpages\ShopBundle\Tests\Model\Cart\Cartable');
        $cartable->expects($this->any())
            ->method("getShopReference")
            ->will($this->returnValue('ref1'));
        $cart = new Cart();
        $line1 = $cart->addLine($cartable, 1, null);
        $this->assertTrue($line1 instanceof CartLine);
        $this->assertEquals(1, $line1->getId());

        $line2 = $cart->addLine($cartable, 5, null);
        $this->assertEquals(5, $line2->getQuantity());

        $line3 = $cart->addLine($cartable, 2, null);

        $line4 = $cart->addLine($cartable, 1, $line3);
        $line5 = $cart->addLine($cartable, 1);

        $this->assertEquals(2, $line2->getId());
        $this->assertEquals(3, $line3->getId());
        $this->assertEquals(4, $line4->getId());
        $this->assertEquals(5, $line5->getId());

        $this->assertEquals(5, count($cart->getLineList()));

        $cart->deleteLine($line3->getId());

        $this->assertEquals(3, count($cart->getLineList()));

        $line5 = $cart->getLine(5);
        $this->assertEquals(5, $line5->getId());

        $cart->emptyCart();
        $lineList = $cart->getLineList();
        $this->assertEquals(0, count($lineList));
        $line6 = $cart->addLine($cartable, 1, null);
        $this->assertEquals(6, $line6->getId());


    }
}
