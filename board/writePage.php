<?php
    /*-- 게시글 작성 포스트 --*/
	require_once ("../inc/sub_header.php") ;
	require_once ("option.php") ;
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require_once ('../db/db_info.php') ;
	require_once ($root.'/lib/define.php');
	require_once ($root.'/lib/common.php');


    if( isset($_GET['p_idx']) && $_GET['p_idx'] != '' ) $p_idx = $_GET['p_idx'];
    if( isset($_GET['m']) && $_GET['m'] != '' ) $mode = $_GET['m'];
    $Html = '' ;

    $c = new db_board();
    
    if( $mode == 'write' )
    {
        if( !isset($_SESSION['mem_id']) )
        {
            messageAlert('로그인 이후 글쓰기 가능') ;
            exit;
        }
        if( isset($_GET['cate']) && $_GET['cate'] != '' )
        {
            $board_cate = $_GET['cate'];
        }
        else
        {
            $board_cate = 'b_001' ;
        }

        $Html .=<<<END
		<div class="container" id="board">
			<div id="board_write">
                <form method="post" id="writeform" >
					<table class="table table-striped" style=" border: 1px solid #ddddda">
						<thead>
							<tr>
								<th colspan="2" style="background-color: #eeeeee; text-align: center;"><h3>게시판 글쓰기</h3></th>
							</tr>
						</thead>	
						<tbody style="border: 1px solid #ddddda " >
                            <tr>
                                <td style="border: 1px solid #ddddda; display:flex;" >
                                    <span class="pull-left">&nbsp;&nbsp;&nbsp;게시판 : &nbsp;&nbsp;<b></b></span> 
                                    <select class="form-control" id="category" style="width:100px;">
                                        {$c_html}
                                    </select>
                                    <span class="pull-left">&nbsp;&nbsp;&nbsp;읽기 등급 : &nbsp;&nbsp;<b></b></span>
                                    <select class="form-control" id="level" style="width:100px; ">
                                        {$l_html}
                                    </select>
                                </td>
							</tr>
							<tr>
								<td style="border: 1px solid #ddddda "><input type="text" class="form-control" placeholder="글 제목" name="title" id="inputTitle" required></td>
							</tr>
                            <tr>
                                <td><p>&nbsp;&nbsp;&nbsp;이미지 6개 까지 첨부 가능</p></td>
							</tr>
                            <tr>
                                <td style="border: 1px solid #ddddda; display:flex"">
                                    <div class="imgs_wrap" style="display:flex">
                                        <img id="img" />
                                    </div>
                                    <div class="filebox" >
                                        <label for="input_file" onclick="fileUploadAction()" class="my_button">이미지 추가</label>
                                        <input type="file" id="input_imgs" multiple/>
                                    </div>
                                </td>
							</tr>
							<tr>	
								<td style="border: 1px solid #ddddda "><textarea class="form-control" placeholder="글 내용" name="content" id="inputContent" style="height: 350px" required></textarea></td>
							</tr>
						</tbody>
					</table>
					<button type="submit" id="write-submit" class="btn btn-primary">글쓰기<i class="fa fa-check spaceLeft"></i></button>
				</form>
			</div>
        </div>
        <script>
        var fileIndex = 0;
        var totalFileSize = 0.0;
        var fileList = new Array();
        var fileSizeList = new Array();
        const maxFileCnt = 6;
        const uploadSize = 10;

        function multiFileSettingInit(){
            window.fileIndex = 0;
            window.totalFileSize = 0.0;
            window.fileList = new Array();
            window.fileSizeList = new Array();
        }

        $(document).ready(function() {
            $('#category').val('$board_cate').prop("selected",true);
            var user_level = '{$_SESSION['level']}' ;
            if( user_level == 'l_001')
            {
                $("#level option[value='l_002']").remove();
                $("#level option[value='l_003']").remove();
            }
            else if ( user_level == 'l_002' )
            {
                $("#level option[value='l_003']").remove();
            }
        });

        function fileUploadAction() {
            console.log("fileUploadAction");
            $("#input_imgs").trigger('click');
        }

        $("#input_imgs").on("change", function(){
            selectFile(this.files);
        });

        function selectFile( files ){

            if(files != null){
    
                var img_cnt = $(".imgs_wrap a").length ;
                var file_cnt = files.length ;
                var errStr = "" ;
    
                if ( img_cnt + file_cnt <= maxFileCnt )
                {
                    for(var i = 0; i < file_cnt; i++)
                    {
                        var fileName = files[i].name;
                        var fileNameArr = fileName.split("\.");
                        var ext = fileNameArr[fileNameArr.length - 1];
                        ext = ext.toLowerCase();
                        var fileSize = files[i].size / 1024 / 1024;
    
                        if($.inArray(ext, ['jpg', 'gif', 'png', 'jpeg']) < 0 )
                        {
                            errStr += "[" + (i+1) + "번째 파일 미허용 확장자]" ;
                            continue ;
                        }
                        else if(fileSize > uploadSize)
                        {
                            errStr += "[" + (i+1) + "번째 파일 용량 초과(" + fileSize + "MB)]" ;
                            continue ;
                        }
                        else
                        {
                            totalFileSize += fileSize;
                            fileList[fileIndex] = files[i];
                            fileSizeList[fileIndex] = fileSize;
                            addFileList( files[i], fileIndex, file_cnt );
                            fileIndex++;
                        }
                    }
    
                    if ( errStr != "" )
                    {
                        alert(errStr);
                    }
                }
                else
                {
                    alert("한번에 최대 6개까지 등록 가능합니다.");
                }
            }
            else
            {
                alert("파일 선택 에러");
            }
        }
    
        var liHtml = "";
        function addFileList( file, fileIndex, file_cnt)
        {
            var reader = new FileReader();
            reader.readAsDataURL(file);

            reader.onloadstart = function(e)
            {
                var liHtml = "<a href=\"javascript:void(0);\" onclick=\"javascript:fileDelete(this).done( fileMoveSort('del') ); \" data-idx ='"+ fileIndex +"'><img src='' class='selProductFile' title='Click to remove' style='display: block; width: 100px; height: auto;' ></a>";
                $(".imgs_wrap").append(liHtml) ;
            }
    
            reader.onload = function(e)
            {
                var fileName = e.target.result;
                console.log(e.target.file);
                $(".imgs_wrap").children('a').each( function(){
    
                    if ( $(this).attr('data-idx') == fileIndex )
                    {
                        $(this).find('img').attr('src', fileName ) ;
                    }
                });
            }
        }

        function fileDelete(obj){

            var deferred = $.Deferred();
    
            try {
                if( obj != null )
                {
                    fIndex =  $(obj).closest('a').attr('data-idx') ;
                    console.log(fIndex);
                    totalFileSize -= fileSizeList[fIndex];
    
                    fileList.splice( fIndex , 1) ;
                    fileSizeList.splice( fIndex , 1) ;
                    $(obj).closest('a').remove() ;
                    fileIndex -- ;
    
                    totalSizeChaged();
    
                    deferred.resolve('성공');
                }
    
            } catch (err) {
    
                deferred.reject(err);
    
            }
    
            return deferred.promise();
        }
        
        function fileMoveSort( mode ){

            var list = $( ".imgs_wrap > a" ) ;
            var tmpFileArr = fileList.slice();
            var tmpFileSzieArr = fileSizeList.slice();
    
            if ( mode == 'del')
            {
                list.each(function(index, item){
                    $(this).attr('data-idx', index ) ;
                    fileList[index] = tmpFileArr[index];
                    fileSizeList[index] = tmpFileSzieArr[index];
                })
            }
            else
            {
                list.each(function(index, item){
                    var fIndex = $(this).attr('data-idx') ;
                    fileList[index] = tmpFileArr[fIndex];
                    fileSizeList[index] = tmpFileSzieArr[fIndex];
                    $(this).attr('data-idx', index ) ;
                })
            }
        }
        $(function(){
            $('#write-submit').click(function(e){
                e.preventDefault();

                if($("#inputTitle").val() =='')
                {
                    alert('제목 입력 하세요');
                    $("#inputTitle").focus();
                    return false;
                }
                if($("#inputContent").val() =='')
                {
                    alert('내용 입력 하세요');
                    $("#inputContent").focus();
                    return false;
                }
                console.log("업로드 파일 갯수 : "+fileList.length);
                var datas = new FormData();
                var select_cate = $("select[id=category]").val() ;
                var select_level = $("select[id=level]").val() ;

                datas.append("mem_id", '{$_SESSION['mem_id']}' );
                datas.append("name", '{$_SESSION['name']}' );
                datas.append("title", $('#inputTitle').val() );
                datas.append("content", $('#inputContent').val() );
                datas.append("category", select_cate  );
                datas.append("level", select_level );

                if(fileList.length > 0) {
                    for(var i=0, len=fileList.length; i<len; i++) {
                        var name = "image_"+i;
                        datas.append(name, fileList[i]);
                    }
                    datas.append("image_count", fileList.length);
                }

                $.ajax({
                    url: 'postWrite.php', 
                    type: 'POST',
                    data: datas,   
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) { 
                        console.log(response.result);
                        if(response.result == 1){
                            alert('글 작성 완료.');
                            location.replace('boardPage.php');
                        } else if(response.result == -1){
                            alert('작성 실패');
                            location.reload(true);
                        } 
                        else if(response.result == -2){
                            alert('파일 업로드 실패');
                            location.reload(true);
                        }   
                        else if(response.result == -3){
                            alert('업로드 불가한 확장자');
                            location.reload(true);
                        }  
                        else if(response.result == -4){
                            alert('데이터가 없습니다.');
                            location.reload(true);
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

    }//업데이트 
    else if( $mode == 'update')
    {
        if( !isset($_SESSION['mem_id']) )
        {
            messageAlert('회원아이디가 불일치 합니다.') ;
            exit;
        }
        else
        {
            $viewData = $c -> selectView($p_idx) ;
    
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
                messageAlert('잘못된 데이터 입니다.') ;
                exit;
            }

            if ($mem_id != $_SESSION['mem_id'] )
            {
                messageAlert('다른 회원의 글은 수정 할 수 없습니다.');
                exit;
            }

            $Html .=<<<END
            <div class="container" id="board">
                <div id="board_write">
                    <form method="post" id="writeform" >
                        <table class="table table-striped" style=" border: 1px solid #ddddda">
                            <thead>
                                <tr>
                                    <th colspan="2" style="background-color: #eeeeee; text-align: center;"><h3>게시판 글 수정</h3></th>
                                </tr>
                            </thead>	
                            <tbody style="border: 1px solid #ddddda " >
                                <tr>
                                    <td style="border: 1px solid #ddddda; display:flex;" >
                                        <span class="pull-left">&nbsp;&nbsp;&nbsp;게시판 : &nbsp;&nbsp;<b></b></span> 
                                        <select class="form-control" id="category" style="width:100px;">
                                            {$c_html}
                                        </select>
                                        <span class="pull-left">&nbsp;&nbsp;&nbsp;읽기 등급 : &nbsp;&nbsp;<b></b></span>
                                        <select class="form-control" id="level" style="width:100px; ">
                                            {$l_html}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #ddddda "><input type="text" class="form-control" placeholder="글 제목" name="title" id="inputTitle" required></td>
                                </tr>
                                <tr>
                                <tr>
                                    <td><p>&nbsp;&nbsp;&nbsp;기존 이미지 (이미지 클릭시 삭제 가능) </p></td>
                                </tr>
                                    <td style="border: 1px solid #ddddda; display:flex">
                                        <div class="imgs_before" style="display:flex">
                                            <img id="img" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><p>&nbsp;&nbsp;&nbsp;이미지 6개 까지 첨부 가능 (이미지 클릭시 삭제 가능)</p></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #ddddda; display:flex">
                                        <div class="imgs_wrap" style="display:flex">
                                            <img id="img" />
                                        </div>
                                        <div class="filebox" >
                                            <label for="input_file" onclick="fileUploadAction()" class="my_button">이미지 추가</label>
                                            <input type="file" id="input_imgs" multiple/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>	
                                    <td style="border: 1px solid #ddddda "><textarea class="form-control" placeholder="글 내용" name="content" id="inputContent" style="height: 350px" required></textarea></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="submit" id="write-submit" class="btn btn-primary">글수정<i class="fa fa-check spaceLeft"></i></button>
                    </form>
                </div>
            </div>
            <script>
            var update_files = new Array();
            var fileIndex = 0;
            var totalFileSize = 0.0;
            var fileList = new Array();
            var fileSizeList = new Array();
            const maxFileCnt = 6;
            const uploadSize = 10;
    
            function multiFileSettingInit(){
                window.fileIndex = 0;
                window.totalFileSize = 0.0;
                window.fileList = new Array();
                window.update_files = new Array();
                window.fileSizeList = new Array();
            }

            $(document).ready(function() {
                $('#inputTitle').val('$title');
                $('#inputContent').val('$content');
                $('#category').val('$board_cate').prop("selected",true);
                $('#level').val('$level').prop("selected",true);

                var user_level = '{$_SESSION['level']}' ;
                if( user_level == 'l_001')
                {
                    $("#level option[value='l_002']").remove();
                    $("#level option[value='l_003']").remove();
                }
                else if ( user_level == 'l_002' )
                {
                    $("#level option[value='l_003']").remove();
                }
                var imgfile = '{$f_dir}' ;

                if( !imgfile )
                {
                    console.log("ok1");
                }
                else
                {
                    var imgstr = imgfile.split(',');
                    for( i=0; i<imgstr.length; i++ )
                    {
                        console.log(imgstr[i]);
                        if( imgstr[i] != '' )
                        {
                            var liHtml = "<a href=\"javascript:void(0);\" onclick=\"javascript:beforefileDelete(this).done( fileMoveSort('before') ); \" data-idx ='"+ i +"'><img src=\"" + imgstr[i] + "\"  class='selProductFile' title='Click to remove' style='display: block; width: 100px; height: auto;' ></a>";
                            $(".imgs_before").append(liHtml) ;
                        }
                    }
                }
            }); 

            function fileUploadAction() {
                console.log("fileUploadAction");
                $("#input_imgs").trigger('click');
            }
    
            $("#input_imgs").on("change", function(){
                selectFile(this.files);
            });
    
            function selectFile( files ){
    
                if(files != null){
        
                    var img_cnt = $(".imgs_wrap a").length ;
                    var file_cnt = files.length ;
                    var errStr = "" ;
        
                    if ( img_cnt + file_cnt <= maxFileCnt )
                    {
                        for(var i = 0; i < file_cnt; i++)
                        {
                            var fileName = files[i].name;
                            var fileNameArr = fileName.split("\.");
                            var ext = fileNameArr[fileNameArr.length - 1];
                            ext = ext.toLowerCase();
                            var fileSize = files[i].size / 1024 / 1024;
        
                            if($.inArray(ext, ['jpg', 'gif', 'png', 'jpeg']) < 0 )
                            {
                                errStr += "[" + (i+1) + "번째 파일 미허용 확장자]" ;
                                continue ;
                            }
                            else if(fileSize > uploadSize)
                            {
                                errStr += "[" + (i+1) + "번째 파일 용량 초과(" + fileSize + "MB)]" ;
                                continue ;
                            }
                            else
                            {
                                totalFileSize += fileSize;
                                fileList[fileIndex] = files[i];
                                fileSizeList[fileIndex] = fileSize;
                                addFileList( files[i], fileIndex, file_cnt );
                                fileIndex++;
                            }
                        }
        
                        if ( errStr != "" )
                        {
                            alert(errStr);
                        }
                    }
                    else
                    {
                        alert("한번에 최대 6개까지 등록 가능합니다.");
                    }
                }
                else
                {
                    alert("파일 선택 에러");
                }
            }
        
            var liHtml = "";
            function addFileList( file, fileIndex, file_cnt)
            {
                var reader = new FileReader();
                reader.readAsDataURL(file);
    
                reader.onloadstart = function(e)
                {
                    var liHtml = "<a href=\"javascript:void(0);\" onclick=\"javascript:fileDelete(this).done( fileMoveSort() ); \" data-idx ='"+ fileIndex +"'><img src='' class='selProductFile' title='Click to remove' style='display: block; width: 100px; height: auto;' ></a>";
                    $(".imgs_wrap").append(liHtml) ;
                }
        
                reader.onload = function(e)
                {
                    var fileName = e.target.result;
                    $(".imgs_wrap").children('a').each( function(){
        
                        if ( $(this).attr('data-idx') == fileIndex )
                        {
                            $(this).find('img').attr('src', fileName ) ;
                        }
                    });
                }
            }

            function fileDelete(obj){
    
                var deferred = $.Deferred();
        
                try {
                    if( obj != null )
                    {
                        fIndex =  $(obj).closest('a').attr('data-idx') ;
                        console.log(fIndex);
                        totalFileSize -= fileSizeList[fIndex];
        
                        fileList.splice( fIndex , 1) ;
                        fileSizeList.splice( fIndex , 1) ;
                        $(obj).closest('a').remove() ;
                        fileIndex -- ;
        
                        totalSizeChaged();
        
                        deferred.resolve('성공');
                    }
        
                } catch (err) {
        
                    deferred.reject(err);
        
                }

                return deferred.promise();
            }
    
            function beforefileDelete(obj){
    
                var deferred2 = $.Deferred();
        
                try {
                    if( obj != null )
                    {
                        fIndex =  $(obj).closest('a').attr('data-idx') ;
                        console.log(fIndex);

                        update_files.splice( fIndex , 1) ;
                        $(obj).closest('a').remove() ;
                        deferred2.resolve('성공');
                    }
        
                } catch (err) {
        
                    deferred2.reject(err);
        
                }
                console.log(fileList);
                return deferred2.promise();
            }
            
            function fileMoveSort( mode ){
                
                if( mode == 'before' )
                {
                    var list = $( ".imgs_before > a" ) ;
                    var tmpupdate_filesArr = update_files.slice();
            
                    list.each(function(index, item){
                        $(this).attr('data-idx', index ) ;
                        update_files[index] = tmpupdate_filesArr[index];
                    })
                }
                else
                {
                    var list = $( ".imgs_wrap > a" ) ;
                    var tmpFileArr = fileList.slice();
                    var tmpFileSzieArr = fileSizeList.slice();
            
                    list.each(function(index, item){
                        $(this).attr('data-idx', index ) ;
                        fileList[index] = tmpFileArr[index];
                        fileSizeList[index] = tmpFileSzieArr[index];
                    })
                }
            }
    
            $(function(){
                $('#write-submit').click(function(e){
                    e.preventDefault();

                    var imgfile = $( ".imgs_before > a" ) ;
                    console.log(imgfile);

                    $(".imgs_before").children('a').each( function(){
                        update_files.push($(this).find('img').attr('src'));
                    });

                    if($("#inputTitle").val() =='')
                    {
                        alert('제목 입력 하세요');
                        $("#inputTitle").focus();
                        return false;
                    }
                    if($("#inputContent").val() =='')
                    {
                        alert('내용 입력 하세요');
                        $("#inputContent").focus();
                        return false;
                    }
                    
                    var datas = new FormData();
                    var select_cate = $("select[id=category]").val() ;
                    var select_level = $("select[id=level]").val() ;

                    if(update_files.length > 0) {
                        for( i=0; i<update_files.length; i++ )
                        {
                            console.log(update_files[i]);
                            var bename = "bename_"+i;
                            datas.append( bename , update_files[i]);
                        }
                    }

                    datas.append("p_idx", $p_idx );
                    datas.append("beimgcount" , update_files.length);
                    datas.append("mem_id", '{$_SESSION['mem_id']}' );
                    datas.append("name", '{$_SESSION['name']}' );
                    datas.append("title", $('#inputTitle').val() );
                    datas.append("content", $('#inputContent').val() );
                    datas.append("category", select_cate  );
                    datas.append("level", select_level );

                    if(fileList.length > 0) {
                        for(var i=0, len=fileList.length; i<len; i++) {
                            var name = "image_"+i;
                            datas.append(name, fileList[i]);
                        }
                        datas.append("image_count", fileList.length);
                    }

                    if( fileList.length + update_files.length > maxFileCnt ) 
                    {
                        alert('파일 갯수가 총 6개를 초과 했습니다.');
                        return false;
                    }

                    $.ajax({
                        url: 'postUpdate.php', 
                        type: 'POST',
                        data: datas,   
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (response) { 
                            console.log(response.result);
                            if(response.result == 1){
                                alert('글 수정 완료.');
                                location.replace('/board/viewPage.php?p_idx=$p_idx');
                            } else if(response.result == -1){
                                alert('수정 실패');
                                location.reload(true);
                            } 
                            else if(response.result == -2){
                                alert('파일 업로드 실패');
                                location.reload(true);
                            }   
                            else if(response.result == -3){
                                alert('업로드 불가한 확장자');
                                location.reload(true);
                            }  
                            else if(response.result == -4){
                                alert('데이터가 없습니다.');
                                location.reload(true);
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
    }

    echo $Html;
?>