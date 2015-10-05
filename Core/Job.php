<?php

namespace ptolemic\sifter\Core;

use ptolemic\sifter\Core\Adapters\Config\Json as ConfigJson;
use Goutte\Client;

class Job {

    static $goutte, $config;
    private $jobs, $crawler, $results = [ ];

    public function __construct(array $jobConfig)
    {
        $this->init($jobConfig['location'], $jobConfig['type']);
    }

    private function init($sourceDirectory, $format)
    {
        self::$goutte = new Client();
        $formatClass = 'Config'.$format
        self::$config[$format] = new  $$formatClass();

        $jobList = scandir($sourceDirectory);
        foreach($jobList as  $filename) {
            if( $filename == '.' || $filename == '..') continue;
            $this->jobs[$filename] = self::$config[$format]->decode(file_get_contents($sourceDirectory.'\\'.$filename));
        }
    }

    private function iterateSelectors($format)
    {
        $store = array('results' => [ ], "total" => 0);
        foreach( $format['results'] as $payload)
        {
            $store['results'] = array_merge($store['results'], $this->iterateSubSelectors($format['container'], $payload));

        }

        //Hack in the total , just speed. Not a final flexible version
        if( isset($format['total']) )
        {
            $store['total'] = count($store['results']);
        }

       return $store;
    }

    private function iterateSubSelectors($container, $payload)
    {
        $nodes = $this->crawler->filter($container);
        $results = array();
        foreach($nodes as $product){
            $result = new \stdClass();
            foreach($payload as $label => $selector)
            {

                if( is_string($selector) ) {
                    $result->label = $product->filter($selector)->text();
                }elseif( is_array($selector) ) {

                    switch($selector['type']) {
                        case "link":
                            $link = $product->selectLink($selector['context']);
                            $nextPageCrawler = self::$goutte->click($link);

                            if($selector['target'] == 'content_size')
                            {
                                $result->size = self::$goutte->getResponse()->getHeader('Content-Length');

                            }elseif( is_array($selector['target'])
                                  && $selector['target']['type'] == "position" ) {
                                $description = $nextPageCrawler
                                                    ->filter($selector['target']['context'])
                                                    ->eq($selector['target']['target']);

                                $result->description = str_replace (array("</p>", "<p>"), "", $description);
                            }
                    }
                }

            }

            $results[] = $result;
        }

        return $results;
    }

    public function process()
    {
        foreach( $this->jobs  as $filename => $settings ) {
            $this->results[$filename] = [ ];

            foreach($settings['jobs'] as $job );
            {
                $this->crawler = self::$goutte->request('GET', $job['url']);

                foreach( $job['format'] as $format )
                {
                    $this->results[$filename][] =$this->iterateSelectors($format);
                }

            }
        }
    }


    public function getResults()
    {
        return $this->results;
    }

    public function getJobs( )
    {
        return $this->jobs;
    }


    public function setJobs( $jobs )
    {
         $this->jobs = $jobs;
    }

} 