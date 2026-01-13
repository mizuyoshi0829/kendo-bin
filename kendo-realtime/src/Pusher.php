<?php
namespace App;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface {

    protected $subscribedTopics = array();

    public function onSubscribe(ConnectionInterface $conn, $topic) {
    
        $subject = $topic->getId();
        $this->subscribedTopics[$subject] = $topic;

    }
    
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }
    
    public function onOpen(ConnectionInterface $conn) {
       echo "New Connection ($conn->resourceId)\n"; 
    }
    
    public function onClose(ConnectionInterface $conn) {
       echo "Close Connection ($conn->resourceId)\n";    
    }
    
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
    }
    
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }

    public function onCommand($command){

        echo $command;
//        $commandData = json_decode($command, true);
        
//        $topic = $this->subscribedTopics[$commandData['command']];
//        $topic->broadcast($commandData);
    
    }

}
