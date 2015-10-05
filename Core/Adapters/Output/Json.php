<?php

namespace ptolemic\sifter\Core\Adapters\Output;

use ptolemic\sifter\Core\Adapters\Adapter;
use ptolemic\sifter\Core\Adapters\JsonHelper;

class Json implements Adapter {

    use JsonHelper;


    public function output( $payload )
    {
        return $this->encode( $payload )? : "";
    }
}