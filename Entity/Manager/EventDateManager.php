<?php

namespace Eluceo\EventBundle\Entity\Manager;

use Eluceo\EventBundle\Model\Manager\EventDateManagerInterface;
use Knp\Component\Pager\Paginator;

class EventDateManager implements EventDateManagerInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Knp\Component\Pager\Paginator
     */
    protected $paginator;

    public function __construct(\Doctrine\ORM\EntityManager $em, $class, Paginator $paginator)
    {
        $this->em        = $em;
        $this->class     = $class;
        $this->paginator = $paginator;
    }

    /**
     * @param  array                                        $filters
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findByFilters(array $filters)
    {
        return $this->getQueryWithFilters($filters)->getResult();
    }

    protected function getQueryWithFilters(array $filters)
    {
        $qb = $this->em->getRepository($this->class)->createQueryBuilder('ed');

        $qb->leftJoin('ed.event', 'e');

        $filters = self::normalizeFilters($filters);

        if (null != @$filters['active']) {
            $qb
                ->where('e.active = :active AND ed.active = :active')
                ->setParameter('active', $filters['active'] ? 1 : 0);
        }

        // Date From
        if (@$filters['date_from']) {
            $qb
                ->andWhere('ed.startDatetime >= :date_from OR ed.endDatetime >= :date_from')
                ->setParameter('date_from', $filters['date_from']);
        }

        // Date To
        if (@$filters['date_to']) {
            $qb->andWhere('ed.startDatetime <= :date_to OR ed.endDateTime <= :date_to')
                ->setParameter('date_to', $filters['date_to']);
        }

        // Gallery Only
        if (@$filters['gallery_only']) {
            $qb->andWhere('e.gallery IS NOT NULL');
        }

        // Filter Categories
        if (@$filters['categories']) {
            $qb->leftJoin('e.categories', 'c')
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $filters['categories']);
        }

        // Order
        if (@$filters['order_by']) {
            $qb->orderBy($filters['order_by'], $filters['order']);
        }

        return $qb->getQuery();
    }

    /**
     * @param  array                                               $filters
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPaginationWithFilters(array $filters)
    {
        $filters    = self::normalizeFilters($filters);
        $query      = $this->getQueryWithFilters($filters);
        $pagination = $this->getPaginator()->paginate($query);

        return $pagination;
    }

    /**
     * @return \Knp\Component\Pager\Paginator
     */
    protected function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * Normalizes Filter-Array
     *
     * @static
     * @param  array $filters
     * @return array
     */
    public static function normalizeFilters(array $filters)
    {
        $filters = array_merge(array(
            'date_from'     => date('Y-m-d'),
            'date_to'       => null,
            'categories'    => null,
            'order_by'      => 'ed.startDatetime',
            'order'         => 'ASC',
            'gallery_only'  => false,
            'active'        => true
        ), $filters);

        return $filters;
    }
}
