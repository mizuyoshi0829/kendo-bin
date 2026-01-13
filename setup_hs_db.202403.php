<?php
/*
INSERT INTO `dantai_league` (`series`, `year`, `series_mw`, `no`, `name`, `team_num`, `extra_match_exists`, `match_num`, `extra_match_num`, `place_num`, `advance_num`, `match_offset`, `display_offset`, `display_place_offset`, `place_match_info_array`, `match_info_array`, `chart_tbl_array`, `chart_team_tbl_array`, `create_date`, `update_date`, `del`) VALUES
(67, 202403, 'w', 1, 'Aリーグ', 4, 0, 6, 6, 8, 2, 0, 0, 0, '0,1,2,3,4,5', '0,1,0,2,0,3,1,2,1,3,2,3', '0,1,2,3,1,0,4,5,2,4,0,6,3,5,6,0', '0,1,1,1,2,0,1,1,2,2,0,1,2,2,2,0', '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0),
(67, 202403, 'w', 2, 'Bリーグ', 4, 0, 6, 6, 8, 2, 0, 0, 0, '0,1,2,3,4,5', '0,1,0,2,0,3,1,2,1,3,2,3', '0,1,2,3,1,0,4,5,2,4,0,6,3,5,6,0', '0,1,1,1,2,0,1,1,2,2,0,1,2,2,2,0', '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0),
(67, 202403, 'w', 3, 'Cリーグ', 4, 0, 6, 6, 8, 2, 0, 0, 0, '0,1,2,3,4,5', '0,1,0,2,0,3,1,2,1,3,2,3', '0,1,2,3,1,0,4,5,2,4,0,6,3,5,6,0', '0,1,1,1,2,0,1,1,2,2,0,1,2,2,2,0', '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0),
(67, 202403, 'w', 4, 'Dリーグ', 4, 0, 6, 6, 8, 2, 0, 0, 0, '0,1,2,3,4,5', '0,1,0,2,0,3,1,2,1,3,2,3', '0,1,2,3,1,0,4,5,2,4,0,6,3,5,6,0', '0,1,1,1,2,0,1,1,2,2,0,1,2,2,2,0', '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0),
(66, 202403, 'm', 1, 'Aリーグ', 5, 0, 10, 10, 8, 3, 0, 0, 0, '0,1,2,3,4,5,6,7,8,9', '0,1,0,2,0,3,0,4,1,2,1,3,1,4,2,3,2,4,3,4', '0,1,2,3,4,1,0,5,6,7,2,5,0,8,9,3,6,8,0,10,4,7,9,10,0', '0,1,1,1,1,2,0,1,1,1,2,2,0,1,1,2,2,2,0,1,2,2,2,2,0', '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0),
(66, 202403, 'm', 2, 'Bリーグ', 6, 0, 15, 15, 8, 3, 0, 0, 0, '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14', '0,1,0,2,0,3,0,4,0,5,1,2,1,3,1,4,1,5,2,3,2,4,2,5,3,4,3,5,4,5', '0,1,2,3,4,5,1,0,6,7,8,9,2,6,0,10,11,12,3,7,10,0,13,14,4,8,11,13,0,15,5,9,12,14,15,0', '0,1,1,1,1,1,2,0,1,1,1,1,2,2,0,1,1,1,2,2,2,0,1,1,2,2,2,2,0,1,2,2,2,2,2,0', '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0),
(66, 202403, 'm', 3, 'Cリーグ', 6, 0, 15, 15, 8, 3, 0, 0, 0, '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14', '0,1,0,2,0,3,0,4,0,5,1,2,1,3,1,4,1,5,2,3,2,4,2,5,3,4,3,5,4,5', '0,1,2,3,4,5,1,0,6,7,8,9,2,6,0,10,11,12,3,7,10,0,13,14,4,8,11,13,0,15,5,9,12,14,15,0', '0,1,1,1,1,1,2,0,1,1,1,1,2,2,0,1,1,1,2,2,2,0,1,1,2,2,2,2,0,1,2,2,2,2,2,0', '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0),
(66, 202403, 'm', 4, 'Dリーグ', 5, 0, 10, 10, 8, 3, 0, 0, 0, '0,1,2,3,4,5,6,7,8,9', '0,1,0,2,0,3,0,4,1,2,1,3,1,4,2,3,2,4,3,4', '0,1,2,3,4,1,0,5,6,7,2,5,0,8,9,3,6,8,0,10,4,7,9,10,0', '0,1,1,1,1,2,0,1,1,1,2,2,0,1,1,2,2,2,0,1,2,2,2,2,0', '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0);

INSERT INTO `dantai_tournament` (`series`, `year`, `series_mw`, `no`, `advanced`, `sub_league`, `team_num`, `tournament_team_num`, `match_num`, `extra_match_num`, `extra_name`, `match_level`, `place_num`, `navi_index`, `match_offset`, `display_offset`, `display_place_offset`, `create_date`, `update_date`, `del`) VALUES
(67, 202403, 'w', 1, 0, 1, 8, 8, 7, 1, '三位決定戦', 3, 8, 12, 0, 0, 0, '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0),
(66, 202403, 'm', 1, 0, 1, 16, 16, 15, 1, '三位決定戦', 4, 8, 12, 0, 0, 0, '2024-06-04 18:34:18', '2024-06-04 09:34:18', 0);

INSERT INTO `kojin_tournament` (`series`, `year`, `series_mw`, `no`, `player_num`, `tournament_player_num`, `match_num`, `extra_match_num`, `match_level`, `place_num`, `tournament_name`, `extra_name`, `relative`, `relative_start`, `relative_num`, `match_offset`, `display_offset`, `display_place_offset`, `create_date`, `update_date`, `del`) VALUES
(69, 202403, 'w', 1, 128, 128, 127, 1, 7, 8, '', '三位決定戦', 0, 0, 0, 0, 0, 0, '2024-06-04 18:34:18', '2024-06-14 13:15:05', 0),
(68, 202403, 'm', 1, 256, 256, 255, 1, 8, 8, '', '三位決定戦', 0, 0, 0, 0, 0, 0, '2024-06-04 18:34:18', '2024-06-14 13:15:07', 0);
*/
    require_once dirname(__FILE__).'/setup_hs_db_data.202403.php';

    $dbs = new mysqli( 'localhost', 'keioffice_kendo', 'hprzjntc', 'keioffice_kendo' );
