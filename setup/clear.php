<?php
    require_once dirname(dirname(dirname(__FILE__))).'/kendo-server/admin/common/common.php';
    require_once dirname(dirname(dirname(__FILE__))).'/kendo-server/admin/common/config.php';

    $sql = 'select `dantai_match`.*,'
        . '`dantai_league_match`.`league` as `league`,'
        . '`dantai_league_match`.`league_match_index` as `league_match_index`,'
        . '`dantai_league`.`series` as `series`'
        . ' from `dantai_match`'
        . ' inner join `dantai_league_match` on `dantai_league_match`.`match`=`dantai_match`.`id`'
        . ' inner join `dantai_league` on `dantai_league_match`.`league`=`dantai_league`.`id`'
        . ' where `dantai_league`.`series`=7 and `dantai_league`.`year`=2026 and `dantai_league`.`del`=0 order by `dantai_match`.`id` asc';
    $list = db_query_list( $dbs, $sql );
	foreach( $list as $lv ){
        for( $match = 1; $match <= 5; $match++ ){
            if( $lv['league_match_index'] <= 3 ){
    			$sql = 'update `one_match`'
	    			. ' set `player1`=' . $match . ',`player2`=' . $match
    				. ' where `id`=' . $lv['match'.$match];
                echo $sql . "\n";
            }
        }
    }
