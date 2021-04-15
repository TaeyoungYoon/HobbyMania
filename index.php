<?php
    require_once ("inc/header.php") ;
    
    echo<<<END
        <!-- Main jumbotron for a primary marketing message or call to action -->
        <div class="jumbotron">
            <div class="container">
                <h1>취미 MANIA 에 오신걸 환영합니다.</h1>
                <p>각자의 취미를 공유하는 커뮤니티입니다. 취미에 대한 이야기를 나누세요!!!</br> 등급제 운영 6번째 글부터 새싹 11번째 글 작성 이후 잡초로!</p>
                <p><a class="btn btn-primary btn-lg" href="/board/boardPage.php?page=1" role="button">입장하기 &raquo;</a></p>
            </div>
        </div>
    
        <div class="container">
            <!-- Example row of columns -->
            <div class="row">
                <div class="col-md-12">
                    <h2>농구MANIA</h2>
                    <p>마이클 조던 부터 르브론제임스 동네농구까지 우리 모두 농구 이야기로 대화 해보세요! 농구 좋아하는사람 여기여기 붙어라~</p>
                    <p><a class="btn btn-default" href="/board/boardPage.php?cate=b_001&page=1" role="button">입장하기 &raquo;</a></p>
                </div>
                <div class="col-md-12">
                    <h2>배구MANIA</h2>
                    <p>여자 배구이야기는 식상하다 남자배구 이야기하자 배구 이야기로 대화 해보세요! 배구 좋아하는사람 여기여기 붙어라~</p>
                    <p><a class="btn btn-default" href="/board/boardPage.php?cate=b_002&page=1" role="button">입장하기 &raquo;</a></p>
                </div>
                <div class="col-md-12">
                    <h2>축구MANIA</h2>
                    <p>축구는 분데스리가 ,EPL 이지 !! 해외축구 보러가자 ~ 축구 이야기로 대화 해보세요! 축구 좋아하는사람 여기여기 붙어라~</p>
                    <p><a class="btn btn-default" href="/board/boardPage.php?cate=b_003&page=1" role="button">입장하기 &raquo;</a></p>
                </div>
            </div>
    
            <hr>
END;
    require_once ("inc/footer.php") ;
?>