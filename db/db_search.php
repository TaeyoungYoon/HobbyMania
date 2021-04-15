<?php
    /*-- 게시글 model db --*/
    require_once ("db_connection.php") ;
    require_once ("../lib/common.php") ;

    class db_search extends db_connection
    {
        function selectSearch( $page_set, $board_cate, $searchCate, $searchQuery )
        {
            if( !isset($_GET['page']))  $_GET['page'] = 1; $page = $_GET['page'];
            $limit_idx = ($page - 1) * $page_set ;

            if( $board_cate != '' ) // 전체 
            {
                $sql = "SELECT * , (SELECT COUNT(*) FROM hb_reply WHERE hb_reply.p_idx=hb_post.p_idx AND isdelete='N') AS replyCnt " ;
                $sql .= "FROM dbo.hb_post WHERE board_cate =:board_cate AND {$searchCate} like CONCAT('%', :query, '%') AND isdelete = 'N' ORDER BY p_idx DESC OFFSET :limit_idx ROWS FETCH NEXT :page_set ROWS ONLY";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':board_cate', $board_cate, PDO::PARAM_STR);
                $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
                $stmt->bindValue(':limit_idx', $limit_idx, PDO::PARAM_INT);
                $stmt->bindValue(':page_set', $page_set, PDO::PARAM_INT);
                $stmt->execute(); 
            }
            else// 게시판에 따라 출력
            {
                $stmt = $this->db->prepare("SELECT * , (SELECT COUNT(*) FROM hb_reply WHERE hb_reply.p_idx=hb_post.p_idx AND isdelete='N') AS replyCnt FROM dbo.hb_post WHERE {$searchCate} like CONCAT('%', :query, '%') AND isdelete = 'N' ORDER BY p_idx DESC OFFSET :limit_idx ROWS FETCH NEXT :page_set ROWS ONLY");
                $stmt->bindValue(':limit_idx', $limit_idx, PDO::PARAM_INT);
                $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
                $stmt->bindValue(':page_set', $page_set, PDO::PARAM_INT);
                $stmt->execute();
            }
            $localCnt = 0;
            $val = array();
            while($row = $stmt -> fetch(PDO::FETCH_ASSOC))
            {   
                $val[$localCnt] =  $row;
                $localCnt++;
            }      
            if( $val )
            {
                return $val;
            }
            else 
            {
                return NULL ;
            }
        }

            // 게시글 페이지네이션
        public function pageNation( $page_set, $block_set ,$board_cate ,$searchCate, $searchQuery )
        {
            if( $board_cate != '' )
            {  
                $stmt = $this->db->prepare("SELECT count(p_idx) AS total FROM dbo.hb_post WHERE board_cate =:board_cate AND {$searchCate} like CONCAT('%', :query, '%') AND isdelete = 'N'");
                $stmt->bindValue(':board_cate', $board_cate, PDO::PARAM_STR);
                $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
                $stmt->execute(); 
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else
            {
                $stmt = $this->db->prepare("SELECT count(p_idx) AS total FROM dbo.hb_post WHERE {$searchCate} like CONCAT('%', :query, '%') AND isdelete = 'N'");
                $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
                $stmt->execute(); 
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            $total = $result['total'] ;

            $total_page = ceil($total / $page_set );
            $total_block = ceil($total_page / $block_set) ;

            if( !isset($_GET['page']))  $_GET['page'] = 1; $page = $_GET['page'];
            $block = ceil($page / $block_set ) ;

            $first_page = (($block - 1) * $block_set) + 1; 
            $last_page = min ($total_page, $block * $block_set);

            $prev_page = $page - 1;
            $next_page = $page + 1;

            $prev_block = $block - 1;
            $next_block = $block + 1;
            
            $prev_block_page = $prev_block * $block_set;
            $next_block_page = $next_block * $block_set - ($block_set - 1);
            
            if( $prev_page > 0 )
            {
                $page_html = "<li><a href='{$_SERVER['PHP_SELF']}?cate=$board_cate&page=$prev_page'>이전</a></li> " ;
            }
            else
            {
                $page_html = '' ;
            }

            if( $prev_block > 0)
            {
                $page_html .= "<li><a href='{$_SERVER['PHP_SELF']}?cate=$board_cate&page=$prev_block_page'>...</a></li> " ;
            }
            else
            {
                $page_html .= '' ;
            }

            for ($i=$first_page; $i<=$last_page; $i++)
            {
                if( $i != $page )
                {
                    $page_html .= "<li><a href='{$_SERVER['PHP_SELF']}?cate=$board_cate&page=$i'>$i</a></li> " ;
                }
                else
                {
                    $page_html .= "<li><a href='javascript:void(0);' id='selectedPage'>$i</a></li>" ;
                }
            }

            if( $next_block <= $total_block )
            {
                $page_html .= "<li><a href='{$_SERVER['PHP_SELF']}?cate=$board_cate&page=$next_block_page'>...</a></li> " ;
            }
            else
            {
                $page_html .= "" ;
            }

            if( $next_page <= $total_page )
            {
                $page_html .= "<li><a href='{$_SERVER['PHP_SELF']}?cate=$board_cate&page=$next_page'>다음</a></li>" ;
            }
            else
            {
                $page_html .= "" ;
            }

            echo $page_html;
        }
    }
?>
