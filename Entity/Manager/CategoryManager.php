<?php

namespace Eluceo\EventBundle\Entity\Manager;

use Eluceo\EventBundle\Model\Manager\CategoryManagerInterface;

class CategoryManager implements CategoryManagerInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    public function findAll()
    {
        $qb = $this->em->getRepository($this->class)->createQueryBuilder('c');
        $qb
            ->select('c')
            ->orderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param  array                              $criteria
     * @return \Eluceo\EventBundle\Model\Category
     */
    public function findOneBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findOneBy($criteria);
    }
}
