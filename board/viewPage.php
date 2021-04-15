<?php
    /*-- 게시글 포스트 --*/
    require_once ('../db/db_info.php') ;
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	require_once ($root.'/lib/define.php');
	require_once ($root.'/lib/common.php');
    require_once ("option.php") ;

    $p_idx = $_GET['p_idx'];
    $list_html = $deleteHtml = $updatePostHtml = '' ;
    $c = new db_board();
    $viewData = $c -> updateSelect($p_idx) ; 
    
    if( isset($viewData) && $viewData != '' )
    {
        foreach ($viewData as $val)
        {
            $title = $val['title'];
            $board_cate = $val['board_cate'];
            $mem_id = $val['mem_id'];
            $name = $val['name'];
            $content = $val['content'];
            $reg_date = explode(':', $val['reg_date']) ;
            $hit = $val['hit'] + 1;
            $like_p = $val['like_p'];
            $level = $val['level'];
            $f_dir = $val['f_dir'];
            $isdelete = $val['isdelete'];
        }
    }
    else
    {
        messageAlert('삭제 되었거나 없는 게시글 입니다.') ;
    }
    
    if( isset($_SESSION['mem_id']) && isset($_SESSION['name']) && isset($_SESSION['level']))
    {
        $user_id = $_SESSION['mem_id'] ;
        $user_name = $_SESSION['name'] ;
        $user_level = $_SESSION['level'] ;

        if( $user_level == 'l_001')
        {
            if( $level == 'l_002' || $level == 'l_003' )
            {
                messageAlert('등급이 낮아 열람 할 수 없는 글입니다.') ;
            }
        }
        else if ( $user_level == 'l_002' )
        {
            if( $level == 'l_003' )
            {
                messageAlert('등급이 낮아 열람 할 수 없는 글입니다.') ;
            }
        }

        if( $_SESSION['mem_id'] == $mem_id || $user_level == 'l_004' )
        {
            $updatePostHtml = "<button type='button' onclick='location.href=\"/board/writePage.php?p_idx={$p_idx}&m=update\"' class='btn btn-primary' style='margin: 10px 0  30px 0 ; position: relative; top: 50%; left: 40%;'>글 수정<i class='fa fa-check spaceLeft'></i></button>" ;
            $deleteHtml = "<button type='submit' id='delete-submit' class='btn btn-warning' style='margin: 10px 0  30px 0 ; position: relative; top: 50%; left: 40%;'>글 삭제<i class='fa fa-check spaceLeft'></i></button>" ;    
        }
    }
    else
    {
        $user_id = 'undefinedUser';
        $user_name = 'undefinedUserName';
    }

    if( isset($f_dir) && $f_dir != '' )
    {
        if( strpos($val['f_dir'],',') !== false )
        {
            $f_dir = explode(',', $val['f_dir']);
            for( $i = 0; $i < count($f_dir); $i++ )
            {
                $list_html .= "<img src='{$f_dir[$i]}' style='margin: 10px 30px 20px 50px;
                width:400px; height:hidden;'></br>" ;
            }
        }
        else
        {
            $f_dir = $val['f_dir'] ;
            $list_html .= "<img src='{$f_dir}' style='margin: 10px 30px 20px 50px; width:400px; height:hidden;'></br>" ;
        }
    }




    /*----- viewPage------*/
    require_once ("../inc/sub_header.php") ;
    echo <<<END
		<div class="container" id="board">
			<div id="col-md-8">
                <form method="post" id="update" >
					<table class="table table-striped" style=" border: 1px solid #ddddda">
						<thead>
							<tr>
								<th colspan="8" style="background-color: #eeeeee; text-align: center;"><h3>{$title}</h3></th>
							</tr>
						</thead>	
						<tbody style="border: 1px solid #ddddda" >
                            <tr>
                                <td class="col-md-1">글 번호 : &nbsp;{$p_idx}</td>
                                <td class="col-md-1">게시판 : &nbsp;{$_ENV[$board_cate]}</td> 
                                <td class="col-md-1">작성자 : &nbsp;{$name}</td> 
                                <td class="col-md-1">읽기 등급 : &nbsp;{$_ENV[$level]}</td>
                                <td class="col-md-2">작성시간 : &nbsp;{$reg_date[0]}:{$reg_date[1]}</td>
                                <td class="col-md-1">조회수 : &nbsp;{$hit}</td>
                                <td class="col-md-1">추천 : &nbsp;{$like_p}</td>
							</tr>
							<tr>	
								<td colspan="8"><div class="blog-post">{$list_html}<br><div style='margin: 10px 30px 20px 50px;'>{$content}</div></div></td>
							</tr>
						</tbody>
					</table>
                    <button type="submit" id="like-submit" class="btn btn-success" style="margin: 10px 0  30px 0 ; position: relative; top: 50%; left: 40%;">☆추천★<i class="fa fa-check spaceLeft"></i></button>
                    {$updatePostHtml}
					{$deleteHtml}   
                    <div class="commentList">
                    </div>
                    <div class="reply">
                        <div class="card-header">
                            <i class="fa fa-comment fa"></i> 댓 글
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                <div class="form-inline"> 
                                    <span>{$user_name}</span>
                                </div>
                                    <textarea class="form-control" id="inputContent" rows="3"  placeholder="댓글을 입력하세요."></textarea>
                                    <button class="btn btn-default" type="submit" id="commentInsert-submit">등록</button>
                                </li>
                            </ul>
                        </div>
                    </div>
				</form>
			</div>


            <div class="comment" id="re_comment">
                <div class="comment_box">
                    <div class="cmtbox">
                        <form id="cmtForm"><input type="hidden" name="m" id="m" value="reply_INSERT"><input type="hidden" name="b" id="b" value="bullpen"><input type="hidden" name="id" id="id" value=""><input type="hidden" name="info" id="info">
                            <div class="name"><span class="f_left">리플쓰기</span></div>
                            <div class="txt"><textarea rows=3 cols=50 id="content" name="content" class="msg_inp" ></textarea></div>
                            <div class="btn"><a href="#" onclick="javascript:closePopup(); return false;" style="margin-right:10px">취소</a><a href="#" onclick="javascript:insertreReply('reply_{$user_name}');return false;" id="re_btn2" style="margin-right:10px">확인</a></div>
                        </form>
                    </div>
                    <div class="comment_all">
                        <ul class="comment_list" style="overflow:hidden;">
                        </ul>
                    </div>
                </div>
            </div>

            <div class="commentup" id="up_comment">
                <div class="comment_box">
                    <div class="cmtbox">
                        <form id="cmtForm"><input type="hidden" name="m" id="m" value="reply_INSERT"><input type="hidden" name="b" id="b" value="bullpen"><input type="hidden" name="id" id="id" value=""><input type="hidden" name="info" id="info">
                            <div class="name"><span class="f_left">댓글 수정</span></div>
                            <div class="txt"><textarea rows=3 cols=50 id="content_up" name="content_up" class="msg_inp" ></textarea></div>
                            <div class="btn"><a href="#" onclick="javascript:closePopup(); return false;" style="margin-right:10px">취소</a><a href="#" onclick="javascript:updateReply();return false;" id="re_btn2" style="margin-right:10px">확인</a></div>
                        </form>
                    </div>
                    <div class="comment_all">
                        <ul class="popUpcomment" style="overflow:hidden;">
                        </ul>
                    </div>
                </div>
            </div>

        <script type="text/javascript">
            $(document).ready(function(){
                var user_id = '{$user_id}';
                var user_name = '{$user_name}';
                viewReply() ;
                $('#re_comment').hide();
                $('#up_comment').hide();
                if( user_id == 'undefinedUser' ) 
                {
                    $('.reply').hide();
                }
            });

            function popUpReply(idClass){
                var replyHtml = "" ;
                var replyName = "" ;
                var replyTime = "" ;
                var replyTxt = "" ;
                var replyFormTxt = "" ;

                if ( $('.' + idClass).find('.name').length == 0 ) return false;

                $('.' + idClass).each ( function() {
                    replyName = $(this).find('.name').html() ;
                    replyTime = $(this).find('.time').html() ;
                    replyTxt = $(this).find('.recomment').html() ;
        
                    replyHtml += "<li><p class='name' style='margin-right:10px;font-size:16px;'>" + replyName + "<span style='margin-left:10px'> " + replyTime + "</span></p>" + "<div class='reply_txt' style='margin-bottom: 15px; font-size:16px;'>" + replyTxt + "</div></li>" ;
                });


                $(".comment_list").html(replyHtml) ;
                $("#content").val ( replyName + '// ' ) ;
                $('#re_comment').bPopup();

                return false;
            }

            function popUpupdate(idClass, re_idx){

                var replyHtml = "" ;
                var replyName = "" ;
                var replyTime = "" ;
                var replyTxt = "" ;
                var replyFormTxt = "" ;

                if ( $('.' + idClass).find('.name').length == 0 ) return false;

                replyName = $('#'+re_idx).find('.name').html() ;
                replyTime = $('#'+re_idx).find('.time').html() ;
                replyTxt = $('#'+re_idx).find('.recomment').html() ;

                replyHtml += "<li id='"+re_idx+"'><p class='name' id='"+re_idx+"'style='margin-right:10px;font-size:16px;'>" + replyName + "<span style='margin-left:10px'> " + replyTime + "</span></p>" + "<div class='reply_txt' style='margin:0px; 0px; 10px; 0px; font-size:16px;'>" + replyTxt + "</div></li>" ;

                $(".popUpcomment").html(replyHtml) ;
                $("#content_up").val ( replyTxt ) ;
                $('#up_comment').bPopup();

                return false;
            }

            function insertreReply(idClass){
                var user_id = '{$user_id}';
                var user_name = '{$user_name}';

                if($("#content").val() =='')
                {
                    alert('댓글을 입력 하세요');
                    $("#content").focus();
                    return false;
                }
                
                if( user_id == 'undefinedUser' )
                {
                    alert('로그인 해야 작성가능 합니다.');
                    return false;
                }

                $.ajax({
                    url: 'reply.php?m=rewrite',
                    type: 'POST',
                    data: {
                        user_id:user_id,
                        user_name:user_name,
                        p_idx:{$p_idx},
                        parent_idx:idClass,
                        replycontent:$('#content').val(),
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.result == 1){
                            viewReply();
                        } 
                        else if(response.result == -1)
                        {
                            alert('댓글 입력 실패') ;
                        }
                        else if( response.result == -2)
                        {
                            alert('데이터 오류' ) ;
                        }
                    },
                    error:function(request,status,error){
                        alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                    }
                });

                $('#re_comment').bPopup().close();
            }

            function viewReply(){
                var user_id = '{$user_id}';
                var user_name = '{$user_name}';

                $.ajax({
                    url: 'reply.php?m=view',
                    type: 'POST',
                    data: {
                        p_idx:{$p_idx}
                    },
                    dataType: "json",
                    success: function (response) {
                        var res_cnt = response.length;
                        if( res_cnt > 0 )
                        {
                            var res = '';
                            var strDate = new Array();
                            for( var i = 0; i < res_cnt; i++ )
                            {
                                strDate[i] = response[i].reg_date.split(':');

                                res += "<div class='commentArea' style='border-bottom:1px solid darkgray; margin-bottom: 15px;'>"; 
                                res += "<div class='reply_"+response[i].name+"' id='"+response[i].re_idx+"'>"; 
                                if( user_name  != 'undefinedUserName' && user_name == response[i].name)
                                { 
                                    res += "<span class='name' style='color:red' id='commentName'>"+ response[i].name+"</span>"; 
                                }
                                else
                                {
                                    res += "<span class='name' id='commentName'>"+ response[i].name+"</span>";
                                } 
                                res += "<span class='time' id='commentTime'>"+"&nbsp;&nbsp;&nbsp;"+strDate[i][0]+":"+strDate[i][1]+"</span>"; 
                                if( user_name  != 'undefinedUserName' && user_name == response[i].name || user_name == '관리자')
                                { 
                                    res += "<button onclick='javascript:deleteReply(\""+response[i].re_idx+"\"); return false;' id='commentDelete' class='btn btn-warning pull-right'> 삭제 </button>"; 
                                    res += "<button onclick='javascript:return popUpupdate(\"reply_"+response[i].name+"\",\""+response[i].re_idx+"\");' id='commentUpdate' class='btn btn-primary pull-right'> 수정 </button>";    
                                }
                                res += "<button onclick='javascript:return popUpReply(\"reply_"+response[i].name+"\");' id='commentreply' class='btn btn-success pull-right'> 답글 </button>"; 
                                res += "<div class='recomment' id='commenttext'>"+response[i].re_content+"</div>"; 
                                res += "</div>"; 
                                res += "</div>"; 
                            }

                            $(".commentList").html(res);
                        }
                        else
                        {
                            $(".commentList").html('');
                        }
                    },
                    error:function(request,status,error){
                        alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                    }
                }); 
            }

            function updateReply(){
                var re_idx = $('.popUpcomment li').attr('id') ;
                console.log(re_idx);
                console.log($('#content_up').val());
                $.ajax({
                    url: 'reply.php?m=update',
                    type: 'POST',
                    data: {
                        re_idx:re_idx,
                        replycontent:$('#content_up').val(),
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.result == 1){
                            viewReply();
                        } 
                        else if(response.result == -1)
                        {
                            alert('댓글 입력 실패') ;
                        }
                        else if( response.result == -2)
                        {
                            alert('데이터 오류' ) ;
                        }
                    },
                    error:function(request,status,error){
                        alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                    }
                });

                $('#up_comment').bPopup().close();
            }

            function deleteReply(re_idx){
                if(confirm("댓글을 삭제 하겠습니까?") == true)
                {   
                    $.ajax({
                        url: 'reply.php?m=delete',
                        type: 'POST',
                        data: {
                            re_idx:re_idx,
                        },
                        dataType: "json",
                        success: function (response) {
                            if(response.result == 1){
                                viewReply();
                            } 
                            else if(response.result == -1)
                            {
                                alert('댓글 삭제 실패') ;
                            }
                            else if( response.result == -2)
                            {
                                alert('데이터 오류' ) ;
                            }
                        },
                        error:function(request,status,error){
                            alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                        }
                    });
                }
                else
                {
                    return false ;
                }
            }
            
            function closePopup() {
                $('#re_comment').bPopup().close();
                $('#up_comment').bPopup().close();
            }

            $('#commentInsert-submit').click(function(e){
                e.preventDefault();

                var user_id = '{$user_id}';
                var user_name = '{$user_name}';

                if($("#inputContent").val() =='')
                {
                    alert('댓글을 입력 하세요');
                    $("#inputContent").focus();
                    return false;
                }
                
                if( user_id == 'undefinedUser' )
                {
                    alert('로그인 해야 작성가능 합니다.');
                    return false;
                }

                $.ajax({
                    url: 'reply.php?m=write',
                    type: 'POST',
                    data: {
                        user_id:user_id,
                        user_name:user_name,
                        p_idx:{$p_idx},
                        replycontent:$('#inputContent').val(),
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.result == 1){
                            viewReply();
                            $("#inputContent").val('');
                        } 
                        else if(response.result == -1)
                        {
                            alert('댓글 입력 실패') ;
                        }
                        else if( response.result == -2)
                        {
                            alert('데이터 오류' ) ;
                        }
                    },
                    error:function(request,status,error){
                        alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                    }
                }); 
            });

            $('#like-submit').click(function(e){
                console.log(1);
                e.preventDefault();

                var user_id = '{$user_id}';
                var user_name = '{$user_name}';
                
                if( user_id == 'undefinedUser' )
                {
                    alert('로그인 해야 추천 가능합니다.');
                    return false;
                }

                $.ajax({
                    url: 'like.php',
                    type: 'POST',
                    data: {
                        user_id:user_id,
                        p_idx:{$p_idx},
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.result == 1){
                            alert('추천 완료!');
                            location.reload();
                        } 
                        else if(response.result == -1)
                        {
                            alert('추천 실패 ') ;
                        }
                        else if( response.result == -2)
                        {
                            alert('이미 추천한 게시글 입니다.' ) ;
                        }else if( response.result == -3)
                        {
                            alert('데이터 오류' ) ;
                        }
                    },
                    error:function(request,status,error){
                        alert("code = "+ request.status + " message = " + request.responseText + " error = " + error);
                    }
                }); 
            });

            $('#delete-submit').click(function(e){
                e.preventDefault();
                if(confirm("게시글을 삭제 하겠습니까?") == true)
                {   
                    $.ajax({
                        url: 'viewDelete.php',
                        type: 'POST',
                        data: {
                            p_idx:'{$p_idx}'
                        },
                        dataType: "json",
                        success: function (response) {
                            if(response.result == 1){
                                alert('게시글 삭제 완료.');
                                location.replace('/board/boardPage.php?{$board_cate}&page=1');
                            } else if(response.result == -1){
                                alert('삭제 실패.');
                            }
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
        </script>
        
END;
        require_once ("../inc/footer.php")
?>