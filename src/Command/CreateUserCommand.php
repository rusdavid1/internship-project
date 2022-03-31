<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
    private ValidatorInterface $validator;

    private EntityManagerInterface $entityManager;

    private UserPasswordHasherInterface $passwordHasher;

    protected static $defaultName = 'app:create-user';

    protected static $defaultDescription = 'This command creates a new user';

    private string $plainPassword;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
         $this->addArgument('firstName', InputArgument::REQUIRED, 'First Name');
         $this->addArgument('lastName', InputArgument::REQUIRED, 'Last Name');
         $this->addArgument('email', InputArgument::REQUIRED, 'E-mail address');
         $this->addArgument('cnp', InputArgument::REQUIRED, 'CNP');
         $this->addOption('role', null, InputOption::VALUE_OPTIONAL, '', [User::ROLE_ADMIN]);
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

        $email = $input->getArgument('email');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $cnp = $input->getArgument('cnp');
        $role = $input->getOption('role');

        $user = new User();

        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->cnp = $cnp;
        $user->email = $email;
        $user->plainPassword = $this->plainPassword;
        $user->password = $this->passwordHasher->hashPassword($user, $this->plainPassword);
        $user->setRoles($role);

        $violationList = $this->validator->validate($user);

        $progressBar = new ProgressBar($output, 50);
        $progressBar->start();

        if (count($violationList) > 0) {
            foreach ($violationList as $violation) {
                $io->error($violation);
            }

            return self::FAILURE;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $progressBar->finish();

        $io->success('You have successfully created a user!');

        return self::SUCCESS;
    }
}
