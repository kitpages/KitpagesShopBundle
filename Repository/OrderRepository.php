<?php
namespace Kitpages\ShopBundle\Repository;
use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    public function findByUserIdAndState($userId, $state)
    {
        $listOrder = $this->_em
            ->createQuery('
                SELECT o
                FROM KitpagesShopBundle:Order o
                JOIN o.invoiceUser iu
                WHERE iu.userId = :userId
                AND o.state = :state
            ')
            ->setParameter("userId", $userId)
            ->setParameter("state", $state)
            ->getResult();
        return $listOrder;
    }
}
