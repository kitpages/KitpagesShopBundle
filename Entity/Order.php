<?php
namespace Kitpages\ShopBundle\Entity;

class Order
{

    /**
     * @var string $randomKey
     */
    private $randomKey;

    /**
     * @var string $state
     */
    private $state;

    /**
     * @var datetime $stateDate
     */
    private $stateDate;

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
     * @var Kitpages\CmsBundle\Entity\Invoice
     */
    private $invoice;

    /**
     * @var Kitpages\CmsBundle\Entity\OrderUser
     */
    private $invoiceUser;

    /**
     * @var Kitpages\CmsBundle\Entity\OrderUser
     */
    private $shippingUser;

    /**
     * @var Kitpages\CmsBundle\Entity\OrderHistory
     */
    private $orderHistoryList;

    /**
     * @var Kitpages\ShopBundle\Entity\OrderLine
     */
    private $orderLineList;

    public function __construct()
    {
        $this->orderHistoryList = new \Doctrine\Common\Collections\ArrayCollection();
    $this->orderLineList = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set randomKey
     *
     * @param string $randomKey
     */
    public function setRandomKey($randomKey)
    {
        $this->randomKey = $randomKey;
    }

    /**
     * Get randomKey
     *
     * @return string 
     */
    public function getRandomKey()
    {
        return $this->randomKey;
    }

    /**
     * Set state
     *
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set stateDate
     *
     * @param datetime $stateDate
     */
    public function setStateDate($stateDate)
    {
        $this->stateDate = $stateDate;
    }

    /**
     * Get stateDate
     *
     * @return datetime 
     */
    public function getStateDate()
    {
        return $this->stateDate;
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
     * Set invoice
     *
     * @param Kitpages\CmsBundle\Entity\Invoice $invoice
     */
    public function setInvoice(\Kitpages\CmsBundle\Entity\Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get invoice
     *
     * @return Kitpages\CmsBundle\Entity\Invoice 
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set invoiceUser
     *
     * @param Kitpages\CmsBundle\Entity\OrderUser $invoiceUser
     */
    public function setInvoiceUser(\Kitpages\CmsBundle\Entity\OrderUser $invoiceUser)
    {
        $this->invoiceUser = $invoiceUser;
    }

    /**
     * Get invoiceUser
     *
     * @return Kitpages\CmsBundle\Entity\OrderUser 
     */
    public function getInvoiceUser()
    {
        return $this->invoiceUser;
    }

    /**
     * Set shippingUser
     *
     * @param Kitpages\CmsBundle\Entity\OrderUser $shippingUser
     */
    public function setShippingUser(\Kitpages\CmsBundle\Entity\OrderUser $shippingUser)
    {
        $this->shippingUser = $shippingUser;
    }

    /**
     * Get shippingUser
     *
     * @return Kitpages\CmsBundle\Entity\OrderUser 
     */
    public function getShippingUser()
    {
        return $this->shippingUser;
    }

    /**
     * Add orderHistoryList
     *
     * @param Kitpages\CmsBundle\Entity\OrderHistory $orderHistoryList
     */
    public function addOrderHistory(\Kitpages\CmsBundle\Entity\OrderHistory $orderHistoryList)
    {
        $this->orderHistoryList[] = $orderHistoryList;
    }

    /**
     * Get orderHistoryList
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOrderHistoryList()
    {
        return $this->orderHistoryList;
    }

    /**
     * Add orderLineList
     *
     * @param Kitpages\ShopBundle\Entity\OrderLine $orderLineList
     */
    public function addOrderLine(\Kitpages\ShopBundle\Entity\OrderLine $orderLineList)
    {
        $this->orderLineList[] = $orderLineList;
    }

    /**
     * Get orderLineList
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOrderLineList()
    {
        return $this->orderLineList;
    }
}