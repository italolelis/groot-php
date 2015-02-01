<?php

namespace Groot;


use Easy\Collections\ArrayList;
use Easy\Collections\Rx\FilterInterface;
use Easy\Collections\VectorInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class Tokenizer
{
    use ToOperation;
    /**
     * @var FilterInterface
     */
    protected $words;
    protected $misses;
    protected $buffer;

    public function __construct(FilterInterface $words, $misses = 0, $buffer = "")
    {
        $this->words = $words;
        $this->misses = $misses;
        $this->buffer = $buffer;
    }

    public function tokenize()
    {
        $ops = new ArrayList();

        $this->words->map(function ($word) use ($ops) {
            if ($this->getOperations()->containsKey($word)) {
                $op = $this->getOperations()->get($word);
                $this->pushOp($ops, $op);
            } else {
                $this->misses += 1;
                if ($this->misses === 3) {
                    throw new InvalidArgumentException(sprintf('syntax error unknown token `%s`', $word));
                }
            }

        });
        return $ops;
    }

    public function pushOp(VectorInterface $ops, $op)
    {
        $ops->add($op);
        $this->misses = 0;
        $this->buffer = "";
    }
}
