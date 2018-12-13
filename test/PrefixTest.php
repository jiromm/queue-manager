<?php

namespace Jiromm\QueueManager;

use PHPUnit\Framework\TestCase;
use Jiromm\QueueManager\Exception\InvalidException;
use Jiromm\QueueManager\Prefix;

class PrefixTest extends TestCase
{
    public function test_returnsPrefixWithDefaultDelimiter()
    {
        $prefix = new Prefix('dev');
        $this->assertEquals((string)$prefix, 'dev_');
    }

    public function test_returnsPrefixWithCustomDefaultDelimiter()
    {
        $prefix = new Prefix('dev', 'dev');
        $this->assertEquals((string)$prefix, 'devdev');
    }

    public function test_returnsPrefixWithoutDefaultDelimiter()
    {
        $prefix = new Prefix('dev', '');
        $this->assertEquals((string)$prefix, 'dev');
    }

    /**
     * @param string $prefix
     * @dataProvider wrongNames
     */
    public function test_throwsAnExceptionIfPrefixIsWrong($prefix)
    {
        $this->expectException(InvalidException::class);

        new Prefix($prefix);
    }

    /**
     * @param string $delimiter
     * @dataProvider wrongNames
     */
    public function test_throwsAnExceptionIfDelimiterIsWrong($delimiter)
    {
        $this->expectException(InvalidException::class);

        new Prefix('dev', $delimiter);
    }

    public function wrongNames()
    {
        return [
            [' '],
            ['x x'],
            ['xx/xx'],
            ['xx*xx'],
            ['xx.xx'],
        ];
    }
}
