<?php

namespace ruano-a\AccessLimiterBundle\Repository;

use ruano-a\AccessLimiterBundle\Entity\FailAccessAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class FailAccessAttemptRepository extends ServiceEntityRepository
{    
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FailAccessAttempt::class);
    }

    public function getFailAccessAttemptsFrom($ip, $beginDate)
    {
        return $this->createQueryBuilder('f')        ->where('f.ip = :ip')
        ->andWhere('f.lastFailDate > :beginDate')
        ->setParameter('ip', $ip)
        ->setParameter('beginDate', $beginDate)
        ->getQuery()
        ->getOneOrNullResult();
    }

    /* not tested */
    public function clearFailAccessAttempts($ip)
    {
        return $this->createQueryBuilder('f')
        ->delete()
        ->where('f.ip = :ip')
        ->setParameter('ip', $ip)
        ->getQuery()
        ->execute();
    }
}
