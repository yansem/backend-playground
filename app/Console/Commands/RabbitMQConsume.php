<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

#[Signature('rabbitmq:consume')]
#[Description('Command description')]
class RabbitMQConsume extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(RabbitMQService $rabbitMQ)
    {
        $channel = $rabbitMQ->getChannel();

        $queue = env('RABBITMQ_QUEUE', 'hello_queue');

        $channel->queue_declare($queue, false, false, false, false);

        $this->info(" [*] Ожидание сообщений в очереди '{$queue}'. Для выхода нажмите CTRL+C");

        $callback = function (AMQPMessage $msg) {
            $this->info(" [x] Получено: " . $msg->body);

            // Подтверждаем обработку сообщения
            $msg->ack();
        };

        // Подписываемся на очередь
        $channel->basic_consume(
            $queue,
            '',           // consumer_tag
            false,        // no_local
            false,        // no_ack (мы будем делать ack вручную)
            false,        // exclusive
            false,        // nowait
            $callback
        );

        // Запускаем цикл ожидания сообщений
        while ($channel->is_open()) {
            $channel->wait();
        }

        $rabbitMQ->close();
    }
}
