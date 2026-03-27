<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

#[Signature('rabbitmq:consume-fanout {queueName?}')]
#[Description('Подписывается на Fanout Exchange через свою очередь')]
class RabbitMQConsumeFanout extends Command
{
    public function handle(RabbitMQService $rabbitMQ)
    {
        $channel = $rabbitMQ->getChannel();

        $exchange = env('RABBITMQ_FANOUT_EXCHANGE', 'logs_exchange');
        $queueName = $this->argument('queueName') ?? 'logs_queue_' . uniqid();
dump(1);
        // 1. Объявляем Fanout exchange
        $rabbitMQ->declareExchange($exchange, 'fanout', true);
        dump(2);
        // 2. Объявляем очередь (лучше durable)
        $channel->queue_declare(
            $queueName,
            false,   // passive
            true,    // durable
            false,   // exclusive (false — чтобы можно было запустить несколько одинаковых consumers)
            false    // auto_delete
        );

        // 3. Биндим очередь к exchange (routing key игнорируется в fanout)
        $channel->queue_bind($queueName, $exchange, '');

        $this->info(" [*] Consumer запущен. Очередь: '{$queueName}'");
        $this->info(" [*] Ожидание сообщений из Fanout Exchange '{$exchange}'. Для выхода нажмите CTRL+C");

        $callback = function (AMQPMessage $msg) {
            $this->info(" [x] Получено в очереди '{$msg->getConsumerTag()}': " . $msg->body);
            $msg->ack(); // подтверждение обработки
        };

        $channel->basic_consume(
            $queueName,
            '',           // consumer_tag
            false,
            false,        // no_ack = false
            false,
            false,
            $callback
        );

        while ($channel->is_open()) {
            $channel->wait();
        }

        $rabbitMQ->close();
    }
}
