<?php
    /*-- 회원 재확인 페이지 View --*/
    // 세션에 한번 인증 한 회원에게 checkok, 세션 update 값 부여
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require_once ($root.'/lib/define.php');

    if( isset($_SESSION['mem_id']) && isset($_SESSION['name']) && isset($_SESSION['email']) && isset($_SESSION['update']) && $_SESSION['update'] == 'update' )
    {
        echo "<script>location.replace('updatePage.php')</script>";
    }
    else
    {
        echo <<<END
        <!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">
        
            <title>{$_ENV['TITLE']}</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
            <link href="../css/signin.css" rel="stylesheet">
          </head>
        
          <body>
        
            <div class="container">
        
              <form class="form-signin">
                <h2 class="form-signin-heading">회원 정보 수정</h2>
                <h4 class="form-signin-heading">ID/비밀번호를 다시 확인 하겠습니다.</h4>
                <label for="inputID" class="sr-only">ID</label>
                <input type="id" id="inputID" class="form-control" placeholder="ID" required autofocus>
                <br>
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                <br>
                <button class="btn btn-lg btn-primary btn-block" id="update-submit" type="submit">수정</button>
              </form>
            
            </div>
            <script>
                $(function(){
                    $('#update-submit').click(function(e){
                        e.preventDefault();
                        var checkok = true;

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

                        if($("#inputPassword").val() =='')
                        {
                            alert('비밀번호를 입력하세요');
                            $("#inputPassword").focus();
                            return false;
                        }
                        $.ajax({
                            url: 'join.php',
                            type: 'POST',
                            data: {
                                mem_id:$('#inputID').val(),
                                password:$('#inputPassword').val(),
                                checkok:checkok,
                            },
                            dataType: "json",
                            success: function (response) {
                                if(response.result == 1){
                                    location.replace('updatePage.php');
                                } else if(response.result == -1){
                                    alert('아이디,비밀번호를 다시 확인 해주세요');
                                } else if(response.result == -2){
                                    alert('입력된 값이 없습니다');
                                } else {
                                    alert('등록중에 에러가 발생했습니다');
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
    }
?>