<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use RdKafka\Message;
use RdKafka\Producer;

class TestKafkaController extends Controller
{
    public function produce()
    {
        dd(new Producer());
        $conf = new Conf();
        $conf->set('log_level', (string)LOG_DEBUG);
        $conf->set('bootstrap.servers', '192.168.10.110:9092');
        $conf->setDrMsgCb(function (\RdKafka $kafka, Message $message) {
            dump($message);
        });

        $conf->setErrorCb(function (\RdKafka $kafka, $error, $reason) {
            dump('Kafka error: '. $error.' (reason: '. $reason .') \n');
        });



        $rk = new Producer($conf);
        $rk->addBrokers("192.168.10.110:9092");

        $topic = $rk->newTopic("Test");
        $topic->produce(0, 0, 'fpm-message '.Carbon::now('Asia/Shanghai'));
        dump('OutLen: '. $rk->getOutQLen());

        $rk->flush(1);
    }
}
