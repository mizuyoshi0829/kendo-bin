<?php
    require_once '/var/www/html/admin/common/common.php';
    require_once '/var/www/html/admin/common/config.php';

    /**
     * ライン出力
     * @var string|array
     */
    function line($msg) {
        if (is_array($msg)) {
            foreach ($msg as $item) {
                line($item);
            }
        } else {
            $now = time();
            $y = sprintf( '%04d', intval( date( 'Y', $now ) ) );
            $m = sprintf( '%02d', intval( date( 'm', $now ) ) );
            $d = sprintf( '%02d', intval( date( 'd', $now ) ) );
            if( !is_dir( '/var/www/cgi-bin/kendo-realtime/log/'.$y ) ){
                mkdir( '/var/www/cgi-bin/kendo-realtime/log/'.$y );
            }
            if( !is_dir( '/var/www/cgi-bin/kendo-realtime/log/'.$y.'/'.$y.$m ) ){
                mkdir( '/var/www/cgi-bin/kendo-realtime/log/'.$y.'/'.$y.$m );
            }
            $fp = fopen( '/var/www/cgi-bin/kendo-realtime/log/'.$y.'/'.$y.$m.'/queue_'.$y.$m.$d.'.log', 'a' );
            fwrite( $fp, '['.date('Y/m/d H:i:s', $now).'] '.$msg."\n" );
            fclose( $fp );
        }
    }

    line('start');
    $url = 'http://133.125.40.139/realtime/realtime_api.php';
    $dbs = db_connect( DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME );
    for(;;){
        $sql = 'SELECT * FROM `realtime_queue` WHERE `deleted_at` is null ORDER BY `id` ASC';
        $list = db_query_list( $dbs, $sql );
        if( count($list) == 0 ){
            sleep(1);
            continue;
        }
        line('queue ('.count($list).')');
        foreach( $list as $lv ){
            if( $lv['file'] == '' ){ continue; }
            if( !file_exists( $lv['file'] ) ){ continue; }
            if( $lv['mode'] == 1 ){
                $data = [
                    'mode' => $lv['mode'],
                    'navi' => $lv['navi'],
                    'file' => '',
                    'place' => $lv['place'],
                    'file' => sprintf( '/realtime/%04d_%02d', $lv['navi'], $lv['place'] ),
                    'series' => $lv['series'],
                ];
                $context = new ZMQContext();
                $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
                $socket->connect("tcp://localhost:5555");
                $socket->send(json_encode($data));
                $socket2 = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher2');
                $socket2->connect("tcp://localhost:5556");
                $socket2->send(json_encode($data));
            } else if( $lv['mode'] == 2 ){
                $data = [
                    'mode' => $lv['mode'],
                    'navi' => $lv['navi'],
                    'place' => $lv['place'],
                    'value' => file_get_contents( $lv['file'] ),
                    'series' => $lv['series'],
                ];
                $data = http_build_query( $data, "", "&" );
                $header = array(
                    "Content-Type: application/x-www-form-urlencoded",
                    "Content-Length: ".strlen($data)
                );
                $options = [
                    'http' => [
                        'method' => 'POST',
                        'header' => implode("\r\n", $header),
                        'content' => $data,
                    ]
                ];
                //line($url);
                $options = stream_context_create( $options );
                $contents = file_get_contents( $url, false, $options );
            }
            $sql = 'update `realtime_queue` set `deleted_at`=NOW() where `id`=' . $lv['id'];
            db_query( $dbs, $sql );
        }
    }
    db_close( $dbs );
