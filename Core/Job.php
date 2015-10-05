<?php

namespace ptolemic\sifter\Core;

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
        $formatClass = "ptolemic\\sifter\\Core\\Adapters\\Config\\".$format;
        self::$config[$format] = new  $formatClass();

        $jobList = scandir($sourceDirectory);
        foreach($jobList as  $filename) {
            if( $filename == '.' || $filename == '..') continue;
            $this->jobs[$filename] = self::$config[$format]->load($sourceDirectory.$filename);
        }
    }

    private function iterateSelectors($task)
    {
        $store = array('data' => [ ], "total" => 0);
        foreach( $task['results'] as $payload)
        {
            $store['data'] = array_merge($store['data'], $this->iterateSubSelectors($task['container'], $payload));

        }

        //Hack in the total , just speed. Not a final flexible version
        if( isset($task['total']) )
        {
            $store['total'] = count($store['data']);
        }

       return $store;
    }

    private function iterateSubSelectors($container, $payload)
    {
        $nodes = $this->crawler->filter($container);
        $results = array();

        $collect = function ( $node, $i) use ($payload, &$results)  {

            $result = new \stdClass();

            foreach($payload as $label => $selector)
            {
                if( is_string($selector) ) {
                    $content = trim(strip_tags($node->filter($selector)->text()));
                    if($label == "unit_price" ) $content = preg_filter( array("/£/", "/[a-z]/", "/\//"),"", $content);
                    $result->$label = $content;

                }elseif( is_array($selector) ) {

                    switch($selector['type']) {
                        case "link":
                            $link = $node->filter($selector['context'])->link();
                            $nextPageCrawler = self::$goutte->click($link);

                            if($selector['target'] == 'content_size')
                            {
                                $result->$label = intval(ceil(strlen(self::$goutte->getResponse()->getContent())/1024));

                            }else {
                                $content = $nextPageCrawler->filter($selector['target']);
                                $result->$label = trim(strip_tags($content->text()));
                            }
                    }
                }

            }

            $results[] = $result;

        };

        $nodes->each($collect);

        return $results;
    }

    public function process()
    {
        foreach( $this->jobs  as $filename => $settings ) {

            foreach($settings['jobs'] as $job );
            {
                $this->crawler = self::$goutte->request('GET', $job['url']);

                foreach( $job['format'] as $format )
                {
                    $this->results[$filename] = $this->iterateSelectors($format);
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