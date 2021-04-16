<?php
    /*-- 비밀 번호 찾기 backend --*/
    require_once ('../lib/common.php') ;
    require_once ('../db/db_info.php') ;

    if( isset($_POST['mem_id']) && $_POST['mem_id'] != ''
    && isset($_POST['email']) && $_POST['email'] != '')
    {
        $mem_id = preg_replace('#[^a-zA-Z0-9]#', '', $_POST['mem_id']);
        $email = $_POST['email'];

        $c = new db_member();

        if ( $c->checkUserID($mem_id) )
        {
            // 회원 아이디 있음 
            if( $c -> checkUserEmail($email) )
            {   // 회원 email 있음

                //send 메일
                $randomString = randomString(10) ;
                $to = $email;
                $toname = $mem_id;
                $subject = 'hobbyMania 비밀번호 변경건';
                $content = '변경 된 비밀번호는 : '. $randomString .' 입니다. 로그인 후 비밀번호 변경하세요';

                mailSend( $to, $toname, $subject, $content );

                $user = $c -> pwUpdate($mem_id, $randomString) ;
                
                if( $user )
                {
                    echo json_encode(array('result' => '1')); // 회원 정보 변경 성공
                }
                else
                {
                    echo json_encode(array('result' => '-1'));// 회원 정보 변경 실패
                }
            }
            else
            {
                echo json_encode(array('result' => '-2')); // 이메일 없음
            }
        }
        else
        {
            echo json_encode(array('result' => '-3')); // 회원 아이디 없음 
        }
    } 
    else
    {// 입력받은 데이터에 문제가 있을 경우
        echo json_encode(array('result' => '-4'));// 입력받은 데이터에 문제가 있을 경우
    }


?>