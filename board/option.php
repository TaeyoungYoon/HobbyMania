<?php
    /*-- 게시판 구분 controller --*/
    
    require_once ('../db/db_info.php') ;

    $c = new db_board();

    $select_category = array();
    $select_level = array();
    $c_html = '' ;
    $l_html = '' ;
    $sub_html = '' ;
    
    $category = $c->selectBoardOption('board');
    $levelOption = $c->selectBoardOption('level');
    $selected = '' ;

    foreach ($category as $key => $val)
    {
        if($val['board_cate'] == 'b_001') $selected = 'selected' ;
        else $selected = '';
        $c_html .= "<option value='{$val['board_cate']}' {$selected} >{$val['board_name']}</option>";

        $sub_html .= "<li class='nav-item'><a href='boardPage.php?cate={$val['board_cate']}&page=1' class='nav-link'>{$val['board_name']}</a></li>" ;
    }
    
    for( $i=0; $i<count($category); $i++)
    {
        $b_name[$i] = $category[$i]['board_name'] ;
        $b_cate[$i] = $category[$i]['board_cate'] ;
    }

    foreach ($levelOption as $key => $val)
    {
        if($val['level'] == 'l_001') $selected = 'selected' ;
        else $selected = '';
        if( $val['l_name'] == '관리자') break;
        $l_html .= "<option value='{$val['level']}' {$selected} >{$val['l_name']}</option>";
    }
?>