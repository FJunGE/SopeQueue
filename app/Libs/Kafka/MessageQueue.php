<?php
namespace App\Libs\Kafka;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\Producer;

class MessageQueue
{
    private $conf;

    public function __construct()
    {
        $this->conf = new Conf();

        $this->conf->set('metadata.broker.list', '127.0.0.1:9092');
        $this->conf->set('log_level', (string)LOG_DEBUG);
        $this->conf->set('debug', 'all');
        $this->conf->set('acks', 'all');

        // 	Alias for message.send.max.retries: How many times to retry sending a failing Message.
        // Note: retrying may cause reordering unless enable.idempotence is set to true.
        // 消息重发次数，enable.idempotence为true 顺序不会打乱
        $this->conf->set('message.send.max.retries', 3);

        // Client group session and failure detection timeout.
        // The consumer sends periodic heartbeats (heartbeat.interval.ms) to indicate its liveness to the broker.
        // 客户端组会话故障超时时间，心跳信号，超过当前时间，消费哲组重平衡
        $this->conf->set('session.timeout.ms', 15000);

        // Default timeout for network requests.
        // 网络请求的默认超时。
        $this->conf->set('socket.timeout.ms', 15000);

        //	When set to true, the producer will ensure that messages are successfully produced exactly once and in the original produce order.
        // 当设置为true时，producer将确保消息只成功生成一次，并且以原始的生成顺序生成。
        $this->conf->set('enable.idempotence', true);

        // 发送失败回调
        $this->conf->setErrorCb(function ($kafka, $err, $reason) {
            Log::channel('kafka_message_queue')
                ->error("error: {$err}, reason: {$reason}, time: ".now()->toDateTimeString());
        });

    }

    public function producer($topic, $message)
    {
        // 发送成功回调
        $this->conf->setDrMsgCb(function ($kafka, Message $message){
            Log::channel('kafka_message_queue')
                ->info("success, reason: {$message->errstr()}, time: ", now()->toDateTimeString());
        });

        $producer = new Producer($this->conf);

        $topic = $producer->newTopic($topic);

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        // 两端客户时间记录 端到端
        return $producer->flush(1000);
    }

    public function consumer($topic, $autoCommit = true)
    {

    }

}
