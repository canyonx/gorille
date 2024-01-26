<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Tag;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 4;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Event $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Event $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Retourne les événements de la semaine numéro $value
     * 
     * @param value Numéro de semaine
     * @return Event[] Returns an array of Event objects
     */
    public function findByWeek(int $value): array
    {
        $today = new \DateTime("today");
        $week = date_format($today, "W");
        $lastMonday = new \DateTime("Monday this week +" . $value - $week . " week");
        $nextSunday = new \DateTime("Monday next week +" . $value - $week . " week");

        return $this->createQueryBuilder('e')
            // date > à lundi dernier 
            ->andWhere('e.dateAt >= :val')
            ->setParameter('val', $lastMonday)
            // date < à lundi prochain
            ->andWhere('e.dateAt <= :val1')
            ->setParameter('val1', $nextSunday)
            // date > à aujourd'hui
            ->andWhere('e.dateAt >= :val2')
            ->setParameter('val2', $today)
            // trié par date
            ->orderBy('e.dateAt', 'ASC')
            ->setMaxResults(9)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les événements associés au tag $tag
     * 
     * @param tag Object Tag dont on veux récupérer les événements
     * @return Event[] Returns an array of Event objects
     */
    public function findByEventWithTag(Tag $tag): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('e')
            // A partir de $today
            ->andWhere('e.dateAt >= :val')
            ->setParameter('val', $today)
            // dont le tag est $tag
            ->andwhere(':tag MEMBER OF e.tags')
            ->setParameter("tag", $tag)
            // triés par date
            ->orderBy('e.dateAt', 'ASC')
            ->setMaxResults(42)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les événements pour la pagination 
     * 
     * @param tag Object Tag dont on veux récupérer les événements
     * @param offset int Echappement des retour de 0 à offset 
     * @return Paginator Avec la $query retourne Event[]
     */
    public function getEventPaginatorByTag($tag, int $offset): Paginator
    {
        $today = new \DateTime();

        $query = $this->createQueryBuilder('e')
            // A partir de $today
            ->andWhere('e.dateAt >= :val')
            ->setParameter('val', $today);
        // dont le tag est $tag
        if ($tag != 'tout') {
            $query
                ->andwhere(':tag MEMBER OF e.tags')
                ->setParameter("tag", $tag);
        }
        // triés par date
        $query
            ->orderBy('e.dateAt', 'ASC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    /**
     * Retourne les événements par période par rapport à aujourd'hui
     * @param compare < >=
     * 
     * @return Event[] Returns an array of Event objects
     */
    public function findByPeriod($compare): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('e')
            // A partir de $today
            ->andWhere('e.dateAt ' . $compare . ' :val')
            ->setParameter('val', $today)
            // triés par date
            ->orderBy('e.dateAt', 'ASC')
            ->setMaxResults(42)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
