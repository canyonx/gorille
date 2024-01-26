<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * To create new admin from command line :
 * app:create-admin user-email password
 */
class CreateAdminCommand extends Command
{
    private $em;
    private $encoder;
    protected static $defaultName = 'app:create-admin';

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $encoder
    ) {
        $this->em = $em;
        $this->encoder = $encoder;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'The email of user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();

        $user->setEmail($input->getArgument('email'));

        $password = $this->encoder->hashPassword($user, $input->getArgument('password'));
        $user->setPassword($password);

        $user->setRoles(['ROLE_ADMIN']);

        dump($user);

        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;
    }
}
