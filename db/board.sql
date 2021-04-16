CREATE TABLE hb_member(
    idx INT IDENTITY (1, 1) NOT NULL,
    mem_id VARCHAR(50) PRIMARY KEY NOT NULL ,
    name VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    ip_address VARCHAR(20) , 
    date_created datetime ,
    hash_key VARCHAR(20) NOT NULL,
    level VARCHAR(10) FOREIGN KEY REFERENCES hb_level (level) NOT NULL DEFAULT 'l_001'
)

CREATE TABLE hb_level(
    level VARCHAR(10) PRIMARY KEY NOT NULL,
    l_name VARCHAR(15)
)

CREATE TABLE hb_post(
    p_idx INT IDENTITY (1, 1) PRIMARY KEY NOT NULL, 
	board_cate VARCHAR(20) FOREIGN KEY REFERENCES hb_board (board_cate) NOT NULL,
    mem_id VARCHAR(50) NOT NULL ,
    name VARCHAR(50) NOT NULL,
	title TEXT NOT NULL,
	content TEXT NOT NULL,
    ip_address VARCHAR(20) ,
    reg_date datetime NOT NULL,
    hit INT DEFAULT 0,
    like_p INT DEFAULT 0,
    level VARCHAR(10) NOT NULL DEFAULT 'l_001',
    f_name VARCHAR(255) ,
    f_type VARCHAR(255) ,
    f_dir VARCHAR(500),
    isdelete VARCHAR(1) NOT NULL DEFAULT 'N',
    modify_date datetime NULL
)


CREATE TABLE hb_board(
    board_cate VARCHAR(20) PRIMARY KEY NOT NULL ,
    board_name VARCHAR(50) NOT NULL,
    creat_id VARCHAR(50) NOT NULL,
)

CREATE TABLE hb_reply(
    re_idx INT IDENTITY (1, 1) PRIMARY KEY NOT NULL,
    p_idx INT NOT NULL,
    parent_idx VARCHAR(50) NULL,
    re_content text NOT NULL,
    mem_id VARCHAR(50) NOT NULL ,
    name VARCHAR(50) NOT NULL,
    ip_address VARCHAR(20) NOT NULL,
    reg_date datetime NOT NULL,
    isdelete VARCHAR(1) DEFAULT 'N' NOT NULL,
    modify_date datetime NULL,
)

CREATE TABLE hb_like(
    l_idx INT IDENTITY (1, 1) PRIMARY KEY NOT NULL,
    p_idx INT NOT NULL,
    mem_id VARCHAR(50) NOT NULL,
    reg_date datetime NOT NULL,
    islike VARCHAR(1) DEFAULT 'N' NOT NULL
)


CREATE PROCEDURE dbo.proc_hb_viewCount

@p_idx int

AS
   SELECT mem_id, name, title, content, reg_date, hit, like_p, level, f_dir FROM dbo.hb_post WHERE p_idx = @p_idx AND isdelete='N'

   if @@ROWCOUNT > 0

   UPDATE dbo.hb_post SET hit = hit + 1 WHERE p_idx= @p_idx 

GO

SELECT * ,(SELECT COUNT(*) AS cnt FROM hb_reply WHERE hb_reply.p_idx=hb_post.p_idx AND isdelete='N')
FROM dbo.hb_post WHERE board_cate = 'b_001' AND isdelete = 'N' ORDER BY p_idx DESC OFFSET 0 ROWS FETCH NEXT 5 ROWS ONLY

CREATE PROCEDURE dbo.proc_hb_likeCount

@p_idx int,
@mem_id VARCHAR(50),

AS
   INSERT INTO dbo.hb_like(p_idx ,mem_id, reg_date, islike ) VALUES (@p_idx, @mem_id, getdate(), 'Y') 

   if @@ROWCOUNT > 0

   UPDATE dbo.hb_post SET like_p = like_p + 1 WHERE p_idx= @p_idx 

GO


CREATE PROCEDURE dbo.proc_hb_writePost

@board_cate VARCHAR(20),
@mem_id VARCHAR(50),
@name VARCHAR(50),
@title TEXT,
@content TEXT,
@ip_address VARCHAR(20),
@reg_date datetime,
@level VARCHAR(10),
@f_name VARCHAR(255) ,
@f_type VARCHAR(255) ,
@f_dir VARCHAR(500)

AS
	INSERT INTO dbo.hb_post(board_cate, mem_id, name, title, content, ip_address, reg_date, level, f_name, f_type, f_dir) VALUES(@board_cate, @mem_id, @name, @title, @content, @ip_address, getdate(), @level, @f_name, @f_type, @f_dir)

	if @@ROWCOUNT > 0

	UPDATE dbo.hb_member SET postCnt = postCnt + 1 ,level = (
		CASE
			WHEN (postCnt) >= 10 THEN 'l_003'
            WHEN (postCnt) >= 5 THEN 'l_002' 
            ELSE 'l_001'
		END
    )
	WHERE mem_id = @mem_id
GO
