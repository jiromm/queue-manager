<?php

namespace Jiromm\QueueManager\Message;

interface JobInterface
{
    public function getMessage();
    public function getAttempts();
    public function getCreatedAt();
    public function getModifiedAt();
    public function getMeta();
}
