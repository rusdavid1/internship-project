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

    public function __construct(HttpClientInterface $httpClient, UserRepository $userRepository)
    {
        $this->httpClient = $httpClient;
        $this->userRepository = $userRepository;
    }


    public function __invoke(SmsNotification $message)
    {
        var_dump($message);

        $users = $this->userRepository->findAll();
        $url = 'http://evozon-internship-sms-service.herokuapp.com/api/messages';

        foreach ($users as $user) {
            $response = $this->httpClient->request('POST', $url, [
                'headers' => ['X-API-KEY' => 'adaeibfece'],
                'json' => [
                    'receiver' => $user->phoneNumber,
                    'body' => $message
                ]
            ]);

            $this->logger->info('SMS sent', ['receivedBy' => $user->email]);

            $response->getStatusCode();
        }
    }
}
