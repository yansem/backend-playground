<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

#[Signature('rabbitmq:publish-fanout {message?}')]
#[Description('Публикует сообщение через Fanout Exchange (broadcast всем потребителям)')]
class RabbitMQPublishFanout extends Command
{
    public function handle(RabbitMQService $rabbitMQ)
    {
        $channel = $rabbitMQ->getChannel();
dump(1);
        $exchange = env('RABBITMQ_FANOUT_EXCHANGE', 'logs_exchange');

        // Объявляем Fanout exchange
        $rabbitMQ->declareExchange($exchange, 'fanout', true);

        $messageText = $this->argument('message') ?? 'Hello from Fanout Exchange!';

        $msg = new AMQPMessage($messageText, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT, // 2
        ]);

        // Публикуем в exchange (routing key пустой для fanout)
        $channel->basic_publish($msg, $exchange, '');

        $this->info(" [x] Отправлено в Fanout Exchange '{$exchange}': '{$messageText}'");

        $rabbitMQ->close();
    }
}
