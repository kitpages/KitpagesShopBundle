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

class CartLineTest extends \PHPUnit_Framework_TestCase
{
    public function testCartline()
    {
        $cartLine = new CartLine();
        $cartLine->setId(12);
        $this->assertEquals(12, $cartLine->getId());
    }
}
