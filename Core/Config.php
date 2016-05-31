<?php

namespace ptolemic\sifter\Core;


class Config {

    static $config = [ ], $output = [ ], $jobModel;
    private $data, $destination;

    public function __construct( $fileLocation = "")
    {
        $this->init($fileLocation);
    }

    /**
     *  Set adapater defaults
     */
    private function defaults()
    {
        //We need at least Json as our base config parser
        $this->setAdapter("config", "json");
        $this->setAdapter("output", "json");
    }

    /**
     * Set adapter defaults
     *
     * @param $type string  Are we setting Config|Output adapter
     * @param $format string What format of adapter are we using ? eg. Json
     *
     */
    private function setAdapter($type, $format)
    {
        if( !isset(self::${$type}[$format])
            || empty(self::${$type}[$format]) ) {
            $classname = "ptolemic\\sifter\\Core\\Adapters\\".ucfirst($type)."\\".ucfirst($format);
            self::${$type}[$format] = new $classname();
        }
    }

    /**
     * Instantiate, initialize, and process scraping jobs
     */
    private function work()
    {
        $this->jobModel = new Job($this->data['jobs']);
        $this->jobModel->process();
    }

    /**
     * Decorate the output from running Jobs
     */
    private function dump()
    {
        foreach($this->jobModel->getResults() as $filename => $results)
        {
            if($results["total"] == 0)
            {
                echo "No results for $filename \n";
                continue;
            }

            self::$output[$this->data['output']['type']]
                ->output($results['data'], $this->destination.$filename);

        }
    }

    /**
     * Function handling object initialization after instantiation
     *
     * @param $fileLocation string Directory path
     */
    public function init($fileLocation)
    {
        $this->defaults();

        if( !empty($fileLocation)
            && is_string($fileLocation)
            && file_exists($fileLocation) ) {
            $this->data = self::$config["json"]->load($fileLocation);
            $this->setDestination( $this->data['output']['location'] );
        }else{
            echo "File location invalid.";
        }

    }

    /**
     *  Verifies and stores the relevant configuration path for this instance
     *
     * @param $location string Directory path
     */
    public function setDestination($location)
    {
        if( !empty($location)
            && is_string($location) ) {
            $this->destination = $location;
        }
    }

    /**
     *  Run jobs defined in the configuration and format their output
     */
    public function build()
    {
        echo "Running jobs found at: ". $this->data['jobs']['location']. "\n";
        $this->work();

        echo "Outputting to: ". $this->data['output']['location']. "\n";
        $this->dump();
    }

}
