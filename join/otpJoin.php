<?php
    require_once "../lib/define.php" ;
    require_once "../lib/auth.php" ;

    if ( isset($_POST['mem_id']) && $_POST['mem_id'] != '' )    $mem_id = $_POST['mem_id'] ; 
    if ( isset($_POST['password']) && $_POST['password'] != '' )    $password = $_POST['password'] ; 

echo <<<END
        <!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">
        
            <title>OTP 인증</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
            <link href="../css/signin.css" rel="stylesheet">
          </head>
        
          <body>
        
            <div class="container">
        
                <div class="otp">
                    <img src="https://image-charts.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/hobbymania?secret={$_SESSION['initial']}">
                </div>

                <div class="otpok">
                    <input type="password" placeholder="Password" id="otp" class="form-control">
                    <button type="submit" class="btn btn-success" id="otp-submit" >otp 확인</button>
                </div>
            </div>
            <script>
                $(function(){
                    $('#otp-submit').click(function(e){
                        $.ajax({
                            url: 'join.php?m=otpjoin',
                            type: 'POST',
                            data: {
                                otp:$('#otp').val(),
                                initial:'{$_SESSION['initial']}',
                                mem_id:'{$mem_id}'
                            },
                            dataType: "json",
                            success: function (response) {
                                if(response.result == 1){
                                    location.href= '../index.php';
                                } else if(response.result == -1){
                                    alert('OTP 인증실패 ');
                                } else if(response.result == -2){
                                    alert('입력된 값이 없습니다');
                                } else {
                                    alert('OTP 인증실패');
                                }
                            },
                            error:function(request,status,error){
                                alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                            }
                        });   
                    });
                });
                </script>
        </body>
        </html>
        
END;
?>