<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Traits\ValidatorCommandTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommand extends Command
{
    use ValidatorCommandTrait;

    private ValidatorInterface $validator;

    private EntityManagerInterface $entityManager;

    private UserPasswordHasherInterface $passwordHasher;

    private LoggerInterface $analyticsLogger;

    protected static $defaultName = 'app:create-user';

    protected static $defaultDescription = 'This command creates a new user';

    private string $plainPassword;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $analyticsLogger
    ) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->analyticsLogger = $analyticsLogger;

        parent::__construct();
    }

    protected function configure(): void
    {
         $this->addArgument('firstName', InputArgument::REQUIRED, 'First Name');
         $this->addArgument('lastName', InputArgument::REQUIRED, 'Last Name');
         $this->addArgument('email', InputArgument::REQUIRED, 'E-mail address');
         $this->addArgument('cnp', InputArgument::REQUIRED, 'CNP');
         $this->addArgument('phoneNumber', InputArgument::REQUIRED, 'Phone number');
         $this->addOption('role', null, InputOption::VALUE_OPTIONAL, '', User::ROLE_ADMIN);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter your new password for the account');

        $this->plainPassword = $helper->ask($input, $output, $question);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();

        $user->firstName = $input->getArgument('firstName');
        $user->lastName = $input->getArgument('lastName');
        $user->cnp = $input->getArgument('cnp');
        $user->email = $input->getArgument('email');
        $user->phoneNumber = $input->getArgument('phoneNumber');
        $user->setPassword($this->passwordHasher->hashPassword($user, $this->plainPassword));
        $user->setRoles([$input->getOption('role')]);

        $violationList = $this->validator->validate($user);

        $progressBar = new ProgressBar($output, 50);
        $progressBar->start();

        if (count($violationList) > 0) {
            $this->displayErrorsInCli($violationList, $io, self::FAILURE);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $progressBar->finish();

        $this->analyticsLogger->info(
            'User registered',
            [
                'email' => $user->email,
                'role' => $user->getRoles()[0],
                'result' => 'successful',
                'type' => 'register',
                'firewall' => 'command',
            ]
        );

        $io->success('You have successfully created a user!');

        return self::SUCCESS;
    }
}
