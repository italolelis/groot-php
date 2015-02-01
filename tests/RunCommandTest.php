<?php

namespace Groot\Tests;


use Groot\RunCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $application;
    protected $command;

    public function setUp()
    {
        $application = new Application();
        $application->add(new RunCommand());

        $this->command = $application->find('groot:run');
        $this->application = $application;
    }

    protected function normalizeOutput($output)
    {
        $values = str_replace(PHP_EOL, " ", $output);
        return explode(" ", $values);
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }

    public function testHelloWorld()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'command' => $this->command->getName(),
            'file' => __DIR__ . '/Resources/helloworld.groot'
        ]);

        $values = $this->normalizeOutput($commandTester->getDisplay());

        $this->assertArraySubset($values, array(
            0 => '9',
            1 => '14',
            2 => '21',
            3 => '21',
            4 => '24',
            5 => '5',
            6 => '7',
            7 => '24',
            8 => '27',
            9 => '21',
            10 => '13',
            11 => '6',
            12 => '',
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSyntaxError()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'command' => $this->command->getName(),
            'file' => __DIR__ . '/Resources/syntax_error.groot'
        ]);
    }

    public function testInput()
    {
        $commandTester = new CommandTester($this->command);
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream('Test'));

        $commandTester->execute([
            'command' => $this->command->getName(),
            'file' => __DIR__ . '/Resources/input.groot'
        ]);

        $this->assertEquals('Please enter the new value: Test', trim($commandTester->getDisplay()));
    }
}