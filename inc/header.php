<?php
    /*-- 헤더 View --*/
    //로그인 되었을때 아닐때 구분하기
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require_once ($root.'/lib/define.php');

    if( strpos($urlLead , '/join/signPage.php') !== false || strpos($urlLead , '/join/updatePage.php') !== false  )
    {
        $LoginPart = '';
        $subcss = '<link rel="stylesheet" href="../css/jumbotron.css">';
        
    }
    else
    {
        $LoginPart =<<<END
                <div id="navbar" class="navbar-collapse collapse">
                    <form class="navbar-form navbar-right">
                        <div class="form-group">
                            <input type="text" placeholder="ID" id="inputID" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="password" placeholder="Password" id="inputPassword" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success" id="login-submit" >로그인</button>
                        <button type="button" onclick="location.href='join/findpwPage.php'" class="btn btn-warning" >PW 찾기</button>
                        <button type="button" onclick="location.href='join/signPage.php'" class="btn btn-info">회원가입</button>
                    </form>
                </div>
END;
    $subcss = '<link rel="stylesheet" href="css/jumbotron.css">';
    }

    if( isset($_SESSION['mem_id']) || isset($_SESSION['name']) )
    {
        
        if( strpos($urlLead , '/join/updatePage.php') !== false )
        {
            $updateHtml = "" ;
        }
        else
        {
            $updateHtml =<<<END
             "<button type="button" onclick="location.href='join/upsignPage.php'" class="btn btn-primary">정보 변경</button>"
END;
        }
        $loginOk = true;
        $LoginPart =<<<END
        <div id="navbar" class="navbar-collapse collapse">
            <form class="navbar-form navbar-right">
                <ul class="nav navbar-nav" style ="margin:-18px 10px 0px 0px;">
                    <li>
                        <h3>
                            <span class="label label-default" >{$_ENV[$_SESSION['level']]} {$_SESSION['name']} 님 </span>
                        </h3>
                    </li>
                </ul>
                {$updateHtml}
                <button type="submit" id="logout-submit" class="btn btn-warning">로그 아웃</button>
            </form>
        </div>
END;
    }
    else
    {
        $loginOk = false;
    }

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
    
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        {$subcss}
    </head>
    <script>
    $(function(){
        $('#login-submit').click(function(e){
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
            if($("#inputPassword").val() =='')
            {
                alert('비밀번호를 입력하세요');
                $("#inputPassword").focus();
                return false;
            }
            $.ajax({
                url: '../join/join.php',
                type: 'POST',
                data: {
                    mem_id:$('#inputID').val(),
                    password:$('#inputPassword').val(),
                },
                dataType: "json",
                success: function (response) {
                    if(response.result == 1){
                        alert('환영합니다.');
                        location.reload(true);
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
        $('#logout-submit').click(function(e){
            if(confirm("로그아웃 하시겠습니까?") == true)
            {
                $.ajax({
                    url: '../join/logout.php',
                    type: 'POST',
                    data: {},
                    dataType: "html",
                    success: function (response) {
                    },
                    error:function(request,status,error){
                        alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                    }
                });
            }
            else
            {
                return false;
            }
        });
    });
    </script>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/" target="_self">취미 MANIA</a>
                </div>
                {$LoginPart}
            </div>
        </nav>
END;
?>