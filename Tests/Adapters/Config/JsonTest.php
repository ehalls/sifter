<?php

namespace ptolemic\sifter\Test\Adapters\Config;

class JsonTest extends \PHPUnit_Framework_TestCase
{

    /**
     *  Test loading a sample json config
     *
     */
    public function testLoad()
    {
        $context = 'Test loading from decoded file';

        $mock = $this->getMockBuilder('ptolemic\sifter\Core\Adapters\Config\Json')
            ->setMethods(array('decode'))
            ->getMock();

        $mock->method('decode')
            ->with($this->anything())
            ->willReturn($context);

        $this->assertEquals($context, $mock->load(__DIR__.'/../../Resources/Sample.json'));

    }
}