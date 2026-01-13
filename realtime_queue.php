<?php
    require_once dirname(dirname(__FILE__)).'/html/admin/common/common.php';
    require_once dirname(dirname(__FILE__)).'/html/admin/common/config.php';

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
            if( !is_dir( dirname(dirname(__FILE__)).'/html/result/log/'.$y ) ){
                mkdir( dirname(dirname(__FILE__)).'/html/result/log/'.$y );
            }
            if( !is_dir( dirname(dirname(__FILE__)).'/html/result/log/'.$y.'/'.$y.$m ) ){
                mkdir( dirname(dirname(__FILE__)).'/html/result/log/'.$y.'/'.$y.$m );
            }
            $fp = fopen( dirname(dirname(__FILE__)).'/html/result/log/'.$y.'/'.$y.$m.'/queue_'.$y.$m.$d.'.log', 'a' );
            fwrite( $fp, '['.date('Y/m/d H:i:s', $now).'] '.$msg."\n" );
            fclose( $fp );
        }
    }

    line('start');
    $sapi_type = php_sapi_name();
    if (substr($sapi_type, 0, 3) != 'cli') {
        line('CLI 版の PHP を使用していません');
        exit();
    }

    /**
     * 二重起動排除
     */
    // ロックファイルの確認
    // ファイルがあったら実際にプロセスがあるか確認。
    $lock_file = '/var/www/cgi-bin/result_queue_lock';
    if (is_file($lock_file)) {
        // 同じコマンドを起動しているかの確認
        // 他のプロセスを探し、2つ以上あった場合終了
        // 下記のps, grepも収集してしまうのでgerpを含む行は排除した数で調査
        exec('ps aux | grep "realtime_queue.php" | grep -v "grep"', $output, $result);
//print_r($output);
        foreach ($output as $key => $row) {
            if (strpos($row, 'grep') !== false) {
                unset($output[$key]);
            }
        }
        if (count($output) > 1) {
            line('二重起動と判定されました。この処理を終了します。');
            exit();
        }
    }
    // ロックファイル作成
    touch($lock_file);

    sleep(1);
    $url = 'http://133.125.40.139/realtime/realtime_api.php';
    $dbs = db_connect( DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME );
    for(;;){
        $sql = 'SELECT * FROM `realtime_queue` WHERE `deleted_at` is null ORDER BY `id` ASC';
        $list = db_query_list( $dbs, $sql );
        line('queue ('.count($list).')');
        if( count($list) == 0 ){ break; }
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
                //$contents = file_get_contents( $url, false, $options );
            }
            $sql = 'update `realtime_queue` set `deleted_at`=NOW() where `id`=' . $lv['id'];
            db_query( $dbs, $sql );
        }
    }
    db_close( $dbs );

    /**
     * 二重起動排除の終了処理
     */
    // ロックファイルの削除
    unlink($lock_file);
