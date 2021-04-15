<?php
    /*-- 게시글 업로드 backend --*/
    require_once ('../db/db_info.php') ;

    if( isset($_POST['imgcount']) && $_POST['imgcount'] != '' )
    {
        $beforeimgcnt = $_POST['imgcount'] ;
        for( $i = 0 ; $i < $beforeimgcnt ; $i++)
        {
            $beforeimg = $_POST['bename_'+ $i].',' ;
        }
    }
    
    $imageKind = array ('image/pjpeg', 'image/jpeg', 'image/JPG', 'image/X-PNG', 'image/PNG', 'image/png', 'image/x-png');
    $dir = "../img/";

    if ( isset($_POST['mem_id']) && $_POST['mem_id'] != '' 
    && isset($_POST['name']) && $_POST['name'] != '' 
    && isset($_POST['title']) && $_POST['title'] != '' 
    && isset($_POST['content']) && $_POST['content'] != '' 
    && isset($_POST['level']) && $_POST['level'] != '' 
    && isset($_POST['category']) && $_POST['category'] != '' )
    {

        $mem_id = $_POST['mem_id'];
        $name = $_POST['name'];
        $title = htmlspecialchars($_POST['title']) ;
        $content = htmlspecialchars($_POST['content']) ;
        $level = $_POST['level'] ;
        $category = $_POST['category'] ;

        $c = new db_board();
        if ( isset($_POST['image_count']) && $_POST['image_count'] != '' )
        {
            for($i=0; $i<$_POST['image_count']; $i++) {

                $image_id[$i] = "image_".$i;
                $image_file[$i] = time().$i.".jpg";
                $image_type[$i] = $_FILES[$image_id[$i]]['type'] ;
                $image_dir[$i] = $dir.$image_file[$i] ;

                $add_file= join(",", $image_file) ;
                $add_type= join(",", $image_type) ;
                $add_dir= join(",", $image_dir) ;

                if(isset($_FILES[$image_id[$i]]) && !$_FILES[$image_id[$i]]['error'])
                {
                    if(in_array($image_type[$i], $imageKind))
                    {
                        if(move_uploaded_file($_FILES[$image_id[$i]]['tmp_name'], $image_dir[$i]))
                        {
                        }
                        else
                        {
                            echo json_encode(array('result' => '-2')); // 파일 업로드 실패
                        }
                    }
                    else
                    {
                        echo json_encode(array('result' => '-3'));  //확장자 실패
                    }
                }
            }

            $post = $c -> writePost( $category, $mem_id, $name, $title, $content, $level, $add_file, $add_type, $add_dir );
            if( $post )
            {
                echo json_encode(array('result' => '1')); // 이미지 있게 post 작성
            }
            else
            {
                echo json_encode(array('result' => '-1')); // post  작성 실패
            }
        }
        else
        {
            $image_file ='';
            $image_type= '';
            $image_dir = '';
            $post = $c -> writePost( $category, $mem_id, $name, $title, $content, $level, $image_file, $image_type, $image_dir );
            echo json_encode(array('result' => '1')); // 이미지 없이 post 작성
        }
    }
    else
    {
        echo json_encode(array('result' => '-4')); // 데이터 안들어옴 실패
    }
?>