<?php
    /*-- 회원 로그인 backend --*/
    require_once ('../db/db_info.php') ;
    require_once ('../lib/auth.php') ;

    if ( isset($_GET['m']) && $_GET['m'] != '' )    $m = $_GET['m'] ;
    
    if ( $m == 'join' )
    {
        $mem_id = preg_replace('#[^a-zA-Z0-9]#', '', $_POST['mem_id']);
        $password = $_POST['password'];
    
        if( !isset($_POST['checkok']) )
        {
            $_POST['checkok'] = false;
        }
    
        if ( isset($mem_id) && $mem_id != '' && isset($password) && $password != '')
        {
    
            $c = new db_member() ;
    
            $user = $c -> selectUser( $mem_id, $password ) ;
    
            if( $user )
            {
                if (!isset($_SESSION)) // 로그인시 세션 설정
                {
                    session_start();
                    $InitalizationKey = Google2FA::generate_secret_key(16);					// Set the inital key
    
                    $TimeStamp	  = Google2FA::get_timestamp();
                    $secretkey 	  = Google2FA::base32_decode($InitalizationKey);	// Decode it into binary
                    $otp       	  = Google2FA::oath_hotp($secretkey, $TimeStamp);	// Get current token
    
                }
                $_SESSION['initial'] = $InitalizationKey ;

                if( $_POST['checkok'] == true) // 회원 정보 변경 인증 회원에게 세션 부여
                {
                    $_SESSION['update'] = 'update' ;
                }
    
                echo json_encode(array('result' => '1')); // 로그인 설정
            }
            else
            {
                echo json_encode(array('result' => '-1'));// 로그인 실패 
            }
        }
        else
        {
            echo json_encode(array('result' => '-2')); // 데이터가 안들어왔을 경우 오류
        }
    }
    else if( $m == 'otpjoin')
    {
        if($_POST['otp'] && $_POST['otp'] != '' )
        {
            $otpPass = $_POST['otp'] ;
            $initialkey = $_POST['initialkey'] ;
            $mem_id = $_POST['mem_id'] ;
            $password = $_POST['password'] ;

            $c = new db_member() ;

            $result = Google2FA::verify_key($initialkey, $otpPass);

            if( $result == true )
            {
                echo json_encode(array('result' => '1')); // 로그인 설정

                $user = $c -> selectUser( $mem_id, $password ) ;

                $_SESSION['mem_id'] = $user['mem_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['level'] = $user['level'];
                $_SESSION['email'] = $user['email'];
            }
            else
            {
                echo json_encode(array('result' => '-1'));// 로그인 실패 (OTP 값 불 일치)
            }
        }
        else
        {
            echo json_encode(array('result' => '-2')); // 데이터가 안들어왔을 경우 오류(OTP 안들어옴)
        }
    }

?>