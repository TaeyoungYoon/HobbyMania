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
            <div class="row">
                <div class="col-md-4" id="mainRank">
                    <h3>농구MANIA</h3>
                    <ul>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="#">test</a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="#">test</a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                    </ul>
                    <p><a class="btn btn-default" href="/board/boardPage.php?cate=b_001&page=1" role="button">입장하기 &raquo;</a></p>
                </div>
                <div class="col-md-4" id="mainRank">
                    <h3>배구MANIA</h3>
                    <ul>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="#">test</a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="#">test</a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                    </ul>
                    <p><a class="btn btn-default" href="/board/boardPage.php?cate=b_002&page=1" role="button">입장하기 &raquo;</a></p>
                </div>
                <div class="col-md-4" id="mainRank">
                    <h3>축구MANIA</h3>
                    <ul>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="#">test</a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="#">test</a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                        <li>
                            <div class="text">
                                <a href="www.naver.com"><span>test</span></a>
                            </div>
                        </li>
                    </ul>
                    <p><a class="btn btn-default" href="/board/boardPage.php?cate=b_003&page=1" role="button">입장하기 &raquo;</a></p>
                </div>
            </div>
    
            <hr>
END;
    require_once ("inc/footer.php") ;
?>