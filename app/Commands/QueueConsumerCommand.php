<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\MailerService;
use App\Services\MessageBrokerService;
use Exception;
use App\Exceptions\InvalidMessageTypeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:queue-consumer',
    description: 'Consume messages from the queue'
)]
class QueueConsumerCommand extends Command
{
    private MessageBrokerService $messageBrokerService;
    private MailerService $mailerService;

    public function __construct(
        MessageBrokerService $messageBrokerService,
        MailerService $mailerService
    ) {
        parent::__construct();
        $this->messageBrokerService = $messageBrokerService;
        $this->mailerService = $mailerService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Consuming messages from the queue');

            $this->messageBrokerService->watch(
                callback: function ($message) use ($output) {
                    $messageData = json_decode($message->body, true);

                    if ($messageData['type'] === EMAIL_QUEUE_TYPE) {
                        $this->sendStockEmail($messageData['data']);
                        $output->writeln('Email sent to ' . $messageData['data']['email']);
                        $message->ack();
                        return;
                    }

                    $message->nack();
                    throw new InvalidMessageTypeException($messageData);
                },
                queue: EMAIL_QUEUE
            );

            $output->writeln('All messages consumed');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }
    }

    private function sendStockEmail(array $emailData): void
    {
        $this->mailerService->sendStockEmail(
            email: $emailData['email'],
            stockCode: $emailData['stockCode'],
            stockData: $emailData['stockData']
        );
    }
}
