<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageBrokerService
{
    public function __construct(
        private readonly string $host,
        private readonly string $port,
        private readonly string $user,
        private readonly string $password
    ) {}

    private function connect(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            host: $this->host,
            port: $this->port,
            user: $this->user,
            password: $this->password,
        );
    }
    public function dispatchMessage(array $data, string $queue): void
    {
        $connection = $this->connect();
        $channel = $connection->channel();
        $channel->queue_declare(queue: $queue, durable: true, auto_delete: false);

        $data['identifier'] = uuid_create(UUID_TYPE_RANDOM);
        $message = new AMQPMessage(
            body: json_encode($data),
            properties: [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        $channel->basic_publish($message, '', $queue);

        $channel->close();
        $connection->close();
    }

    public function watch(callable $callback, string $queue): void
    {
        $connection = $this->connect();
        $channel = $connection->channel();
        $channel->queue_declare(queue: $queue, passive: true);
        $channel->basic_consume(
            queue: $queue,
            callback: $callback
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
