<?php
    /*-- 회원 비밀 번호 찾기 페이지 View --*/

    echo <<<END
    <!DOCTYPE html>
    <html lang="en">
        <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>hobbymania 비밀번호 찾기</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <link href="../css/signin.css" rel="stylesheet">
        </head>
    
        <body>
    
        <div class="container">
    
            <form class="form-signin">
            <h2 class="form-signin-heading">비밀번호 찾기</h2>
            <h4 class="form-signin-heading">ID/Email 를 입력해주세요.</h4>
            <h4 class="form-signin-heading">메일로 변경 된 pw가 발송 됩니다.</h4>
            <label for="inputID" class="sr-only">ID</label>
            <input type="id" id="inputID" class="form-control" placeholder="ID" required autofocus>
            <br>
            <label for="inputEmail">이메일 주소</label>
            <input type="email" class="form-control" id="inputEmail" placeholder="이메일 주소를 입력해주세요">
            <br>
            <button class="btn btn-lg btn-primary btn-block" id="pwfind-submit" type="submit">발송</button>
            </form>
        
        </div>
        <script>
            $(function(){
                $('#pwfind-submit').click(function(e){
                    e.preventDefault();

                    if($("#inputID").val() =='')
                    {
                        alert('ID를 입력하세요');
                        $("#inputID").focus();
                        return false;
                    }
                    else
                    {
                        var idRegex =/[^a-zA-Z0-9]/g;
                        if (idRegex.test($("#inputID").val()))
                        {
                            alert('ID에 특수문자 혹은 기호 혹은 띄어쓰기가 있습니다.');
                            $("#inputID").focus();
                            return false;
                        }
                    }

                    var email = $('#inputEmail').val();
                    if(email == '')
                    {
                        alert('이메일을 입력하세요');
                        $("#inputEmail").focus();
                        return false;
                    }
                    else
                    {
                        var emailRegex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                        if (!emailRegex.test(email))
                        {
                            alert('이메일 주소가 유효하지 않습니다. ex)abc@gmail.com');
                            $("#inputEmail").focus();
                            return false;
                        }
                    }

                    $.ajax({
                        url: 'checkEmail.php',
                        type: 'POST',
                        data: {
                            mem_id:$('#inputID').val(),
                            email:$('#inputEmail').val(),
                        },
                        dataType: "json",
                        success: function (response) {
                            if(response.result == 1){
                                alert('메일 발송 하였습니다. 확인하세요');
                                location.replace('../index.php');
                            } else if(response.result == -1){
                                alert('비밀 번호 변경 실패');
                            } else if(response.result == -2){
                                alert('이메일이 없습니다.');
                            } else if(response.result == -3){
                                alert('회원 아이디가 없습니다.');
                            } else if(response.result == -4){
                                alert('입력된 값이 없습니다');
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