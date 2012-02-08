<?php
namespace Kitpages\ShopBundle\Model\Discount;

use Kitpages\ShopBundle\Model\Discount\DiscountInterface;

class DiscountFreeProduct
    implements DiscountInterface
{
    /** @var int */
    protected $productCountToActivate = 5;
    /** @var bool */
    protected $multipleApply = true;

    /**
     * @param boolean $multipleApply
     */
    public function setMultipleApply($multipleApply)
    {
        $this->multipleApply = $multipleApply;
    }

    /**
     * @return boolean
     */
    public function getMultipleApply()
    {
        return $this->multipleApply;
    }

    /**
     * @param int $productCountToActivate
     */
    public function setProductCountToActivate($productCountToActivate)
    {
        $this->productCountToActivate = $productCountToActivate;
    }

    /**
     * @return int
     */
    public function getProductCountToActivate()
    {
        return $this->productCountToActivate;
    }


    /**
     * reference of the product in the shop (must be unique in the shop)
     * @return string reference
     */
    public function getShopReference()
    {
        return "d-free-product";
    }

    /**
     * name printed in the cart
     * @return string name of the line in the cart
     */
    public function getShopName()
    {
        return "Discount 1 free for ".$this->productCountToActivate." products";
    }

    /**
     * description printed in the commande and in the invoice
     * @return string description of the line in the cart
     */
    public function getShopDescription()
    {
        return "One product free for ".$this->productCountToActivate." products bought.";
    }

    /**
     * extra data saved by serialization in the database
     * @return array elements given to the cart and serialized in the order
     */
    public function getShopData()
    {
        return array();
    }

    /**
     * extra data saved by serialization in the database
     * @return string name of the product category
     */
    public function getShopCategory()
    {
        return "discount";
    }



}