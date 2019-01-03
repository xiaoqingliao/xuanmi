<?php
namespace App\Services;

use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TBinaryProtocol;
use videomod\VideoCalculatorClient;
use videomod\VideoOperation;

class VideoModService
{
    
    private static $client;
    
    private static function getClient() {
        if (!self::$client) {
            $socket = new TSocket('127.0.0.1', 9081);
            $transport = new TBufferedTransport($socket, 1024, 1024);
            $protocol = new TBinaryProtocol($transport);
            
            self::$client = new VideoCalculatorClient($protocol);
            
            $transport->open();
        }
        return self::$client;
    }
    
    public static function getCover($videoPath, $outputPath) {
        $client = self::getClient();
        try {
            $client->getCover($videoPath, $outputPath);
        } catch (VideoOperation $e) {
            throw new \Exception($e->why);
        }
    }
}

