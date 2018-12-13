<?php

require __DIR__ . "/../vendor/autoload.php";

$loop = \React\EventLoop\Factory::create();
$killer = new \React\Signals\Killer\SerialKiller($loop, [SIGTERM, SIGINT]);
$adapter = new class($loop) implements \Jiromm\QueueManager\Interfaces\MessageQueueAdapterInterface {
    protected $manager;
    protected $loop;

    public function __construct(\React\EventLoop\LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function send(string $queue, string $message, int $delay = 0): void
    {
        $job = new \Jiromm\QueueManager\Message\Job($message);
        echo $job->serialize() . ' SENT' . PHP_EOL;
    }

    public function receive(string $queue, callable $callback)
    {
        return $this->loop->addPeriodicTimer(1, function () use ($callback) {
            $job = new \Jiromm\QueueManager\Message\Job('Text from queue');

            echo 'working...' . PHP_EOL;
            shell_exec(sprintf('exec sleep %ds', mt_rand(3, 10)));
            echo 'done' . PHP_EOL;

            $callback($job->serialize());
        });
    }

    public function getMessageCount(string $queue): int
    {
        mt_rand(1, 5);
    }
};

$killer->onExit(function ($signal) use ($loop) {
    echo sprintf('Termination signal [%d] received.', $signal) . PHP_EOL;
    $loop->stop();
});

$manager = new \Jiromm\QueueManager\QueueManager(
    $adapter,
    $loop,
    $killer
);

$job = new \Jiromm\QueueManager\Message\Job('Test string');
$manager->send('queue_name', $job);
$manager->receive('queue_name', function (\Jiromm\QueueManager\Message\JobInterface $job) {
    echo $job->getMessage() . PHP_EOL;
});
