<?php

namespace Jiromm\QueueManager;

use PHPUnit\Framework\TestCase;
use Jiromm\QueueManager\Exception\WrongMessageException;
use Jiromm\QueueManager\Message\MessageArrayValidator;

class MessageArrayValidatorTest extends TestCase
{
    public function test_messageIsValid()
    {
        $message = ['message' => 'xx', 'attempts' => 5, 'created_at' => '2017-01-01 01:01:01', 'modified_at' => '2017-01-01 01:01:01'];
        $validator = new MessageArrayValidator();
        $validator->validate($message);

        $this->assertTrue(true);
    }

    /**
     * @param array $message
     * @dataProvider wrongMessages
     */
    public function test_throwsAnExceptionOnWrongMessage($message)
    {
        $this->expectException(WrongMessageException::class);

        $validator = new MessageArrayValidator();
        $validator->validate($message);
    }

    public function wrongMessages()
    {
        return [
            [['message' => 'xx', 'attempts' => 5, 'created_at' => '2017-01-01 01:01:01']],
            [['message' => 'xx', 'attempts' => 5, 'modified_at' => '2017-01-01 01:01:01']],
            [['message' => 'xx', 'created_at' => '2017-01-01 01:01:01', 'modified_at' => '2017-01-01 01:01:01']],
            [['attempts' => 5, 'created_at' => '2017-01-01 01:01:01', 'modified_at' => '2017-01-01 01:01:01']],
        ];
    }
}
