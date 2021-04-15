<?php
	/*-- 게시글 View 페이지 --*/

	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	require_once ("../inc/sub_header.php") ;
	require_once ("option.php") ;
	require_once ('../db/db_info.php') ;
	require_once ($root.'/lib/define.php');
	require_once ($root.'/lib/common.php');

	$cate = $list_html = '' ;

	if( isset($_GET['cate']) )	$cate = $_GET['cate'];	
	if( isset($_GET['searchCate']) )	$searchCate = $_GET['searchCate'];	
	if( isset($_GET['query']) )	$query = $_GET['query'];
    // 검색어 관리
    $query = trim($query);
	// 게시판 구분
	if( isset($cate) && $cate == 'b_001' )
	{
		$b_name = $b_name[0];
	}
	else if( isset($cate) && $cate == 'b_002' )
	{
		$b_name = $b_name[1];
	}
	else if( isset($cate) && $cate == 'b_003' )
	{
		$b_name = $b_name[2];
	}
	else
	{
		$b_name = '전체' ;
	}

	$c = new db_search();
	$board_data = $c->selectSearch(10, $cate, $searchCate, $query );
	
	// DB에서 select 값 표시
	if(isset($board_data) && $board_data != '' )
	{
		foreach ($board_data as $val){

			$reg_date = explode(':', $val['reg_date']) ;
			$f_dir = explode('../', $val['f_dir']) ;
			$level = $val['level'];
			$board_cate = $val['board_cate'] ;
			$p_idx = $val['p_idx'] ;
			$replyCnt = $val['replyCnt'] ;

			$list_html .= "<tr id='board_table'>";
			$list_html .= "<th class='col-md-1'>{$val['p_idx']}</th>" ;
			$list_html .= "<th class='col-md-1'>";

			if( isset($val['f_dir']) && $val['f_dir'] != '' ) // 이미지
			{
				if( strpos($val['f_dir'],',') !== false )
				{
					$f_dir = explode(',', $val['f_dir']);
					$f_dir = $f_dir[0];
				}
				else
				{
					$f_dir = $val['f_dir'] ;
				}
				$list_html .= "<img src='{$f_dir}' style='
				padding:0px;
				width:70px; height:50px;'>" ;
			}

			$list_html .= "</th>" ;
			$list_html .= "<th class='col-md-4'><a href='/board/viewPage.php?cate=$board_cate&p_idx=$p_idx' class='vf_ellipsis' id='vf_360'>{$val['title']}<span style='color:red;'> [ $replyCnt ]</span></a><br><span id='board_name'>{$_ENV[$board_cate]}게시판</span></th>" ;
			$list_html .= "<th class='col-md-1'><span style='color:green;'>&nbsp;&nbsp;{$_ENV[$level]}</span></th>" ;
			$list_html .= "<th class='col-md-1'>{$val['name']}</th>" ;
			$list_html .= "<th class='col-md-2'>{$reg_date[0]}:{$reg_date[1]}</th>" ;
			$list_html .= "<th class='col-md-1'>{$val['like_p']}</th>" ;
			$list_html .= "<th class='col-md-2'>{$val['hit']}</th>" ;
			$list_html .= "</tr>";
		}
	}


    echo <<<END
		<div class="container" id="board">
			<div id="board_area"> 
				<h1><b>{$b_name} MANIA</b></h1><br>
				<h4>'{$query}' 에 대한 검색 결과.</h4><br>
				<table class="table table-hover" style="border: 1px solid #ddddda">
				<thead>
					<tr>
						<th class="col-md-1" id='board_data'">번호</th>
						<th class="col-md-1	" id='board_data'"></th>
						<th class="col-md-4	" id='board_data'">제목</th>
						<th class="col-md-1" id='board_data'">글 등급</th>
						<th class="col-md-1" id='board_data'">작성자</th>
						<th class="col-md-2" id='board_data'">작성일</th>
						<th class="col-md-1" id='board_data'">추천</th>
						<th class="col-md-2" id='board_data'">조회수</th>
					</tr>
				</thead>
				<tbody>
						{$list_html}
				</tbody>
				</table>
				<hr/>
				<div class="text-center">
					<ul class="pagination">
END;
				$test = $c -> pageNation( 10, 3, $cate, $searchCate, $query);
	echo<<<END
					</ul>
				</div>
				<div class="searchbox" id="searchbox" style="text-align: center; margin: 20px 0px 20px 0px;">
					<form action="/board/searchPage.php" method="get">
                        <input type="hidden" name="cate" value="$cate"/>    
						<select name="searchCate">
							<option value="title">제목</option>
							<option value="name">글쓴이</option>
							<option value="content">내용</option>
						</select>
						<input type="text" id="query" name="query" size="40" required="required" /> <button>검색</button>
					</form>
				</div>
			</div>
END;

	require_once ("../inc/footer.php") ;
?>