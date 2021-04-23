<?php
    /*-- 회원 가입 backend --*/
    require_once ('../db/db_info.php') ;
    require_once ('../lib/auth.php') ;
    $mem_id = preg_replace('#[^a-zA-Z0-9]#', '', $_POST['mem_id']);
    $name = preg_replace('#[^가-힣ㄱ-ㅎㅏ-ㅣa-zA-Z0-9]#', '', $_POST['name']);
    $password =  $_POST['password'];
    $email = $_POST['email'] ;

    if ( isset($mem_id) && $mem_id != '' )
    {
        $c = new db_member();

        if ( $c->checkUserID($mem_id) )
        {
            echo json_encode(array('result' => '0')); // 아이디 중복 시 회원가입 실패

            if( $c->checkUserID($name) )
            {
                echo json_encode(array('result' => '-3'));// 별명 중복 시 회원가입 실패
            }
        } 
        else 
        {
            //$otpkey = $mem_id.Google2FA::generate_secret_key(16);//오류
    
            $otpkey = Google2FA::generate_secret_key(16);
            // 회원 등록
            $user = $c->insertUser($mem_id, $name, $password, $email ,$otpkey );
            if ($user) 
            {
                if (!isset($_SESSION)) 
                {
                    session_start();
                }
                $_SESSION['mem_id'] = $user['mem_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['level'] = $user['level'];
                $_SESSION['email'] = $user['email'];

                echo json_encode(array('result' => '1'));
            } 
            else
            {
                // 회원 등록 실패
                echo json_encode(array('result' => '-1'));
            }
        }
    } 
    else
    {// 입력받은 데이터에 문제가 있을 경우
        echo json_encode(array('result' => '-2'));
    }
?>