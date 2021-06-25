<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\Producer;

class TestRDKafka extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:produce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Kafka';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

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
        $topic->produce(0, 0, 'message payload'.Carbon::now('Asia/Shanghai'));
        dump('OutLen: '. $rk->getOutQLen());

        $rk->flush(1);
    }
}
