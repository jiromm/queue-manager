<?php

namespace Jiromm\QueueManager;

use PHPUnit\Framework\TestCase;
use Jiromm\QueueManager\Exception\WrongMessageException;
use Jiromm\QueueManager\Message\Job;

class JobTest extends TestCase
{
    public function test_getMessageMethod()
    {
        $message = 'test';
        $job = new Job($message);

        $this->assertEquals($job->getMessage(), $message);
    }

    public function test_getAttemptsMethod()
    {
        $message = 'test';
        $attempts = 5;
        $job = new Job($message, $attempts);

        $this->assertEquals($job->getAttempts(), $attempts);
    }

    public function test_throwsAnExceptionOnWrongAttempt()
    {
        $this->expectException(WrongMessageException::class);
        $message = 'test';
        $attempts = -5;
        new Job($message, $attempts);
    }

    public function test_getCreatedAtMethod()
    {
        $message = 'test';
        $attempts = 5;
        $createdAt = date('Y-m-d H:i:s');
        $job = new Job($message, $attempts, $createdAt);

        $this->assertEquals($job->getCreatedAt(), $createdAt);
    }

    public function test_getCreatedAtMethodIfNotExist()
    {
        $message = 'test';
        $attempts = 5;
        $acceptableDelta = 1; // in seconds
        $job = new Job($message, $attempts);

        $this->assertEquals(strtotime($job->getCreatedAt()), time(), 'Created date not equal to now() weather it missed in parameters', $acceptableDelta);
    }

    public function test_getModifiedAtMethod()
    {
        $message = 'test';
        $attempts = 5;
        $acceptableDelta = 1; // in seconds
        $job = new Job($message, $attempts);

        $this->assertEquals(strtotime($job->getModifiedAt()), time(), 'Modified date not equal to now() weather it missed in parameters', $acceptableDelta);
    }

    public function test_increaseAttemptsMethod()
    {
        $message = 'test';
        $attempts = 5;
        $job = new Job($message, $attempts);
        $job->increaseAttempts();
        $job->increaseAttempts();

        $this->assertEquals($job->getAttempts(), 7);
    }

    public function test_decreaseAttemptsMethod()
    {
        $message = 'test';
        $attempts = 5;
        $job = new Job($message, $attempts);
        $job->decreaseAttempts();
        $job->decreaseAttempts();
        $job->decreaseAttempts();

        $this->assertEquals($job->getAttempts(), 2);
    }

    public function test_decreaseAttemptsLessThanZero()
    {
        $message = 'test';
        $attempts = 2;
        $job = new Job($message, $attempts);
        $job->decreaseAttempts();
        $job->decreaseAttempts();
        $job->decreaseAttempts();

        $this->assertEquals($job->getAttempts(), 1);
    }

    public function test_resetAttemptsMethod()
    {
        $message = 'test';
        $attempts = 10;
        $job = new Job($message, $attempts);
        $job->resetAttempts();

        $this->assertEquals($job->getAttempts(), 1);
    }

    public function test_serializeAndUnserializeMethods()
    {
        $message = 'test';
        $attempts = 5;
        $createdAt = date('Y-m-d H:i:s');
        $job = new Job($message, $attempts, $createdAt);
        $serialized = $job->serialize();
        $emptyJob = new Job();
        $emptyJob->unserialize($serialized);

        $this->assertEquals($job->getMessage(), $emptyJob->getMessage());
        $this->assertEquals($job->getAttempts(), $emptyJob->getAttempts());
        $this->assertEquals($job->getCreatedAt(), $emptyJob->getCreatedAt());
    }
}
