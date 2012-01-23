<?php
namespace Kitpages\ShopBundle\Entity;

class OrderLine
{

    /**
     * @var integer $quantity
     */
    private $quantity;

    /**
     * @var integer $cartLineId
     */
    private $cartLineId;

    /**
     * @var integer $cartParentLineId
     */
    private $cartParentLineId;

    /**
     * @var string $shopReference
     */
    private $shopReference;

    /**
     * @var string $shopName
     */
    private $shopName;

    /**
     * @var string $shopDescription
     */
    private $shopDescription;

    /**
     * @var float $priceWithoutVat
     */
    private $priceWithoutVat;

    /**
     * @var float $priceIncludingVat
     */
    private $priceIncludingVat;

    /**
     * @var datetime $createdAt
     */
    private $createdAt;

    /**
     * @var datetime $updatedAt
     */
    private $updatedAt;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Kitpages\ShopBundle\Entity\Order
     */
    private $order;


    /**
     * Set quantity
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set cartLineId
     *
     * @param integer $cartLineId
     */
    public function setCartLineId($cartLineId)
    {
        $this->cartLineId = $cartLineId;
    }

    /**
     * Get cartLineId
     *
     * @return integer 
     */
    public function getCartLineId()
    {
        return $this->cartLineId;
    }

    /**
     * Set cartParentLineId
     *
     * @param integer $cartParentLineId
     */
    public function setCartParentLineId($cartParentLineId)
    {
        $this->cartParentLineId = $cartParentLineId;
    }

    /**
     * Get cartParentLineId
     *
     * @return integer 
     */
    public function getCartParentLineId()
    {
        return $this->cartParentLineId;
    }

    /**
     * Set shopReference
     *
     * @param string $shopReference
     */
    public function setShopReference($shopReference)
    {
        $this->shopReference = $shopReference;
    }

    /**
     * Get shopReference
     *
     * @return string 
     */
    public function getShopReference()
    {
        return $this->shopReference;
    }

    /**
     * Set shopName
     *
     * @param string $shopName
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;
    }

    /**
     * Get shopName
     *
     * @return string 
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * Set shopDescription
     *
     * @param string $shopDescription
     */
    public function setShopDescription($shopDescription)
    {
        $this->shopDescription = $shopDescription;
    }

    /**
     * Get shopDescription
     *
     * @return string 
     */
    public function getShopDescription()
    {
        return $this->shopDescription;
    }

    /**
     * Set priceWithoutVat
     *
     * @param float $priceWithoutVat
     */
    public function setPriceWithoutVat($priceWithoutVat)
    {
        $this->priceWithoutVat = $priceWithoutVat;
    }

    /**
     * Get priceWithoutVat
     *
     * @return float 
     */
    public function getPriceWithoutVat()
    {
        return $this->priceWithoutVat;
    }

    /**
     * Set priceIncludingVat
     *
     * @param float $priceIncludingVat
     */
    public function setPriceIncludingVat($priceIncludingVat)
    {
        $this->priceIncludingVat = $priceIncludingVat;
    }

    /**
     * Get priceIncludingVat
     *
     * @return float 
     */
    public function getPriceIncludingVat()
    {
        return $this->priceIncludingVat;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set order
     *
     * @param Kitpages\ShopBundle\Entity\Order $order
     */
    public function setOrder(\Kitpages\ShopBundle\Entity\Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return Kitpages\ShopBundle\Entity\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }
    /**
     * @var array $shopData
     */
    private $shopData;


    /**
     * Set shopData
     *
     * @param array $shopData
     */
    public function setShopData($shopData)
    {
        $this->shopData = $shopData;
    }

    /**
     * Get shopData
     *
     * @return array 
     */
    public function getShopData()
    {
        return $this->shopData;
    }
    /**
     * @var string $cartableClass
     */
    private $cartableClass;

    /**
     * @var string $shopCategory
     */
    private $shopCategory;


    /**
     * Set cartableClass
     *
     * @param string $cartableClass
     */
    public function setCartableClass($cartableClass)
    {
        $this->cartableClass = $cartableClass;
    }

    /**
     * Get cartableClass
     *
     * @return string 
     */
    public function getCartableClass()
    {
        return $this->cartableClass;
    }

    /**
     * Set shopCategory
     *
     * @param string $shopCategory
     */
    public function setShopCategory($shopCategory)
    {
        $this->shopCategory = $shopCategory;
    }

    /**
     * Get shopCategory
     *
     * @return string 
     */
    public function getShopCategory()
    {
        return $this->shopCategory;
    }
}