<?php 

namespace ruano_a\AccessLimiterBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use ruano_a\AccessLimiterBundle\Entity\FailAccessAttempt;

class FailAccessAttemptService
{
    private $em;
    private $logger;
    private $repo;
    const MAX_ATTEMPT = 3;
    const BAN_DURATION_MN = 10;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->repo = $this->em->getRepository(FailAccessAttempt::class);
    }

    public function hasFailedTooManyTimes($ip) : bool
    {
        $beginDate = new \DateTime();
        $beginDate->modify('-'.self::BAN_DURATION_MN.' minutes');
        $failAccessAttempt = $this->repo->getFailAccessAttemptsFrom($user, $ip, $beginDate);

        return ($failAccessAttempt && $failAccessAttempt->getNbFails() >= self::MAX_ATTEMPT);
    }

    public function noteFail(string $ip)
    {
        $failAccessAttempt = $this->repo->findOneByIp($ip);
        if (!$failAccessAttempt)
        {
            $failAccessAttempt = new FailAccessAttempt();
            $failAccessAttempt->setIp($ip);
            $this->em->persist($failAccessAttempt);
        }
        $failAccessAttempt->addFail();
        $failAccessAttempt->initLastFailDate();
        $this->em->flush();
    }

    /*
     * Log in file, not database, the password too, since it's not personal.
     */
    public function logFail(Request $request, string $passwordSent)
    {
        $this->logger->info('[Fail access][' . $request->getClientIp() . ']: ' . $passwordSent);
    }

    public function clearFails($ip)
    {
        $this->repo->clearFailAccessAttempts($ip);
    }
}