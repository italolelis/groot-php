<?php

namespace Groot;


use Easy\Collections\Dictionary;

trait ToOperation
{
    /**
     * @return Dictionary
     */
    public function getOperations()
    {
        return new Dictionary([
            "iamgroot" => Operations::INCREMENT,
            "IamGroot" => Operations::DECREMENT,
            "IAMGROOOT" => Operations::OUTPUT,
            "IAMGROOT" => Operations::RIGHT,
            "Iamgroot" => Operations::LEFT,
            "I'mGroot" => Operations::JUMP,
            "WeareGroot" => Operations::JUMP_BACK,
            "Iamgrooot" => Operations::INPUT,
            "" => Operations::UNKNOWN,
        ]);
    }

}
