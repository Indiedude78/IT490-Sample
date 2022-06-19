<?php
    include(__DIR__ . "/vendor/autoload.php");
    require('config_rmq.ini');
    use PhpAmqpLib\Connection\AMQPStreamConnection;
    use PhpAmqpLib\Exchange\AMQPExchangeType;
    //require_once(__DIR__ . "/lib/helpers.php");

	$CONSUMER_TAG = 'reg_consumer';

	$connection = new AMQPStreamConnection('192.168.1.105', 5672, 'smit', 'P@78word', '/');

	$channel = $connection->channel();
	$channel->queue_declare('regdata', false, true, false, false);
	$channel->exchange_declare('regexch', AMQPExchangeType::DIRECT, false, true, false);
	$channel->queue_bind('regdata', 'regexch');

	function process_message($message) {
		echo json_decode($message);
		echo "\n------\n";
		$message->ack();
	}

	$channel->basic_consume('regdata', $CONSUMER_TAG, false, false, false, false, 'process_message');

	while ($channel->is_open()) {
		$channel->wait();
	}

?>