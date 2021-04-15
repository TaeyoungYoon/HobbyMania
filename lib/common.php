<?php
    include_once ('mailsystem/class.phpmailer.php');
    /*-- password hash function --*/
    function hashPassword($password)
    {
        $key = sha1(mt_rand()); // 랜덤의 수를 해쉬 함수로 키 값 추출
        $key = substr($key, 0, 10); // 너무 길기 때문에 키 10자리로 제한 
        $encrypted = base64_encode(sha1($password . $key, true) . $key); //기존 비밀번호와 해쉬화된 키로 다시 해쉬 
        $hash = array("key" => $key, "encrypted" => $encrypted);
        return $hash;
    }
    /*-- password decode function --*/
    function checkPw($key, $password)
    {
        $hash = base64_encode(sha1($password . $key, true) . $key);
        return $hash;
    }

    /*-- message function --*/
    function messageAlert($msg)
    {
        echo '<script type="text/javascript">alert("' . $msg . '");history.go(-1);</script>';
    }

    /*-- random function --*/
    function randomString( $length ) 
    {
        $characters = '0123456789';
        $characters .= 'abcdefghijklmnopqrstuvwxyz';
        $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string_generated = '';
        $charactersnum = strlen($characters) - 1;

        $nmr_loops = $length;

        while ($nmr_loops--) {
            $string_generated .= $characters[mt_rand(0, $charactersnum )];
        }

        return $string_generated;
    }

    function mailSend( $to, $toname, $subject, $content)
    {
        $mail = new PHPMailer(true); 

        $mail->IsSMTP(); 


        $mail->Host       = "smtp.naver.com"; 
        $mail->SMTPDebug  = 1;                     
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = "ssl";
        $mail->Port       = 465;
        $mail->CharSet = 'utf-8';
        $mail->Encoding = "base64";
        $mail->Username   = "yoonty1017@naver.com";
        $mail->Password   = "*taeyoung2852";
        $mail->AddAddress( $to, $toname );
        $mail->SetFrom('yoonty1017@naver.com', '취미Mania');
        $mail->Subject = $subject;
        $mail->Body = $content;
        $mail->Send();

    }
?>