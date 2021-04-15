<?php
    /*-- 댓글 작성 수정 삭제 대댓글  backend --*/
    require_once ('../db/db_info.php') ;

    if ( isset($_GET['m']) && $_GET['m'] != '' )  $mode = $_GET['m'] ;
    if ( isset($_POST['p_idx']) && $_POST['p_idx'] != '' )  $p_idx = $_POST['p_idx'];
    $replyHtml = '' ;
    
    if( $mode == 'write' )
    {
        if ( isset($_POST['user_id']) && $_POST['user_id'] != '' 
        && isset($_POST['user_name']) && $_POST['user_name'] != ''  
        && isset($_POST['replycontent']) && $_POST['replycontent'] != '' 
        )
        {
            $mem_id = $_POST['user_id'];
            $name = $_POST['user_name'];
            $replycontent = htmlspecialchars($_POST['replycontent']);
    
            $c = new db_board();
            
            $reply = $c -> writeReply( $p_idx, $mem_id, $name, $replycontent );
    
            if( isset($reply) && $reply != '' )
            {   
                echo json_encode(array('result' => '1')); //댓글 입력 성공
            }
            else
            {
                echo json_encode(array('result' => '-1'));//댓글 입력 실패
            }
    
        }
        else
        {
            echo json_encode(array('result' => '-2')); // 데이터 안 들어왔을 경우
        }

    }
    else if( $mode == 'rewrite' )
    {
        if ( isset($_POST['user_id']) && $_POST['user_id'] != '' 
        && isset($_POST['user_name']) && $_POST['user_name'] != '' 
        && isset($_POST['parent_idx']) && $_POST['parent_idx'] != '' 
        && isset($_POST['replycontent']) && $_POST['replycontent'] != '' 
        )
        {
            $mem_id = $_POST['user_id'];
            $name = $_POST['user_name'];
            $parent_idx = $_POST['parent_idx'];
            $replycontent = htmlspecialchars($_POST['replycontent']);

            $c = new db_board();
            
            $reply = $c -> rewriteReply( $p_idx, $parent_idx, $mem_id, $name, $replycontent );
    
            if( isset($reply) && $reply != '' )
            {   
                echo json_encode(array('result' => '1')); //댓글 입력 성공
            }
            else
            {
                echo json_encode(array('result' => '-1'));//댓글 입력 실패
            }
    
        }
        else
        {
            echo json_encode(array('result' => '-2')); // 데이터 안 들어왔을 경우
        }
    }
    else if( $mode == 'view' )
    {
        if( isset($p_idx) && $p_idx != '' )
        {
            $c = new db_board();

            $selectReply = $c -> selectReply( $p_idx );
            if(  isset($selectReply) && $selectReply != '' )
            {
                echo json_encode( $selectReply, true) ;
            }
            else
            {
                echo json_encode(array('result' => '-1'));
            }
        }
        else
        {
            echo json_encode(array('result' => '-2'));
        }
    }
    else if( $mode == 'update' )
    {
        if( isset($_POST['re_idx']) && $_POST['re_idx'] != ''
        && isset($_POST['replycontent']) && $_POST['replycontent'] != ''
        )
        {
            $re_idx = $_POST['re_idx'];
            $replycontent = htmlspecialchars($_POST['replycontent']);

            $c = new db_board();

            $updateReply = $c -> updateReply($re_idx, $replycontent);
            if(  isset($updateReply) && $updateReply != '' )
            {
                echo json_encode(array('result' => '1'));//댓글 입력 성공
            }
            else
            {
                echo json_encode(array('result' => '-1')); //댓글 입력 실패
            }
        }
        else
        {
            echo json_encode(array('result' => '-2')); //데이터 없음
        }
    }
    else if( $mode == 'delete' )
    {
        if( isset($_POST['re_idx']) && $_POST['re_idx'] != '' )
        {
            $re_idx = $_POST['re_idx'];

            $c = new db_board();

            $delteReply = $c -> deleteReply($re_idx);
            if( $delteReply == 1 )
            {
                echo json_encode(array('result' => '1'));//댓글 삭제 성공
            }
            else
            {
                echo json_encode(array('result' => '-1')); //댓글 삭제 실패
            }
        }
        else
        {
            echo json_encode(array('result' => '-2')); //데이터 없음
        }
    }

?>