<?php
    /*-- 게시글 model db --*/
    require_once ("db_connection.php") ;
    require_once ("../lib/common.php") ;

    class db_board extends db_connection
    {

        // 게시글 등록
        public function writePost( $board_cate, $mem_id , $name, $title, $content, $level, $f_name ,$f_type, $f_dir)
        {
            $ip = $_SERVER["REMOTE_ADDR"] ;
            try
            {
                $this->db->beginTransaction();
                $query = "INSERT INTO dbo.hb_post(board_cate, mem_id, name, title, content, ip_address, reg_date, level, f_name, f_type, f_dir)" ;
                $query .= "VALUES(:board_cate, :mem_id, :name, :title, :content, :ip, getdate(), :level, :f_name, :f_type, :f_dir)" ;
                $stmt = $this->db->prepare($query) ;
                $stmt->bindValue(':board_cate',$board_cate,PDO::PARAM_STR) ;
                $stmt->bindValue(':mem_id',$mem_id,PDO::PARAM_STR) ;
                $stmt->bindValue(':name',$name,PDO::PARAM_STR) ;
                $stmt->bindValue(':title',$title,PDO::PARAM_STR) ;
                $stmt->bindValue(':content',$content,PDO::PARAM_STR) ;
                $stmt->bindValue(':ip',$ip,PDO::PARAM_STR) ;
                $stmt->bindValue(':level',$level,PDO::PARAM_STR) ;
                $stmt->bindValue(':f_name',$f_name,PDO::PARAM_STR) ;
                $stmt->bindValue(':f_type',$f_type,PDO::PARAM_STR) ;
                $stmt->bindValue(':f_dir',$f_dir,PDO::PARAM_STR) ;
                $result = $stmt->execute() ;
                $this->db->commit() ;
            } 
            catch (PDOException $pex)
            {
                $this->db->rollBack() ;
                echo " 에러 : ".$pex->getMessage() ;
            }
            if ($result) // 게시글 등록 수에 따라 등급 업데이트
            {
                $query2 = "UPDATE dbo.hb_member SET postCnt = postCnt + 1 ,level = (
                    CASE
                        WHEN (postCnt) >= 10 THEN 'l_003'
                        WHEN (postCnt) >= 5 THEN 'l_002' 
                        ELSE 'l_001'
                    END
                )
                WHERE mem_id = :mem_id" ;
                $stmt2 = $this->db->prepare($query2) ;
                $stmt2->bindValue(':mem_id',$mem_id,PDO::PARAM_STR) ;
                $result2 = $stmt2->execute() ;
                return $result2;
            } 
            else
            {
                return false;
            }
        }

        // 게시판 선택
        public function selectBoardOption( $type )
        {
            if( $type == 'board' )
            {
                $stmt = $this -> db -> prepare("SELECT * FROM dbo.hb_board");
            }
            else if( $type == 'level')
            {
                $stmt = $this -> db -> prepare("SELECT * FROM dbo.hb_level");
            }
            $stmt->execute();
            
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
                return NULL;
            }
        }

        // 게시글 출력
        public function selectPost( $page_set ,$board_cate )
        {
            if( !isset($_GET['page']))  $_GET['page'] = 1; $page = $_GET['page'];
            $limit_idx = ($page - 1) * $page_set ;
            
            if( $board_cate != '' ) // 전체 
            {
                $stmt = $this->db->prepare("SELECT * , (SELECT COUNT(*) FROM hb_reply WHERE hb_reply.p_idx=hb_post.p_idx AND isdelete='N') AS replyCnt FROM dbo.hb_post WHERE board_cate =:board_cate AND isdelete = 'N' ORDER BY p_idx DESC OFFSET :limit_idx ROWS FETCH NEXT :page_set ROWS ONLY");
                $stmt->bindValue(':board_cate', $board_cate, PDO::PARAM_STR);
                $stmt->bindValue(':limit_idx', $limit_idx, PDO::PARAM_INT);
                $stmt->bindValue(':page_set', $page_set, PDO::PARAM_INT);
                $stmt->execute(); 
            }
            else// 게시판에 따라 출력
            {
                $stmt = $this->db->prepare("SELECT * , (SELECT COUNT(*) FROM hb_reply WHERE hb_reply.p_idx=hb_post.p_idx AND isdelete='N') AS replyCnt FROM dbo.hb_post WHERE isdelete = 'N' ORDER BY p_idx DESC OFFSET :limit_idx ROWS FETCH NEXT :page_set ROWS ONLY");
                $stmt->bindValue(':limit_idx', $limit_idx, PDO::PARAM_INT);
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
        public function pageNation( $page_set, $block_set ,$board_cate )
        {
            if( $board_cate != '' )
            {  
                $stmt = $this->db->prepare("SELECT count(p_idx) AS total FROM dbo.hb_post WHERE board_cate =:board_cate AND isdelete = 'N'");
                $stmt->bindValue(':board_cate', $board_cate, PDO::PARAM_STR);
                $stmt->execute(); 
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else
            {
                $stmt = $this->db->prepare("SELECT count(p_idx) AS total FROM dbo.hb_post WHERE isdelete = 'N'");
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

        // 포스트 글 내용 출력 (글 업데이트시 사용) 조회수 카운트 x
        public function selectView( $p_idx )
        {
            $stmt = $this->db->prepare("SELECT * FROM dbo.hb_post WHERE p_idx=:p_idx");
            $stmt->bindValue(':p_idx', $p_idx, PDO::PARAM_INT);
            $stmt->execute(); 

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

        // 포스트 글 내용 출력 프로시저 사용으로 게시글 열람시 조회수 update
        public function updateSelect( $p_idx )
        {
            $stmt = $this->db->prepare("exec proc_hb_viewCount $p_idx");
            $stmt->execute(); 

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

        //게시글 삭제
        public function postDelete( $p_idx ){
            try
            {
                $this->db->beginTransaction();
                $stmt = $this->db->prepare("UPDATE dbo.hb_post SET isdelete = 'Y' WHERE p_idx=:p_idx");
                $stmt->bindValue(':p_idx',$p_idx, PDO::PARAM_INT);
                $status = $stmt->execute();
                $this->db->commit();
                if($status == true){
                    return 1;
                } else {
                    return 0;
                }
            }
            catch (PDOException $pex) 
            {
                $this->db->rollBack();
                echo "에러 : ".$pex->getMessage();
            }
        }

        // 게시글 업데이트
        public function updatePost( $p_idx, $board_cate, $title, $content, $level, $f_name ,$f_type, $f_dir)
        {
            $ip = $_SERVER["REMOTE_ADDR"] ;
            try
            {
                $this->db->beginTransaction();
                $query = "UPDATE dbo.hb_post SET board_cate=:board_cate, modify_date=getdate(), title=:title, content=:content, ip_address=:ip, level=:level, f_name=:f_name, f_type=:f_type, f_dir=:f_dir WHERE p_idx=:p_idx" ;
                $stmt = $this->db->prepare($query) ;
                $stmt->bindValue(':board_cate',$board_cate,PDO::PARAM_STR) ;
                $stmt->bindValue(':title',$title,PDO::PARAM_STR) ;
                $stmt->bindValue(':content',$content,PDO::PARAM_STR) ;
                $stmt->bindValue(':ip',$ip,PDO::PARAM_STR) ;
                $stmt->bindValue(':level',$level,PDO::PARAM_STR) ;
                $stmt->bindValue(':f_name',$f_name,PDO::PARAM_STR) ;
                $stmt->bindValue(':f_type',$f_type,PDO::PARAM_STR) ;
                $stmt->bindValue(':f_dir',$f_dir,PDO::PARAM_STR) ;
                $stmt->bindValue(':p_idx',$p_idx,PDO::PARAM_INT) ;
                $result = $stmt->execute() ;
                $this->db->commit() ;
            } 
            catch (PDOException $pex)
            {
                $this->db->rollBack() ;
                echo " 에러 : ".$pex->getMessage() ;
            }
            if ($result)
            {
                return $result;
            } 
            else
            {
                return false;
            }
        }

        // 댓글 작성
        public function writeReply( $p_idx, $mem_id , $name, $replycontent)
        {
            $ip = $_SERVER["REMOTE_ADDR"] ;
            try
            {
                $this->db->beginTransaction();
                $query = "INSERT INTO dbo.hb_reply(p_idx, mem_id, name, re_content, ip_address, reg_date )" ;
                $query .= "VALUES(:p_idx, :mem_id, :name, :re_content, :ip, getdate())" ;
                $stmt = $this->db->prepare($query) ;
                $stmt->bindValue(':p_idx',$p_idx,PDO::PARAM_INT) ;
                $stmt->bindValue(':mem_id',$mem_id,PDO::PARAM_STR) ;
                $stmt->bindValue(':name',$name,PDO::PARAM_STR) ;
                $stmt->bindValue(':re_content',$replycontent,PDO::PARAM_STR) ;
                $stmt->bindValue(':ip',$ip,PDO::PARAM_STR) ;
                $result = $stmt->execute() ;
                $this->db->commit() ;
            } 
            catch (PDOException $pex)
            {
                $this->db->rollBack() ;
                echo " 에러 : ".$pex->getMessage() ;
            }
            if ($result)
            {
                return $result ;
            } 
            else
            {
                return false;
            }
        }

        // 대댓글 작성
        public function rewriteReply( $p_idx, $parent_idx, $mem_id , $name, $replycontent)
        {
            $ip = $_SERVER["REMOTE_ADDR"] ;
            try
            {
                $this->db->beginTransaction();
                $query = "INSERT INTO dbo.hb_reply(p_idx, parent_idx, mem_id, name, re_content, ip_address, reg_date )" ;
                $query .= "VALUES(:p_idx, :parent_idx, :mem_id, :name, :re_content, :ip, getdate())" ;
                $stmt = $this->db->prepare($query) ;
                $stmt->bindValue(':p_idx',$p_idx,PDO::PARAM_INT) ;
                $stmt->bindValue(':parent_idx',$parent_idx,PDO::PARAM_STR) ;
                $stmt->bindValue(':mem_id',$mem_id,PDO::PARAM_STR) ;
                $stmt->bindValue(':name',$name,PDO::PARAM_STR) ;
                $stmt->bindValue(':re_content',$replycontent,PDO::PARAM_STR) ;
                $stmt->bindValue(':ip',$ip,PDO::PARAM_STR) ;
                $result = $stmt->execute() ;
                $this->db->commit() ;
            } 
            catch (PDOException $pex)
            {
                $this->db->rollBack() ;
                echo " 에러 : ".$pex->getMessage() ;
            }
            if ($result)
            {
                return $result ;
            } 
            else
            {
                return false;
            }
        }

        // 댓글 출력
        public function selectReply( $p_idx )
        {
            $stmt = $this->db->prepare("SELECT re_idx, mem_id, name, re_content, reg_date FROM dbo.hb_reply WHERE p_idx=:p_idx AND isdelete='N' ORDER BY re_idx ASC");
            $stmt->bindValue(':p_idx', $p_idx, PDO::PARAM_INT);
            $stmt->execute(); 

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

        // 댓글 변경
        public function updateReply( $re_idx, $replycontent)
        {
            $ip = $_SERVER["REMOTE_ADDR"] ;
            try
            {
                $this->db->beginTransaction();
                $query = "UPDATE dbo.hb_reply SET modify_date=getdate(), re_content=:replycontent, ip_address=:ip WHERE re_idx=:re_idx" ;
                $stmt = $this->db->prepare($query) ;
                $stmt->bindValue(':re_idx',$re_idx,PDO::PARAM_INT) ;
                $stmt->bindValue(':replycontent',$replycontent,PDO::PARAM_STR) ;
                $stmt->bindValue(':ip',$ip,PDO::PARAM_STR) ;
                $result = $stmt->execute() ;
                $this->db->commit() ;
            } 
            catch (PDOException $pex)
            {
                $this->db->rollBack() ;
                echo " 에러 : ".$pex->getMessage() ;
            }
            if ($result)
            {
                return $result;
            } 
            else
            {
                return false;
            }
        }

         // 댓글 삭제
        public function deleteReply( $re_idx ){
            try
            {
                $this->db->beginTransaction();
                $stmt = $this->db->prepare("UPDATE dbo.hb_reply SET isdelete = 'Y' WHERE re_idx=:re_idx");
                $stmt->bindValue(':re_idx',$re_idx, PDO::PARAM_INT);
                $status = $stmt->execute();
                $this->db->commit();
                if($status == true){
                    return 1;
                } else {
                    return 0;
                }
            }
            catch (PDOException $pex) 
            {
                $this->db->rollBack();
                echo "에러 : ".$pex->getMessage();
            }
        }

         // 추천 출력
        public function selectLike( $p_idx, $mem_id )
        {
            $stmt = $this->db->prepare("SELECT * FROM dbo.hb_like WHERE p_idx=:p_idx AND mem_id=:mem_id");
            $stmt->bindValue(':p_idx', $p_idx, PDO::PARAM_INT);
            $stmt->bindValue(':mem_id', $mem_id, PDO::PARAM_STR);
            $stmt->execute(); 

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
        
        // 추천 업데이트
        public function updateLike( $p_idx , $mem_id )
        {
            try
            {
                $this->db->beginTransaction();
                $stmt = $this->db->prepare("exec dbo.proc_hb_likeCount $p_idx, $mem_id");
                $result = $stmt->execute(); 
                $this->db->commit();
                if($result == true){
                    return 1;
                } else {
                    return 0;
                }
            }
            catch (PDOException $pex)
            {
                $this->db->rollBack();
                echo "에러 : ".$pex->getMessage();
            }
        }
    }
?>