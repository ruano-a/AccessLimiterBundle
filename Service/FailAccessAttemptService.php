<?php 

namespace ruano-a\AccessLimiterBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use ruano-a\AccessLimiterBundle\Entity\FailAccessAttempt;

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
     * Log in file, not database
     */
    public function logFail(?User $user, string $username, Request $request)
    {
        if ($user) // if the user exists
        {
            $superadmin = false;

            if (($superadmin = in_array('ROLE_SUPER_ADMIN', $user->getRoles())) //if he is an admin or superadmin
                || in_array('ROLE_ADMIN', $user->getRoles()))
            {
                $role = $superadmin ? 'SUPERADMIN' : 'ADMIN';
                $this->logger_admins->info('[Fail login ' . $role . '][' . $request->getClientIp() . ']: ' . $user->getUsername());
            }
            else
            {
                $this->logger_users->info('[Fail login USER][' . $request->getClientIp() .']: ' . $user->getUsername());
            }
        }
        else
        {
            $this->logger_users->info('[Fail login UNEXISTING USER][' . $request->getClientIp() . ']: ' . $username);
        }
    }

    public function clearFails($user, $ip)
    {
        $failLoginAttempt = $this->repo->clearFailLoginAttempts($user, $ip);
    }
}