<?php
/**
 * Created by PhpStorm.
 * User: conjure
 * Date: 04/10/15
 * Time: 20:22
 */

namespace ptolemic\sifter\Core;


class Config {

    static $config = [ ], $output = [ ], $jobModel;
    private $data, $destination;

    public function __construct( $fileLocation = "")
    {
        $this->init($fileLocation);
    }

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

    private function defaults()
    {
        //We need at least Json as our base config parser
        $this->setAdapter("config", "json");
        $this->setAdapter("output", "json");
    }

    private function setAdapter($type, $format)
    {
        if( !isset(self::${$type}[$format])
            || empty(self::${$type}[$format]) ) {
            $classname = "ptolemic\\sifter\\Core\\Adapters\\".ucfirst($type)."\\".ucfirst($format);
            self::${$type}[$format] = new $classname();
        }
    }

    public function build()
    {
        echo "Running jobs found at: ". $this->data['jobs']['location']. "\n";
        $this->work();

        echo "Outputting to: ". $this->data['output']['location']. "\n";
        $this->dump();
    }

    private function work()
    {
        $this->jobModel = new Job($this->data['jobs']);
        $this->jobModel->process();
    }

    public function dump()
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
    public function setDestination($location)
    {
        if( !empty($location)
            && is_string($location) ) {
            $this->destination = $location;
        }
    }


}
