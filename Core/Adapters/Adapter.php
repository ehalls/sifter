<?php
/**
 * Created by PhpStorm.
 * User: conjure
 * Date: 04/10/15
 * Time: 21:47
 */

namespace ptolemic\sifter\Core\Adapters;


interface Adapter {

    public function decode( $payload );

    public function encode( $payload );
} 