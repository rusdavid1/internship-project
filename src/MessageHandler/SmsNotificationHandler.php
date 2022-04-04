<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SmsNotification;
use App\Repository\UserRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsNotificationHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private UserRepository $userRepository;

    private HttpClientInterface $httpClient;

    public function __construct(
        HttpClientInterface $smsClient,
        UserRepository $userRepository
    ) {
        $this->httpClient = $smsClient;
        $this->userRepository = $userRepository;
    }


    public function __invoke(SmsNotification $message)
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $this->httpClient->request('POST', 'messages', [
                'json' => ['receiver' => $user->phoneNumber,'body' => $message->getContent()]
            ]);

            $this->logger->info('SMS sent', ['receivedBy' => $user->phoneNumber]);
        }
    }
}
