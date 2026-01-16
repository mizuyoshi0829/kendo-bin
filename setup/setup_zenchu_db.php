<?php
/*
INSERT INTO `dantai_tournament` (`series`, `year`, `series_mw`, `no`, `advanced`, `sub_league`, `team_num`, `tournament_team_num`, `match_num`, `extra_match_num`, `extra_name`, `match_level`, `place_num`, `navi_index`, `match_offset`, `display_offset`, `display_place_offset`, `create_date`, `update_date`, `del`) VALUES
(70, 2025, 'm', 1, 0, 1, 64, 64, 63, 0, '', 6, 8, 13, 0, 0, 0, NOW(), NOW(), 0);

INSERT INTO `kojin_tournament` (`series`, `year`, `series_mw`, `no`, `player_num`, `tournament_player_num`, `match_num`, `extra_match_num`, `match_level`, `place_num`, `tournament_name`, `extra_name`, `relative`, `relative_start`, `relative_num`, `match_offset`, `display_offset`, `display_place_offset`, `create_date`, `update_date`, `del`) VALUES
(73, 2025, 'w', 1, 64, 64, 63, 0, 6, 8, '個人戦女子', '', 0, 0, 0, 0, 0, 0, NOW(), NOW(), 0),
(72, 2025, 'm', 1, 64, 64, 63, 0, 6, 8, '個人戦男子', '', 0, 0, 0, 0, 0, 0, NOW(), NOW(), 0);
define( 'ROOT_PASSWORD', 'tNson5C4LT8t' );
*/
    define( '__DATABASE_NAME__', 'keioffice_kendo' );

    function insert_table_data( $dbs, $table_name, $data, $include_id )
    {
        $dbs->query('TRUNCATE TABLE `'.$table_name.'`');
        $sql = 'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_KEY, EXTRA'
            . ' FROM information_schema.COLUMNS'
            . ' WHERE TABLE_SCHEMA = \'' . __DATABASE_NAME__ . '\''
            . ' AND TABLE_NAME = \'' . $table_name . '\'';
        //echo $sql."\n";
        $rs = $dbs->query( $sql );
    	if( $rs === false ){ return; }
        $schema = [];
        for(;;){
            $row = $rs->fetch_assoc();
            if( $row === false || $row === null ){ break; }
            if( !$include_id && $row['COLUMN_NAME'] == 'id' ){
                continue;
            }
            $schema[] = $row;
        }
        foreach( $data as $lv ){
            $columns = [];
            $values = [];
          //print_r($row);
            foreach( $schema as $row ){
                $columns[] = $row['COLUMN_NAME'];
                if(
                    $row['DATA_TYPE'] == 'text'
                    || $row['DATA_TYPE'] == 'datetime'
                    || $row['DATA_TYPE'] == 'timestamp'
                ){
                    if( !isset($lv[$row['COLUMN_NAME']]) || $lv[$row['COLUMN_NAME']] === null ){
                        if( $row['IS_NULLABLE'] == 'YES' ){
                            $values[] = 'null';
                        } else if( $row['COLUMN_DEFAULT'] === null ){
                            $values[] = '\'\'';
                        } else {
                            $values[] = '\'' . $row['COLUMN_DEFAULT'] . '\'';
                        }
                    } else {
                        $values[] = '\'' . $dbs->real_escape_string($lv[$row['COLUMN_NAME']]) . '\'';
                    }
                } else {
                    if( !isset($lv[$row['COLUMN_NAME']]) || $lv[$row['COLUMN_NAME']] === null ){
                        if( $row['IS_NULLABLE'] == 'YES' ){
                            $values[] = 'null';
                        } else if( $row['COLUMN_DEFAULT'] === null ){
                            $values[] = 0;
                        } else {
                            $values[] = $row['COLUMN_DEFAULT'];
                        }
                    } else {
                        $values[] = $lv[$row['COLUMN_NAME']];
                    }
                }
            }
            $sql = 'INSERT INTO `' . $table_name . '`'
                . ' (`' . implode('`,`',$columns) . '`)'
                . ' VALUES (' . implode(',',$values) . ')';
            echo $sql."\n";
            $dbs->query( $sql );
        }
    }

    $series = isset($argv['1']) ? intval($argv['1']): 0;
    echo $series;
    if( $series == 0 ){ exit(); }
    $year = isset($argv['2']) ? intval($argv['2']): 0;
    echo $year;
    if( $year == 0 ){ exit(); }
    $json = file_get_contents('https://i-kendo.net/zenchu/result/setup_zenchu_db_get.php?s='.$series.'&y='.$year);
    $ret = json_decode($json,true);
