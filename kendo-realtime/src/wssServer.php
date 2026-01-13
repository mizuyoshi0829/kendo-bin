<?php
    namespace App;

    use React\EventLoop;
    use React\Socket;
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    use Ratchet\Wamp\WampServer;
    use App\Realtime;

    require dirname(__DIR__) . '/vendor/autoload.php';

    $loop = EventLoop\Factory::create();
    $realtime = new Realtime;

    // Listen for the web server to make a ZeroMQ push after an ajax request
    $context = new \React\ZMQ\Context($loop);
    $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
    $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
    $pull->on('message', array($realtime, 'onCommand'));

    // Binding to 0.0.0.0 means remotes can connect
    $webSock = new Socket\SecureServer(
        new Socket\Server('0.0.0.0:3000', $loop), $loop, []
    );
    $webServer = new IoServer(
        new HttpServer(
            new WsServer(
                $realtime
            )
        ),
        $webSock
    );
    $loop->run();

/*
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Realtime()
            )
        ),
        3000
    );

    $server->run();
*/