<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQService
{
    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;

    public function __construct()
    {
        dump(1);
        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'rabbitmq'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
            env('RABBITMQ_VHOST', '/')
        );

        $this->channel = $this->connection->channel();
        dump(2);
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function declareExchange(string $exchangeName, string $type = 'fanout', bool $durable = true): void
    {
        $this->channel->exchange_declare(
            $exchangeName,
            $type,      // fanout, direct, topic
            false,      // passive
            $durable,   // durable — рекомендуется true в продакшене
            false       // auto_delete
        );
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
