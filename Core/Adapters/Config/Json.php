<?php
/**
 * Created by PhpStorm.
 * User: conjure
 * Date: 04/10/15
 * Time: 20:25
 */

namespace ptolemic\sifter\Core\Adapters\Config;

use ptolemic\sifter\Core\Adapters\Adapter;
use ptolemic\sifter\Core\Adapters\JsonHelper;

/**
 * Class Json
 * @package ptolemic\sifter\Core\Adapters\Config
 *
 * Decorator(parser) for configs
 */
class Json implements Adapter {

    use JsonHelper;

    public function output()
    {
        return $this->data;
    }

    public function load( $payload )
    {
        $this->data = $this->decode( file_get_contents($payload) );
        return $this->data;
    }
} 