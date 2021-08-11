<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\Producer;

class TestKafkaController extends Controller
{
    public function produce()
    {
        $conf = new Conf();
        $conf->set('log_level', (string)LOG_DEBUG);
        $conf->set('bootstrap.servers', '192.168.10.110:9092');
        $conf->setDrMsgCb(function (\RdKafka $kafka, Message $message) {
            dump($message);
            dump('回调：'.$kafka->getOutQLen());
        });

        $conf->setErrorCb(function (\RdKafka $kafka, $error, $reason) {
            dump('Kafka error: '. $error.' (reason: '. $reason .') \n');
        });



        $rk = new Producer($conf);
        $rk->addBrokers("192.168.10.110:9092");

        $topic = $rk->newTopic("Test");

        for ($i = 0; $i < 10; $i++) {
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, 'fpm-message '.Carbon::now('Asia/Shanghai').'__'.$i);
            $poll = $rk->poll(0);
            $len = $rk->getOutQLen();
            dump($i.'poll:'.$poll, $i.'poll len:'.$len);
        }

        dump('total:'.$rk->getOutQLen());
        $rk->flush(-1);

        /*for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
            $result = $rk->flush(1);
            dump('flush:'.$result);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }*/

        dump('finish');
    }
}
