<?php
namespace Kitpages\ShopBundle\Entity;

class OrderHistory
{

    /**
     * @var integer $userId
     */
    private $userId;

    /**
     * @var string $userLogin
     */
    private $userLogin;

    /**
     * @var string $state
     */
    private $state;

    /**
     * @var datetime $stateDate
     */
    private $stateDate;

    /**
     * @var string $note
     */
    private $note;

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
     * Set userId
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userLogin
     *
     * @param string $userLogin
     */
    public function setUserLogin($userLogin)
    {
        $this->userLogin = $userLogin;
    }

    /**
     * Get userLogin
     *
     * @return string 
     */
    public function getUserLogin()
    {
        return $this->userLogin;
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
     * Set note
     *
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
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
}