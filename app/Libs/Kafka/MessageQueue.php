<?php
namespace App\Libs\Kafka;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RdKafka\Conf;
use RdKafka\Consumer;
use RdKafka\KafkaConsumer;
use RdKafka\KafkaErrorException;
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

        // 加载kafka配置
        $this->config('broker');

        // 发送失败回调
        $this->conf->setErrorCb(function ($kafka, $err, $reason) {
            Log::channel('kafka_message_queue')
                ->error("error: {$err}, reason: {$reason}, time: ".now()->toDateTimeString());
        });

    }

    public function producer($topic, $message, $beginTransaction = false)
    {
        // 发送成功回调
        $this->conf->setDrMsgCb(function ($kafka, Message $message){
            Log::channel('kafka_message_queue')
                ->info("success, reason: {$message->errstr()}, time: ", now()->toDateTimeString());
        });

        $this->config('producer');
        $producer = new Producer($this->conf);

        $rdTopic = $producer->newTopic($topic);

        $message = serialize($message);

        if ($beginTransaction) {
            // 开启事务 设置事务id
            $this->conf->set('transaction.id', $topic.'__transaction');
            $producer->initTransactions(30000);
            try {
                $producer->beginTransaction();
                $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
                $producer->commitTransaction(60000);

            } catch(KafkaErrorException $exception) {
                $producer->abortTransaction(120000);
                Log::channel('kafka_message_queue')
                    ->error($topic.'__transaction' . ':' . $exception->getMessage());
            }
        }else{
            // 最好是自动commit
            $rdTopic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        }


        // 两端客户时间记录 端到端
        return $producer->flush(1000);
    }

    public function consumer($topic, $autoCommit = true)
    {
        // rebalance重平衡回调
        $this->conf->setRebalanceCb(function (KafkaConsumer $consumer, $err, $partitions = null) {
                switch ($err) {
                    case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                        $consumer->assign($partitions);
                        Log::channel('kafka_message_queue')
                            ->info("Trigger Rebalance Assign Partition" . implode(",", $partitions));
                        break;
                    case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                        $consumer->assign(null);
                        Log::channel('kafka_message_queue')
                            ->info("Trigger Rebalance Revoke Partition" . implode(",", $partitions));
                }
        });

        $this->conf->set('group.id', $topic.'-group');

        // 设置自动提交级别
        if ($autoCommit) {
            $this->conf->set('enable.auto.commit', true);
        }else{
            $this->conf->set('enable.auto.commit', false);
            // 避免消费时间过长导致重平衡设置
            // 设置时间为5分钟 给下游业务争取时间
            $this->conf->set('max.poll.interval.ms', '300000');
        }

        $this->config('consumer');
        $consumer = new KafkaConsumer($this->conf);

        $consumer->subscribe([$topic]);
        $message = $consumer->consume(2000);

        if ($message->err == RD_KAFKA_RESP_ERR_NO_ERROR) {
            $message->payload = unserialize($message->payload);
        }

        return $message;
    }

    public function config($key)
    {
        $config = config("kafka.{$key}");

        foreach ($config as $key => $value) {
            $this->conf->set(str_replace('-', '.', $key), $value);
        }
    }

}
