<?php
/**
 * Created by PhpStorm.
 * User: conjure
 * Date: 04/10/15
 * Time: 20:21
 */

namespace ptolemic\sifter;

use ptolemic\sifter\Core\Config;


/**
* Class Sifter
 * @package ptolemic\sifter
 *
 * This class acts as a gateway wrapper to the core scraping configuration and operation.
 * If modifications to the configuration are required for breaking up the config into several
 * threads then this is the place for it.
 *
 * So in the future clustering/threading will be coupled at this stage.
 */
class Sifter {

    private $config;

    public function __construct(Config $settings)
    {
        $this->init($settings);
    }

    private function init($settings = null)
    {
       if( $settings instanceof Config ) $this->config = $settings;
    }

    private function scrape()
    {
        return $this->config->build();
    }

    /**
     * TODO Add multithreading and distributed functionality.
     */
    private function cluster() { }

    public function execute()
    {
        echo "Starting.....\n";
        $this->scrape();
        $this->output();
    }


    // Some basic reporting possible and for cleaning up.
    public function output()
    {
        echo "Finished.....\n";
    }

    /**
     *  Used to clean the state in all relevant objects
     */
    public function reset()
    {
        $this->init();
    }

} 