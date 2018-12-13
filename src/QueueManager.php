<?php

namespace Jiromm\QueueManager;

use Jiromm\QueueManager\Exception\WrongMessageException;
use Jiromm\QueueManager\Interfaces\MessageQueueAdapterInterface;
use Jiromm\QueueManager\Message\Job;
use Jiromm\QueueManager\Message\JobInterface;
use React\EventLoop\LoopInterface;
use React\Signals\Killer\KillerInterface;

class QueueManager
{
    /**
     * @var MessageQueueAdapterInterface
     */
    protected $adapter;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var KillerInterface
     */
    protected $killer;

    /**
     * @var string
     */
    protected $prefix = '';

    public function __construct(
        MessageQueueAdapterInterface $adapter,
        LoopInterface $loop,
        KillerInterface $killer,
        Prefix $prefix = null
    )
    {
        $this->adapter = $adapter;
        $this->loop = $loop;
        $this->killer = $killer;

        if (!is_null($prefix)) {
            $this->prefix = (string)$prefix;
        }
    }

    /**
     * @param string $queue
     * @param string|Job $message
     * @param int $delay
     */
    public function send(string $queue, $message, int $delay = 0)
    {
        $queue = $this->fixQueueName($queue);
        if (is_string($message)) {
            $message = new Job($message);
        }

        if (!$message instanceof JobInterface) {
            throw new WrongMessageException('Queue message format is wrong');
        }

        $this->adapter->send($queue, $message->serialize(), $delay);
    }

    public function receive(string $queue, callable $callback, JobInterface $job = null): void
    {
        $queue = $this->fixQueueName($queue);
        $jinx = new Jinx($this, $queue);

        $timer = $this->adapter->receive($queue, function (string $message) use ($callback, $jinx, $job) {
            if (is_null($job)) {
                $job = new Job();
            }

            $job->unserialize($message);
            $callback($job, $jinx);

            if ($sleep = $jinx->getSleepSeconds()) {
                $jinx->sleep(0);
                sleep($sleep);
            }
        });

        $this->loop->run();
    }

    public function execute(string $jobString, string $queue, callable $callback): void
    {
        $queue = $this->fixQueueName($queue);
        $jinx = new Jinx($this, $queue);
        $job = new Job();
        $job->unserialize($jobString);

        $callback($job, $jinx);
    }

    public function getMessageCount(string $queue): int
    {
        $queue = $this->fixQueueName($queue);
        return $this->adapter->getMessageCount($queue);
    }

    public function getAdapter(): MessageQueueAdapterInterface
    {
        return $this->adapter;
    }

    private function fixQueueName(string $queue): string
    {
        if ($this->prefix && strpos($queue, $this->prefix) === 0) {
            return $queue;
        }

        return $this->prefix . $queue;
    }
}
