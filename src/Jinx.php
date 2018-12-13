<?php

namespace Jiromm\QueueManager;

use Jiromm\QueueManager\Message\Job;

class Jinx
{
    /**
     * @var QueueManager
     */
    protected $queueManager;
    protected $queue;
    protected $sleepSeconds = 0;

    public function __construct(QueueManager $manager, string $queue)
    {
        $this->queueManager = $manager;
        $this->queue = $queue;
    }

    public function reSubmit(Job $job, ?string $queueName = null): void
    {
        if (is_null($queueName)) {
            $queueName = $this->queue;
        }

        $this->queueManager->send($queueName, $job);
    }

    public function sleep(int $second): void
    {
        $this->sleepSeconds = $second;
    }

    public function getSleepSeconds(): int
    {
        return $this->sleepSeconds;
    }

    /**
     * @param string $command
     * @param string $queue
     * @param Job $job
     * @param bool $verboseMode
     *
     * @return string|null
     */
    public function execute(string $command, string $queue, Job $job, bool $verboseMode = false)
    {
        $endCommand = sprintf(
            '%s %s %s %s',
            escapeshellcmd($command),
            escapeshellarg($queue),
            escapeshellarg($job->serialize()),
            $verboseMode ? '-v' : ''
        );
        return shell_exec($endCommand);
    }
}
