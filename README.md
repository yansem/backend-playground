1. Базовые сценарии (обязательно для понимания основ)

Hello World (Simple Queue)
Producer отправляет одно сообщение в очередь.
Consumer получает и обрабатывает его (с basic_consume и callback).
Тестируй: отправку/получение простого текста/JSON, закрытие соединения.

Work Queues (Task Queue)
Один producer отправляет много «тяжёлых» задач (например, симуляция обработки изображения/отправки email).
Несколько consumers (запускай несколько процессов/консольных команд).
По умолчанию — round-robin распределение.
Тестируй: durable queue + persistent messages (delivery_mode = 2), чтобы сообщения не терялись при рестарте RabbitMQ.

Publish/Subscribe (Fanout Exchange)
Producer публикует сообщение в fanout exchange.
Несколько consumers с временными (или durable) очередями, привязанными к exchange (без routing key).
Все consumers получают копию сообщения (логирование, уведомления, broadcasting событий).


2. Средние сценарии (самые полезные на практике)

Routing (Direct Exchange)
Producer отправляет сообщения с разными routing_key (например: error, info, warning).
Consumers биндятся к exchange с конкретными ключами.
Тестируй: selective доставку (один consumer только ошибки, другой — всё).

Topics (Topic Exchange)
Самый гибкий паттерн.
Routing keys с wildcard: user.created.*, order.*.failed, logs.# и т.д.
Примеры: события пользователей, заказов, аналитика.

Headers Exchange
Маршрутизация не по routing_key, а по заголовкам сообщения (headers).
Полезно, когда нужна сложная логика фильтрации.


3. Продвинутые сценарии (часто спрашивают на собеседованиях)

Request/Reply (RPC — Remote Procedure Call)
Client отправляет запрос в очередь RPC + указывает reply_to (callback queue) и correlation_id.
Server обрабатывает и отвечает в callback queue.
Тестируй: синхронный вид асинхронного общения (например, «посчитай что-то тяжёлое»).

Multiple Consumers + Prefetch (QoS)
Настрой basic_qos(prefetch_count = 1 или 5-10).
Тестируй fair dispatch: чтобы один медленный consumer не блокировал всех.
Сравни поведение с prefetch=0 (неограниченно).

Acknowledgments и Reliability
Manual ACK ($message->ack() или basic_ack).
Negative ACK + requeue (basic_nack с requeue=true/false).
Dead Letter Exchange (DLX) — когда сообщения «умирают» (TTL, rejected, max-length).
Тестируй потерю соединения, перезапуск consumer'а, повторную обработку.

Persistent + Durable + Quorum Queues
Durable exchange + queue + persistent messages.
В новых версиях RabbitMQ — попробуй Quorum Queues (более надёжные, чем classic mirrored).
Тестируй: kill RabbitMQ / перезапуск — сообщения должны остаться.


4. Специфично для Laravel + php-amqplib

Интеграция с Artisan командами
Создай консольные команды:
php artisan rabbitmq:publish {message}
php artisan rabbitmq:consume {queue-or-exchange}

Запускай consumer как long-running process (supervisor в проде, или while true + sleep в тесте).

Обработка ошибок и перезапуска
Что происходит при exception в callback? (реqueue или dead letter).
Graceful shutdown consumer'а.
Reconnection logic (php-amqplib не всегда сам переподключается — реализуй wrapper).

JSON / Structured Messages
Отправляй/принимай массив/объект → json_encode/decode.
Тестируй валидацию сообщения перед обработкой.

Микросервисный стиль
Два отдельных Laravel проекта (или два kernel'а).
Service A публикует событие UserRegistered → Service B потребляет и делает что-то (отправка welcome email, обновление аналитики).
Event-driven architecture.

Producer в Laravel контроллере / Job'е
При HTTP запросе (регистрация пользователя) сразу публикуешь в RabbitMQ, а не делаешь тяжёлую работу синхронно.


5. Дополнительные интересные сценарии для глубокого тестирования

Message TTL + Expiration
Queue length limit + overflow behaviour
Priority Queues (с x-max-priority)
Batch publishing / consuming (несколько сообщений за раз)
Connection / Channel management (один connection — много channels)
SSL / AMQPS (если используешь cloud вроде CloudAMQP)
Мониторинг — подключи RabbitMQ Management UI и смотри, как меняются метрики при твоих тестах.

Рекомендации по реализации и подготовке к собеседованию

Начни с официальных туториалов RabbitMQ на PHP:
https://www.rabbitmq.com/tutorials/tutorial-one-php (и дальше 2–6).
Создай сервис-классы в Laravel:
RabbitMQProducer
RabbitMQConsumer
С конфигами в config/rabbitmq.php или .env.

Используй AMQPStreamConnection (или AMQPSSLConnection).
Для long-running consumers лучше не использовать чистый basic_consume в цикле без обработки сигналов — реализуй graceful shutdown (pcntl_signal).
На собеседовании тебя могут спросить:
Разница между Direct / Fanout / Topic.
Как обеспечить at-least-once / exactly-once доставку.
Prefetch, ACK, DLX.
Почему RabbitMQ, а не Redis/Kafka/SQS.
Как масштабировать consumers.


Если хочешь, могу подсказать структуру классов или пример кода для конкретного сценария (например, RPC или Topic с JSON). Просто скажи, какой хочешь разобрать первым.
Удачи на собеседовании! С таким sandbox'ем ты будешь чувствовать себя уверенно.
