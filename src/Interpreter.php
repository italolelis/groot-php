<?php

namespace Groot;


use Easy\Collections\ArrayList;
use Easy\Collections\VectorInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Interpreter
{
    /**
     * @var VectorInterface
     */
    protected $operations;

    /**
     * @var int
     */
    protected $pointer;

    /**
     * @var VectorInterface
     */
    protected $memory;

    public function __construct(VectorInterface $operations)
    {
        $this->operations = $operations;
        $this->memory = new ArrayList();
    }

    public function run($command, InputInterface $input, OutputInterface $output)
    {
        $programCounter = 0;
        while ($programCounter < $this->operations->count()) {
            switch ($this->operations[$programCounter]) {
                case Operations::INCREMENT:
                    $this->increment();
                    break;
                case Operations::DECREMENT:
                    $this->decrement();
                    break;
                case Operations::OUTPUT:
                    $this->output($output);
                    break;
                case Operations::RIGHT:
                    $this->right();
                    break;
                case Operations::LEFT:
                    $this->left();
                    break;
                case Operations::INPUT:
                    $this->input($command, $input, $output);
                    break;
                case Operations::JUMP:
                    $this->jump($programCounter);
                    break;
                case Operations::JUMP_BACK:
                    $this->jumpBack($programCounter);
                    break;
            }
            $programCounter += 1;
        }
    }

    protected function left()
    {
        $this->pointer -= 1;
        return $this;
    }

    protected function right()
    {
        $this->pointer += 1;
        return $this;
    }

    protected function input($command, InputInterface $input, OutputInterface $output)
    {
        $helper = $command->getHelper('question');
        $question = new Question(_('Please enter the new value: '), $this->pointer);
        $value = $helper->ask($input, $output, $question);
        $this->memory[$this->pointer] = $value;
        return $this;
    }

    protected function increment()
    {
        if (!isset($this->memory[$this->pointer])) {
            $this->memory->add(1);
        } else {
            $this->memory[$this->pointer] += 1;
        }

        return $this;
    }

    protected function decrement()
    {
        $this->memory[$this->pointer] -= 1;
        return $this;
    }

    protected function output(OutputInterface $output)
    {
        $output->writeln($this->memory[$this->pointer]);
    }

    protected function jump($programCounter)
    {
        $bal = 1;
        if ($this->memory[$this->pointer] === 0) {
            while (true) {
                $programCounter += 1;
                if ($this->operations[$this->programCounter] === Operations::JUMP) {
                    $bal += 1;
                } elseif ($this->operations[$this->programCounter] === Operations::JUMP_BACK) {
                    $bal -= 1;
                }

                if ($bal === 0) {
                    break;
                }
            }
        }
        return $this;
    }

    protected function jumpBack($programCounter)
    {
        $bal = 0;
        while (true) {
            if ($this->operations[$programCounter] === Operations::JUMP) {
                $bal += 1;
            } elseif ($this->operations[$programCounter] === Operations::JUMP_BACK) {
                $bal -= 1;
            }

            $programCounter -= 1;
            if ($bal === 0) {
                break;
            }
        }
        return $this;
    }
}
