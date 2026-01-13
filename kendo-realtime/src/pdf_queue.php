<?php
    require_once '/var/www/html/admin/common/common.php';
    require_once '/var/www/html/admin/common/config.php';
    require_once '/var/www/html/admin/class/page.php';
    require_once '/var/www/html/admin/class/page_dantai_entry.php';
    require_once '/var/www/html/admin/class/page_dantai_match.php';
    require_once '/var/www/html/admin/class/page_dantai_league.php';
    require_once '/var/www/html/admin/class/page_dantai_tournament.php';

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
            $fp = fopen( '/var/www/cgi-bin/kendo-realtime/log/'.$y.'/'.$y.$m.'/pdf_queue_'.$y.$m.$d.'.log', 'a' );
            fwrite( $fp, '['.date('Y/m/d H:i:s', $now).'] '.$msg."\n" );
            fclose( $fp );
        }
    }


    $sapi_type = php_sapi_name();
    line($sapi_type);
    
    if (substr($sapi_type, 0, 3) != 'cli') {
        line('CLI 版の PHP を使用していません');
        exit();
    }

    /**
     * 二重起動排除
     */
    // ロックファイルの確認
    // ファイルがあったら実際にプロセスがあるか確認。
    $lock_file = dirname(__FILE__) . '/pdf_queue_lock';
    if (is_file($lock_file)) {
        // 同じコマンドを起動しているかの確認
        // 他のプロセスを探し、2つ以上あった場合終了
        // 下記のps, grepも収集してしまうのでgerpを含む行は排除した数で調査
        exec('ps aux | grep "pdf_queue.php" | grep -v "grep"', $output, $result);
        line(print_r($output,true));
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

    $objPage = new form_page();
    $objMatch = new form_page_dantai_match( $objPage );

    $dbs = db_connect( DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME );
    for(;;){
        $sql = 'SELECT * FROM `pdf_queue` WHERE `deleted_at` is null ORDER BY `id` ASC';
        $list = db_query_list( $dbs, $sql );
        line('queue ('.count($list).')');
        if( count($list) == 0 ){
            sleep(1);
            continue;
        }
        foreach( $list as $lv ){
            line('   queue ('.$lv['navi_id'].':'.$lv['place'].':'.$lv['place_match_no'].')');
            if( $lv['place'] == 0 ){
                if( $lv['place_match_no'] == 0 ){
                    $objMatch->output_dantai_all_match_pdf( $lv['navi_id'] );
                } else {
                    $allnavi = $objPage->get_series_place_all_navi_data( $lv['navi_id'] );
                    foreach( $allnavi as $nv ){
                        line('   ---- queue ('.$lv['navi_id'].':'.$nv['place_no'].':'.$nv['place_match_no'].')');
                        if( $nv['place_match_no'] >= 1000 ){ continue; }
                        $objMatch->output_dantai_one_match_pdf2($lv['navi_id'],$nv['place_no'],$nv['place_match_no']);
                    }
                }
            } else {
                $objMatch->output_dantai_one_match_pdf2($lv['navi_id'],$lv['place'],$lv['place_match_no']);
            }
            $sql = 'update `pdf_queue` set `deleted_at`=NOW() where `id`=' . $lv['id'];
            db_query( $dbs, $sql );
            line('   queue ('.$lv['navi_id'].':'.$lv['place'].':'.$lv['place_match_no'].')----------------');
        }
    }
    db_close( $dbs );

    /**
     * 二重起動排除の終了処理
     */
    // ロックファイルの削除
    unlink($lock_file);