//print_r($dbs);
    if( $dbs === false ){
        //接続失敗
        echo 'データベース接続に失敗しました。(1)';
        exit;
    }
    //データベース選択
    $dbs->set_charset( "utf8" );
/*
    foreach( $entry_info as $entry )
    {
        $sql = 'INSERT INTO `entry_info` (`series`, `year`, `disp_order`, `create_date`, `update_date`, `del`)'
            . ' VALUES ('
            . $entry[0] . ',202403,' . $entry[1] . ',NOW(),NOW(),0)';
        echo $sql."\n";
        $dbs->query( $sql );
        $id = $dbs->insert_id;
        //$id = 1000;

        $values = [];
        foreach( $entry[2] as $field ){
            $v = '('
                . $field[0] . ',202403,' . $id . ','
                . "'" . $field['1'] . "',"
                . "'" . $dbs->real_escape_string($field['2']) . "',"
                . "'" . $dbs->real_escape_string($field['3']) . "')";
            $values[] = $v;
        }
        $sql = 'INSERT INTO `entry_field` (`series`, `year`, `info`, `field`, `data3`, `data`) VALUES '
            . implode( ',', $values );
        $dbs->query( $sql );
        echo $sql."\n";
    }
*/
/**/
    foreach( $dantai_league_team as $team )
    {
        $sql = 'SELECT * from `dantai_league` where `series`=' . $team[0] . ' and `year`=202403 and `no`=' . $team[1];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();

        $sql = 'SELECT * from `dantai_league_team` where `league`=' . $row['id'] . ' and `league_team_index`=' . $team[2];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();
        $team_id = $row['id'];

        $sql = 'SELECT * from `entry_info` where `series`=' . $team[0] . ' and `year`=202403 and `disp_order`=' . $team[3];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();
        $entry_id = $row['id'];

        $sql = 'UPDATE `dantai_league_team` set `team`=' . $entry_id . ' where `id`=' . $team_id;
        echo $sql."\n";
        $dbs->query( $sql );
    }
    foreach( $kojin_tournament_player as $player )
    {
        $sql = 'SELECT * from `kojin_tournament` where `series`=' . $player[0] . ' and `year`=202403 and `no`=' . $player[1];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();

        $sql = 'SELECT * from `kojin_tournament_player` where `tournament`=' . $row['id'] . ' and `tournament_player_index`=' . $player[2];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();
        $player_id = $row['id'];

        $sql = 'SELECT * from `entry_info` where `series`=' . $player[0] . ' and `year`=202403 and `disp_order`=' . $player[3];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();
        $entry_id = $row['id'];

        $sql = 'UPDATE `kojin_tournament_player`'
            . ' set `team`=' . $entry_id . ',`player`=' . $player[4]
            . ' where `id`=' . $player_id;
        echo $sql."\n";
        $dbs->query( $sql );
    }
    foreach( $dantai_league_match as $match )
    {
        $sql = 'SELECT * from `dantai_league` where `series`=' . $match[0] . ' and `year`=202403 and `no`=' . $match[1];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();

        $sql = 'SELECT * from `dantai_league_match` where `league`=' . $row['id'] . ' and `league_match_index`=' . $match[2];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();
        $match_id = $row['match'];

        $sql = 'UPDATE `dantai_match`'
            . ' set `place`=' . $match[3] . ',`place_match_no`=' . $match[4]
            . ' where `id`=' . $match_id;
        echo $sql."\n";
        $dbs->query( $sql );
    }
    foreach( $dantai_tournament_match as $match )
    {
        $sql = 'SELECT * from `dantai_tournament` where `series`=' . $match[0] . ' and `year`=202403 and `no`=' . $match[1];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();

        $sql = 'SELECT * from `dantai_tournament_match` where `tournament`=' . $row['id'] . ' and `tournament_match_index`=' . $match[2];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();
        $match_id = $row['match'];

        $sql = 'UPDATE `dantai_match`'
            . ' set `place`=' . $match[3] . ',`place_match_no`=' . $match[4]
            . ' where `id`=' . $match_id;
        echo $sql."\n";
        $dbs->query( $sql );
    }
    foreach( $kojin_tournament_match as $match )
    {
        $sql = 'SELECT * from `kojin_tournament` where `series`=' . $match[0] . ' and `year`=202403 and `no`=' . $match[1];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();

        $sql = 'SELECT * from `kojin_tournament_match` where `tournament`=' . $row['id'] . ' and `tournament_match_index`=' . $match[2];
    	$rs = $dbs->query( $sql );
	    if( $rs === false ){ exit; }
        $row = $rs->fetch_assoc();
        $match_id = $row['match'];

        $sql = 'UPDATE `kojin_match`'
            . ' set `place`=' . ($match[3]=='no_match' ? "'no_match'": $match[3])
              . ',`place_match_no`=' . $match[4]
            . ' where `id`=' . $match_id;
        echo $sql."\n";
        $dbs->query( $sql );
    }
/**/
