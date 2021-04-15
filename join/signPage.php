<?php
    /*-- 회원 가입 View --*/
    require_once ("../inc/header.php") ;
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require_once ($root.'/lib/define.php');
    require_once ($root.'/lib/common.php');

    if( $loginOk )
    {
        messageAlert(' 로그인 되어있습니다. '); 
    }
    echo <<<END
        <article class="container">
            <div class="page-header">
                <div class="col-md-6 col-md-offset-3">
                <h3>취미 MANIA 회원 가입</h3>
                </div>
            </div>
            <div class="col-sm-6 col-md-offset-3">
                <form role="form">
                    <div class="form-group">
                        <label for="inputID">ID (특수문자,한글,기호,띄어쓰기 불가)</label>
                        <input type="text" class="form-control" id="inputID" placeholder="아이디을 입력해 주세요">
                        <button type="submit" id="checkId-submit" class="btn btn-info" style="margin-top:10px">
                            중복확인<i class="fa fa-check spaceLeft"></i>
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="inputName">별명 (특수문자,기호,띄어쓰기 불가) </label>
                        <input type="text" class="form-control" id="inputName" placeholder="별명을 입력해 주세요">
                        <button type="submit" id="checkName-submit" class="btn btn-info" style="margin-top:10px">
                            중복확인<i class="fa fa-check spaceLeft"></i>
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail">이메일 주소</label>
                        <input type="email" class="form-control" id="inputEmail" placeholder="이메일 주소를 입력해주세요">
                    </div>
                    <div class="form-group">
                        <label for="inputPassword">비밀번호</label>
                        <input type="password" class="form-control" id="inputPassword" placeholder="비밀번호를 입력해주세요">
                    </div>
                    <div class="form-group">
                        <label for="inputPasswordCheck">비밀번호 확인</label>
                        <input type="password" class="form-control" id="inputPasswordCheck" placeholder="비밀번호 확인을 위해 다시한번 입력 해 주세요">
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" id="join-submit" class="btn btn-primary">
                            회원가입<i class="fa fa-check spaceLeft"></i>
                        </button>
                    </div>
                </form>
            </div>
        </article>
        <script>
        $(function(){
            // 회원 가입 처리
            $('#join-submit').click(function(e){
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
                
                if($("#inputName").val() =='')
                {
                    alert('별명을 입력하세요');
                    $("#inputName").focus();
                    return false;
                }
                else
                {
                    var NameRegex =/[^가-힣ㄱ-ㅎㅏ-ㅣa-zA-Z0-9]/gi;
                    if (NameRegex.test($("#inputName").val()))
                    {
                        alert('별명에 특수문자 혹은 기호 혹은 띄어쓰기가 있습니다.');
                        $("#inputName").focus();
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
                var pass = $("#inputPassword").val() ;
                var passlength = pass.length;
                if( pass =='')
                {
                    alert('비밀번호를 입력하세요');
                    $("#inputPassword").focus();
                    return false;
                }else if( passlength < 8 )
                {
                    alert('비밀번호 8자리 이상 눌러주세요');
                    $("#inputPassword").focus();
                    return false;
                }

                if($("#inputPasswordCheck").val() =='')
                {
                    alert('비밀번호를 다시 한번 더 입력하세요');
                    $("#inputPasswordCheck").focus();
                    return false;
                }
                
                if($("#inputPassword").val()!== $("#inputPasswordCheck").val())
                {
                    alert('비밀번호를 둘다 동일하게 입력하세요');
                    return false;
                }

                $.ajax({
                    url: 'register.php',
                    type: 'POST',
                    data: {
                        mem_id:$('#inputID').val(),
                        name:$('#inputName').val(),
                        email:$('#inputEmail').val(),
                        password:$('#inputPassword').val(),
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.result == 1){
                            alert('가입 완료');
                            location.replace('../index.php');
                        } else if(response.result == 0){
                            alert('아이디 중복 확인 하세요.');
                        } else if(response.result == -3){
                            alert('별명 중복 확인 하세요.');
                        } else if(response.result == -2){
                            alert('아이디가 없습니다');
                        } else {
                            alert('등록중에 에러가 발생했습니다');
                        }
                    },
                    error:function(request,status,error){
                        alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                    }
                });   
            });

            $('#checkId-submit').click(function(e){
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

                $.ajax({
                    url: 'check.php?m=n',
                    type: 'POST',
                    data: {
                        mem_id:$('#inputID').val(),
                        name:'',
                        current_name:'',
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.result == 1){
                            if(confirm("사용 가능한 아이디입니다. 사용하시겠습니까?") == true)
                            {}
                            else
                            {
                                $('#inputID').val('');
                            }
                        } else if(response.result == 0){
                            alert('사용 할 수 없는 아이디 입니다.');
                        } else if(response.result == -2){
                            alert('입력된 아이디가 없습니다');
                        } else {
                            alert('등록중에 에러가 발생했습니다');
                        }
                    },
                    error:function(request,status,error){
                        alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                    }
                });   
            });

            $('#checkName-submit').click(function(e){
                e.preventDefault();
                if($("#inputName").val() =='')
                {
                    alert('별명을 입력하세요');
                    $("#inputName").focus();
                    return false;
                }
                else
                {
                    var NameRegex =/[^가-힣ㄱ-ㅎㅏ-ㅣa-zA-Z0-9]/gi;
                    if (NameRegex.test($("#inputName").val()))
                    {
                        alert('별명에 특수문자 혹은 기호 혹은 띄어쓰기가 있습니다.');
                        $("#inputName").focus();
                        return false;
                    }
                }
                $.ajax({
                    url: 'check.php?m=n',
                    type: 'POST',
                    data: {
                        mem_id:'',
                        name:$('#inputName').val(),
                        current_name:'',
                        
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.result == 1){
                            if(confirm("사용 가능한 별명입니다. 사용하시겠습니까?") == true)
                            {}
                            else
                            {
                                $('#inputName').val('');
                            }
                        } else if(response.result == 0){
                            alert('사용 할 수 없는 별명 입니다.');
                        } else if(response.result == -2){
                            alert('입력된 별명이 없습니다');
                        } else {
                            alert('에러가 발생했습니다');
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

