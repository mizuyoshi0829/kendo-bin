<?php
namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\Http\HttpServerInterface;
use Ratchet\ConnectionInterface;
use Psr\Http\Message\RequestInterface;

class Realtime implements MessageComponentInterface {

    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null ) {
        $this->__output_log( 'New Connection (' . $conn->resourceId . ':' . count($this->clients) . ')' );
        $this->clients[$conn] = [
            'mode' => 0,
            'navi' => 0,
            'places' => [],
            'init' => false,
        ];
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        //echo $msg;
        //print_r($from);
        $this->__output_log( 'onMessage: ' . $msg );
        foreach ($this->clients as $client) {
            if ($from !== $client) { continue; }
            if( $this->clients[$from]['init'] ){ break; }
            $j = json_decode($msg, true);
            $client_data = [
                'mode' => isset($j['mode']) ? intval($j['mode']) : 0,
                'navi' => isset($j['navi']) ? intval($j['navi']) : 0,
                'places' => [],
                'init' => true,
            ];
            $places = isset($j['places']) ? $j['places'] : [];
            if( $client_data['mode'] == 0 ){ break; }
            if( $client_data['navi'] == 0 ){ break; }
            if( !is_array($places) || count($places) == 0 ){
                break;
            }
            foreach( $places as $place ){
                if( $client_data['mode'] == 1 ){
                    $p = intval($place);
                    if( $p == 0 ){ continue; }
                    $client_data['places'][] = $p;
                    $file = sprintf( '/var/www/html/result/realtime/%04d_%02d.html', $client_data['navi'], $p );
                } else {
                    $p = htmlentities($place);
                    if( $p == '' ){ continue; }
                    $client_data['places'][] = $p;
                    $file = sprintf( '/www/realtime/result/%04d_%s.html', $client_data['navi'], $p );
                }
                if( $file == '' ){ continue; }
                if( !file_exists($file) ){ continue; }
                $data = [
                    'mode' => $client_data['mode'],
                    'navi' => $client_data['navi'],
                    'place' => $p,
                    'html' => file_get_contents( $file ),
                ];
                $client->send(json_encode($data));
            }
            $this->clients[$from] = $client_data;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    public function db_loop() {
    }

    public function onCommand($command)
    {
        //echo $command;
        $commandData = json_decode($command, true);
        $data = [
            'mode' => isset($commandData['mode']) ? intval($commandData['mode']) : 0,
            'navi' => isset($commandData['navi']) ? intval($commandData['navi']) : 0,
            'place' => 0,
            'html' => '',
        ];
        $this->__output_log( 'onCommand: ' . $data['mode'] . ',' . $data['navi'] );
        if( $data['mode'] == 0 ){ return; }
        if( $data['navi'] == 0 ){ return; }
        if( $data['mode'] == 1 ){
            $data['place'] = isset($commandData['place']) ? intval($commandData['place']) : 0;
            if( $data['place'] == 0 ){ return; }
            $file = sprintf( '/var/www/html/result/realtime/%04d_%02d.html', $data['navi'], $data['place'] );
        } else {
            $data['place'] = isset($commandData['place']) ? htmlentities($commandData['place']) : '';
            if( $data['place'] == '' ){ return; }
            $file = dirname(dirname(dirname(__FILE__))).sprintf( '/www/realtime/result/%04d_%s.html', $data['navi'], $data['place'] );
        }
        if( !file_exists($file) ){ return; }
        $data['html'] = file_get_contents( $file );
        foreach ($this->clients as $client) {
            $client_data = $this->clients[$client];
            if( $client_data['mode'] != $data['mode'] ){ continue; }
            if( $client_data['navi'] != $data['navi'] ){ continue; }
            foreach( $client_data['places'] as $place ){
                if( $place !== $data['place'] ){ continue; }
                $client->send(json_encode($data));
            }
        }
    }
    private function __output_log( $msg )
    {
        $now = time();
        $y = sprintf( '%04d', intval( date( 'Y', $now ) ) );
        $m = sprintf( '%02d', intval( date( 'm', $now ) ) );
        $d = sprintf( '%02d', intval( date( 'd', $now ) ) );
        $log_path = dirname(dirname(__FILE__)).'/log/';
        if( !is_dir( $log_path.$y ) ){
            mkdir( $log_path.$y );
        }
        if( !is_dir( $log_path.$y.'/'.$y.$m ) ){
            mkdir( $log_path.$y.'/'.$y.$m );
        }
        $fp = fopen( $log_path.$y.'/'.$y.$m.'/'.$y.$m.$d.'.log', 'a' );
        fwrite( $fp, '['.date('Y/m/d H:i:s', $now)."]--------------------------------------------------\n" );
        fwrite( $fp, 'server:'.print_r( $_SERVER, true )."\n" );
        fwrite( $fp, 'msg:'.$msg."\n\n" );
        fclose( $fp );
    
    }

}
