<?php

namespace Groot;


use Easy\Collections\ArrayList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('groot:run')
            ->setDescription('Run the groot language interpreter')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Which file do you want to interpret?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $words = new ArrayList(file($file));
        $newWords = $words->map(function ($word) {
            return $string = preg_replace('/\s+/', '', $word);
        });

        $tokenizer = new Tokenizer($newWords);
        $ops = $tokenizer->tokenize();
        $interpreter = new Interpreter($ops);
        $interpreter->run($this, $input, $output);
    }
}