//    if( !$ret['result'] ){ exit(); }

    $dump_file = 'kendo.' . date('YmdHis') . '.sql';
    $dump = 'mysqldump --single-transaction -h localhost -u keioffice_kendo -phprzjntc keioffice_kendo >' . $dump_file;
    echo $dump;
    exec($dump);

    $dbs = new mysqli( 'localhost', 'keioffice_kendo', 'hprzjntc', __DATABASE_NAME__ );
    if( $dbs === false ){
        //接続失敗
        echo 'データベース接続に失敗しました。(1)';
        exit;
    }
    //データベース選択
    $dbs->set_charset( "utf8" );

    if( isset($ret['entry_info']) && count($ret['entry_info']) > 0 ){
        $dbs->query('TRUNCATE TABLE `entry_info`');
        $dbs->query('TRUNCATE TABLE `entry_field`');
        foreach( $ret['entry_info'] as $entry )
        {
            $sql = 'INSERT INTO `entry_info` (`series`, `year`, `disp_order`, `create_date`, `update_date`, `del`)'
                . ' VALUES ('
                . $entry['series'] . ',' . $year . ',' . $entry['disp_order'] . ',NOW(),NOW(),' . $entry['id'] . ')';
            echo $sql."\n";
            $dbs->query( $sql );
            $id = $dbs->insert_id;
            //$id = 1000;

            $values = [];
            foreach( $entry['fields'] as $field ){
                $v = '('
                    . $entry['series'] . ',' . $year . ',' . $id . ','
                    . "'" . $field['field'] . "',"
                    . "'',"
                    . "'" . $dbs->real_escape_string($field['data']) . "')";
                $values[] = $v;
            }
            $sql = 'INSERT INTO `entry_field` (`series`, `year`, `info`, `field`, `data3`, `data`) VALUES '
                . implode( ',', $values );
            $dbs->query( $sql );
            echo $sql."\n";
        }
    }
    if( isset($ret['entry_field_def']) && count($ret['entry_field_def']) > 0 ){
        $dbs->query('TRUNCATE TABLE `entry_field_def`');
        $values = [];
        foreach( $ret['entry_field_def'] as $def ){
            $v = [
                $def['series'],
                $def['year'],
                $def['field_order'],
                '\'' . $dbs->real_escape_string($def['field']) . '\'',
                '\'' . $dbs->real_escape_string($def['data']) . '\'',
                '\'' . $dbs->real_escape_string($def['name']) . '\'',
                '\'' . $dbs->real_escape_string($def['kind']) . '\'',
                '\'' . $dbs->real_escape_string($def['placeholder']) . '\'',
                '\'' . $dbs->real_escape_string($def['leader']) . '\'',
                '\'' . $dbs->real_escape_string($def['tail']) . '\'',
                $def['text_width'],
                '\'' . $dbs->real_escape_string($def['select_info']) . '\'',
                $def['span_next'],
                $def['float_left'],
                $def['required'],
                $def['excel_out_order'],
                '\'' . $dbs->real_escape_string($def['excel_title1']) . '\'',
                '\'' . $dbs->real_escape_string($def['excel_title2']) . '\'',
                '\'' . $dbs->real_escape_string($def['excel_title3']) . '\'',
                0
            ];
            $values[] = implode( ',', $v );
        }
        $sql = 'INSERT INTO `entry_field_def` '
            . '(`series`, `year`, `field_order`, `field`, `data`, `name`, `kind`, `placeholder`, `leader`, `tail`, `text_width`, `select_info`, `span_next`, `float_left`, `required`, `excel_out_order`, `excel_title1`, `excel_title2`, `excel_title3`, `del`)'
            . ' VALUES (' . implode('),(', $values) . ')';
        echo $sql."\n";
        $dbs->query($sql);
    }

    $dbs->query('TRUNCATE TABLE `dantai_match`');
    $dbs->query('TRUNCATE TABLE `one_match`');
    if( isset($ret['dantai_league']) && count($ret['dantai_league']) > 0 ){
        $dbs->query('TRUNCATE TABLE `dantai_league`');
        $dbs->query('TRUNCATE TABLE `dantai_league_team`');
        $dbs->query('TRUNCATE TABLE `dantai_league_match`');
        foreach( $ret['dantai_league'] as $league )
        {
            $sql = 'INSERT INTO `dantai_league` ('
                . '`series`,'
                . '`year`,'
                . '`series_mw`,'
                . '`no`,'
                . '`name`,'
                . '`team_num`,'
                . '`extra_match_exists`,'
                . '`match_num`,'
                . '`extra_match_num`,'
                . '`place_num`,'
                . '`advance_num`,'
                . '`match_offset`,'
                . '`display_offset`,'
                . '`display_place_offset`,'
                . '`place_match_info_array`,'
                . '`match_info_array`,'
                . '`chart_tbl_array`,'
                . '`chart_team_tbl_array`,'
                . '`create_date`,'
                . '`update_date`,'
                . '`del`'
                . ') VALUES ('
                . $league['series'] . ','
                . $league['year'] . ','
                . '\'' . $league['series_mw'] . '\','
                . $league['no'] . ','
                . '\'' . $league['name'] . '\','
                . $league['team_num'] . ','
                . $league['extra_match_exists'] . ','
                . $league['match_num'] . ','
                . $league['extra_match_num'] . ','
                . $league['place_num'] . ','
                . $league['advance_num'] . ','
                . $league['match_offset'] . ','
                . $league['display_offset'] . ','
                . $league['display_place_offset'] . ','
                . '\'' . $league['place_match_info_array'] . '\','
                . '\'' . $league['match_info_array'] . '\','
                . '\'' . $league['chart_tbl_array'] . '\','
                . '\'' . $league['chart_team_tbl_array'] . '\','
                . 'NOW(),NOW(),0)';
            echo $sql."\n";
            $dbs->query( $sql );
            $league_id = $dbs->insert_id;

            if( isset($league['dantai_league_team']) && count($league['dantai_league_team']) > 0 ){
                $sqlval = [];
                foreach($league['dantai_league_team'] as $team){
                    print_r($team);
                    $entry_id = 0;
                    if( $team['team'] !== null ){
                        $sql = 'SELECT * from `entry_info` where `series`=' . $team['series'] . ' and `year`=' . $year . ' and `disp_order`=' . $team['team'];
        	            $rs = $dbs->query( $sql );
                	    if( $rs === false ){ exit; }
                        $row = $rs->fetch_assoc();
                        print_r($row);
                        if( $row !== null ){
                            $entry_id = $row['id'];
                        }
                    }
                    $sqlval[] = '(' . $league_id . ',' . $team['league_team_index'] . ',' . $entry_id . ',NOW(),NOW(),0)';
                }
                $sql = 'INSERT INTO `dantai_league_team` ('
                    . '`league`,`league_team_index`,`team`,`create_date`,`update_date`,`del`'
                    . ') VALUES ' . implode(',',$sqlval);
                echo $sql."\n";
                $dbs->query( $sql );
            }
            if( isset($league['dantai_league_match']) && count($league['dantai_league_match']) > 0 ){
                $sqlval = [];
                foreach($league['dantai_league_match'] as $match){
                    $mids = [];
                    for( $i1 = 1; $i1 <= 6; $i1++ ){
                        $sql = 'INSERT INTO `one_match` (`create_date`, `update_date`, `del`)'
                            . ' VALUES (NOW(),NOW(),0)';
                        echo $sql."\n";
                        $dbs->query( $sql );
                        $mids[] = $dbs->insert_id;
                    }
                    $sql = 'INSERT INTO `dantai_match` ('
                        . '`place`,`place_match_no`,'
                        . '`match1`,`match2`,`match3`,`match4`,`match5`,`match6`,'
                        . '`create_date`,`update_date`,`del`'
                        . ') VALUES ('
                        . $match['place'] . ','
                        . $match['place_match_no'] . ','
                        . $mids[0] . ','
                        . $mids[1] . ','
                        . $mids[2] . ','
                        . $mids[3] . ','
                        . $mids[4] . ','
                        . $mids[5] . ','
                        . 'NOW(),NOW(),0)';
                    echo $sql."\n";
                    $dbs->query( $sql );
                    $match_id = $dbs->insert_id;
                    $sqlval[] = '(' . $league_id . ',' . $match['league_match_index'] . ',' . $match_id . ',NOW(),NOW(),0)';
                }
                $sql = 'INSERT INTO `dantai_league_match` ('
                    . '`league`,`league_match_index`,`match`,`create_date`,`update_date`,`del`'
                    . ') VALUES ' . implode(',',$sqlval);
                echo $sql."\n";
                $dbs->query( $sql );
            }
        }
    }

    if( isset($ret['dantai_tournament']) && count($ret['dantai_tournament']) > 0 ){
        $dbs->query('TRUNCATE TABLE `dantai_tournament`');
        $dbs->query('TRUNCATE TABLE `dantai_tournament_team`');
        $dbs->query('TRUNCATE TABLE `dantai_tournament_match`');
        foreach( $ret['dantai_tournament'] as $tournament )
        {
            $sql = 'INSERT INTO `dantai_tournament` ('
                . '`series`,'
                . '`year`,'
                . '`series_mw`,'
                . '`no`,'
                . '`advanced`,'
                . '`sub_league`,'
                . '`team_num`,'
                . '`tournament_team_num`,'
                . '`match_num`,'
                . '`extra_match_num`,'
                . '`extra_name`,'
                . '`match_level`,'
                . '`place_num`,'
                . '`navi_index`,'
                . '`match_offset`,'
                . '`display_offset`,'
                . '`display_place_offset`,'
                . '`create_date`,'
                . '`update_date`,'
                . '`del`'
                . ') VALUES ('
                . $tournament['series'] . ','
                . $tournament['year'] . ','
                . '\'' . $tournament['series_mw'] . '\','
                . $tournament['no'] . ','
                . $tournament['advanced'] . ','
                . $tournament['sub_league'] . ','
                . $tournament['team_num'] . ','
                . $tournament['tournament_team_num'] . ','
                . $tournament['match_num'] . ','
                . $tournament['extra_match_num'] . ','
                . '\'' . $tournament['extra_name'] . '\','
                . $tournament['match_level'] . ','
                . $tournament['place_num'] . ','
                . $tournament['navi_index'] . ','
                . $tournament['match_offset'] . ','
                . $tournament['display_offset'] . ','
                . $tournament['display_place_offset'] . ','
                . 'NOW(),NOW(),0)';
            echo $sql."\n";
            $dbs->query( $sql );
            $tournament_id = $dbs->insert_id;

            if( isset($tournament['dantai_tournament_team']) && count($tournament['dantai_tournament_team']) > 0 ){
                $sqlval = [];
                foreach($tournament['dantai_tournament_team'] as $team){
                    if( $team['team'] === null ){
                        $entry_id = 0;
                    } else {
                        $sql = 'SELECT * from `entry_info` where `series`=' . $team['series'] . ' and `year`=' . $year . ' and `disp_order`=' . $team['team'];
        	            $rs = $dbs->query( $sql );
                	    if( $rs === false ){ exit; }
                        $row = $rs->fetch_assoc();
                        $entry_id = $row['id'];
                    }
                    $sqlval[] = '(' . $tournament_id . ',' . $team['tournament_team_index'] . ',' . $entry_id . ',NOW(),NOW(),0)';
                }
                $sql = 'INSERT INTO `dantai_tournament_team` ('
                    . '`tournament`,`tournament_team_index`,`team`,`create_date`,`update_date`,`del`'
                    . ') VALUES ' . implode(',',$sqlval);
                echo $sql."\n";
                $dbs->query( $sql );
            }
            if( isset($tournament['dantai_tournament_match']) && count($tournament['dantai_tournament_match']) > 0 ){
                $sqlval = [];
                foreach($tournament['dantai_tournament_match'] as $match){
                    $mids = [];
                    for( $i1 = 1; $i1 <= 6; $i1++ ){
                        $sql = 'INSERT INTO `one_match` (`create_date`, `update_date`, `del`)'
                            . ' VALUES (NOW(),NOW(),0)';
                        echo $sql."\n";
                        $dbs->query( $sql );
                        $mids[] = $dbs->insert_id;
                    }
                    $sql = 'INSERT INTO `dantai_match` ('
                        . '`place`,`place_match_no`,'
                        . '`match1`,`match2`,`match3`,`match4`,`match5`,`match6`,'
                        . '`create_date`,`update_date`,`del`'
                        . ') VALUES ('
                        . $match['place'] . ','
                        . $match['place_match_no'] . ','
                        . $mids[0] . ','
                        . $mids[1] . ','
                        . $mids[2] . ','
                        . $mids[3] . ','
                        . $mids[4] . ','
                        . $mids[5] . ','
                        . 'NOW(),NOW(),0)';
                    echo $sql."\n";
                    $dbs->query( $sql );
                    $match_id = $dbs->insert_id;
                    $sqlval[] = '(' . $tournament_id . ',' . $match['tournament_match_index'] . ',' . $match_id . ',NOW(),NOW(),0)';
                }
                $sql = 'INSERT INTO `dantai_tournament_match` ('
                    . '`tournament`,`tournament_match_index`,`match`,`create_date`,`update_date`,`del`'
                    . ') VALUES ' . implode(',',$sqlval);
                echo $sql."\n";
                $dbs->query( $sql );
            }
        }
    }

    if( isset($ret['kojin_tournament']) && count($ret['kojin_tournament']) > 0 ){
        $dbs->query('TRUNCATE TABLE `kojin_tournament`');
        $dbs->query('TRUNCATE TABLE `kojin_tournament_player`');
        $dbs->query('TRUNCATE TABLE `kojin_tournament_match`');
        $dbs->query('TRUNCATE TABLE `kojin_match`');
        foreach( $ret['kojin_tournament'] as $tournament )
        {
            $sql = 'INSERT INTO `kojin_tournament` ('
                . '`series`,'
                . '`year`,'
                . '`series_mw`,'
                . '`no`,'
                . '`player_num`,'
                . '`tournament_player_num`,'
                . '`match_num`,'
                . '`extra_match_num`,'
                . '`match_level`,'
                . '`place_num`,'
                . '`tournament_name`,'
                . '`extra_name`,'
                . '`relative`,'
                . '`relative_start`,'
                . '`relative_num`,'
                . '`match_offset`,'
                . '`display_offset`,'
                . '`display_place_offset`,'
                . '`create_date`,'
                . '`update_date`,'
                . '`del`'
                . ') VALUES ('
                . $tournament['series'] . ','
                . $tournament['year'] . ','
                . '\'' . $tournament['series_mw'] . '\','
                . $tournament['no'] . ','
                . $tournament['player_num'] . ','
                . $tournament['tournament_player_num'] . ','
                . $tournament['match_num'] . ','
                . $tournament['extra_match_num'] . ','
                . $tournament['match_level'] . ','
                . $tournament['place_num'] . ','
                . '\'' . $tournament['tournament_name'] . '\','
                . '\'' . $tournament['extra_name'] . '\','
                . $tournament['relative'] . ','
                . $tournament['relative_start'] . ','
                . $tournament['relative_num'] . ','
                . $tournament['match_offset'] . ','
                . $tournament['display_offset'] . ','
                . $tournament['display_place_offset'] . ','
                . 'NOW(),NOW(),0)';
            echo $sql."\n";
            $dbs->query( $sql );
            $tournament_id = $dbs->insert_id;

            if( isset($tournament['kojin_tournament_player']) && count($tournament['kojin_tournament_player']) > 0 ){
                $sqlval = [];
                foreach($tournament['kojin_tournament_player'] as $team){
                    if( $team['team'] === null ){
                        $entry_id = 0;
                    } else {
                        $sql = 'SELECT * from `entry_info` where `series`=' . $team['series'] . ' and `year`=' . $year . ' and `disp_order`=' . $team['team'];
        	            $rs = $dbs->query( $sql );
                	    if( $rs === false ){ exit; }
                        $row = $rs->fetch_assoc();
                        $entry_id = $row['id'];
                    }
                    $sqlval[] = '(' . $tournament_id . ',' . $team['tournament_player_index'] . ',' . $entry_id . ',' . $team['player'] . ',NOW(),NOW(),0)';
                }
                $sql = 'INSERT INTO `kojin_tournament_player` ('
                    . '`tournament`,`tournament_player_index`,`team`,`player`,`create_date`,`update_date`,`del`)'
                    . ' VALUES ' . implode(',',$sqlval);
                echo $sql."\n";
                $dbs->query( $sql );
            }
            if( isset($tournament['kojin_tournament_match']) && count($tournament['kojin_tournament_match']) > 0 ){
                $sqlval = [];
                foreach($tournament['kojin_tournament_match'] as $match){
                    $sql = 'INSERT INTO `one_match` (`create_date`, `update_date`, `del`)'
                        . ' VALUES (NOW(),NOW(),0)';
                    echo $sql."\n";
                    $dbs->query( $sql );
                    $one_id = $dbs->insert_id;
                    $sql = 'INSERT INTO `kojin_match` ('
                        . '`place`,`place_match_no`,`match`,'
                        . '`create_date`,`update_date`,`del`'
                        . ') VALUES ('
                        . '\'' . $match['place'] . '\','
                        . $match['place_match_no'] . ','
                        . $one_id . ','
                        . 'NOW(),NOW(),0)';
                    echo $sql."\n";
                    $dbs->query( $sql );
                    $match_id = $dbs->insert_id;
                    $sqlval[] = '(' . $tournament_id . ',' . $match['tournament_match_index'] . ',' . $match_id . ',NOW(),NOW(),0)';
                }
                $sql = 'INSERT INTO `kojin_tournament_match` ('
                    . '`tournament`,`tournament_match_index`,`match`,`create_date`,`update_date`,`del`'
                    . ') VALUES ' . implode(',',$sqlval);
                echo $sql."\n";
                $dbs->query( $sql );
            }
        }
    }

    if( isset($ret['series']) ){
        $data = [ $ret['series'] ];
        insert_table_data( $dbs, 'series', $data, true );
    }

    if( isset($ret['navi_input_info']) ){
        insert_table_data( $dbs, 'navi_input_info', $ret['navi_input_info'], false );
    }
