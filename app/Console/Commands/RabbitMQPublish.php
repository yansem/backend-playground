<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

#[Signature('rabbitmq:publish {message?}')]
#[Description('Command description')]
class RabbitMQPublish extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(RabbitMQService $rabbitMQ)
    {
        $channel = $rabbitMQ->getChannel();

        // Объявляем очередь (durable = false для простоты на старте)
        $channel->queue_declare(
            env('RABBITMQ_QUEUE', 'hello_queue'),
            false,   // passive
            false,   // durable
            false,   // exclusive
            false    // auto_delete
        );

        $messageText = $this->argument('message') ?? 'Hello World!';

        $msg = new AMQPMessage($messageText, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT  // пока не сохраняем на диск
        ]);

        $channel->basic_publish($msg, '', env('RABBITMQ_QUEUE', 'hello_queue'));

        $this->info(" [x] Отправлено: '{$messageText}'");

        $rabbitMQ->close();
    }
}
