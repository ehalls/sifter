<?php
/**
 * Created by PhpStorm.
 * User: conjure
 * Date: 04/10/15
 * Time: 22:29
 */

namespace ptolemic\sifter\Core\Adapters;

/**
 * Trait Json
 * @package ptolemic\sifter\Core\Adapters
 *
 * Collection of functions shared by Json decorators.
 * Does not need to be an inherited class as there is no use for it as is.
 */
trait JsonHelper {

    protected $data;

    public function decode( $payload )
    {
        return json_decode($payload, true);
    }

    public function encode( $payload )
    {
        return json_encode($payload);
    }
} 