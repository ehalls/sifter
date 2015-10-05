<?php
/**
 * Created by PhpStorm.
 * User: conjure
 * Date: 04/10/15
 * Time: 20:22
 */

namespace ptolemic\sifter\Core;

use ptolemic\sifter\Core\Adapters\Config\Json as ConfigJson;
use ptolemic\sifter\Core\Adapters\Output\Json as OutputJson;

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
            $this->build();
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
        if( empty(self::$$type[$format]) ) {
            $classname = ucfirst($type.$format);
            self::$$type[$format] = new $$classname();
        }
    }

    private function build()
    {
        echo "Running jobs found at: ". $this->data['jobs']['location'];
        //Run jobs
        $this->work();

        echo "Outputting to: ". $this->data['output']['location'];
        $this->dump();
    }

    public function work()
    {
        $this->jobModel = new Job($this->data['jobs']['location']);
        $this->jobModel->process();
    }

    public function dump()
    {
        foreach($this->jobModel->getResults() as $filename => $results)
        {
            $jsonOutput = $this->output[$this->data['output']['type']]->output($results);
            file_put_contents($this->destination.'\\'.$filename, $jsonOutput);
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
