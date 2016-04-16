<?php

namespace eu\luige\plagiarism\endpoint;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Slim\Container;
use Slim\Http\Request;

class Check extends Endpoint
{
    /** @var  AMQPStreamConnection */
    protected $connection;

    /**
     * Check constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->connection = $container->get(AMQPStreamConnection::class);
    }

    public function enqueue(Request $request, $response)
    {
        $channel = $this->connection->channel();
        $message = new AMQPMessage('test', ['delivery_mode' => 2]);
        $channel->basic_publish($message, '', 'task_queue2');
    }
}