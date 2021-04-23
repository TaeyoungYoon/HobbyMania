<?php
    /*-- 회원 model db --*/
    require_once ("db_connection.php") ;
    require_once ("../lib/common.php") ;

    class db_member extends db_connection
    {
        // 회원 insert
        public function insertUser($mem_id, $name, $password, $email ,$otpkey) 
        {

            $hash = hashPassword($password) ;
            $encrypted_password = $hash['encrypted'] ; 
            $key = $hash['key'] ; 
            $ip = $_SERVER["REMOTE_ADDR"] ;
            try
            {
                $this->db->beginTransaction();
                $query = "INSERT INTO dbo.hb_member(mem_id, name, password, email, ip_address, date_created, hash_key, otpkey) " ;
                $query .= "VALUES(:mem_id, :name, :password, :email, :ip, getdate(), :hash_key, :otpkey)" ;
                $stmt = $this->db->prepare($query) ; 
                $stmt->bindValue(':mem_id',$mem_id,PDO::PARAM_STR) ;
                $stmt->bindValue(':name',$name,PDO::PARAM_STR) ;
                $stmt->bindValue(':password',$encrypted_password,PDO::PARAM_STR) ;
                $stmt->bindValue(':email',$email,PDO::PARAM_STR) ;
                $stmt->bindValue(':ip',$ip,PDO::PARAM_STR) ;
                $stmt->bindValue(':hash_key',$key,PDO::PARAM_STR) ;
                $stmt->bindValue(':otpkey',$otpkey,PDO::PARAM_STR) ;
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
                $stmt = $this->db->prepare("SELECT * FROM dbo.hb_member WHERE mem_id = :mem_id");
                $stmt->bindValue(':mem_id', $mem_id, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                return $user;
            } 
            else
            {
                return false;
            }
        }

        // 아이디 중복 체크
        public function checkUserID($mem_id)
        {
            $stmt = $this->db->prepare("SELECT count(mem_id) from dbo.hb_member WHERE mem_id=:mem_id");
            $stmt -> bindValue(':mem_id', $mem_id, PDO::PARAM_STR);
            $stmt -> execute();

            if( $row = $stmt->fetch() )
            {
                return $row[0];
            }
            else
            {
                return -1;
            }
        }

        // 별명 중복 체크
        public function checkUserName($name)
        {
            $stmt = $this->db->prepare("SELECT count(name) from dbo.hb_member WHERE name=:name");
            $stmt -> bindValue(':name', $name, PDO::PARAM_STR);
            $stmt -> execute();

            if( $row = $stmt->fetch() )
            {
                return $row[0];
            }
            else
            {
                return -1;
            }
        }

        // 회원 이메일 체크
        public function checkUserEmail($email)
        {
            $stmt = $this->db->prepare("SELECT count(email) from dbo.hb_member WHERE email=:email");
            $stmt -> bindValue(':email', $email, PDO::PARAM_STR);
            $stmt -> execute();

            if( $row = $stmt->fetch() )
            {
                return $row[0];
            }
            else
            {
                return -1;
            }
        }
        
        // 회원 정보 수정시 기존 별명 제외 후 중복 체크
        public function changeCheckUserName($name, $current_name )
        {
            $stmt = $this->db->prepare("SELECT count(name) from (SELECT name FROM dbo.hb_member WHERE name NOT IN(:current_name)) AS dup WHERE name=:name");
            $stmt -> bindValue(':name', $name, PDO::PARAM_STR);
            $stmt -> bindValue(':current_name', $current_name, PDO::PARAM_STR);
            $stmt -> execute();

            if( $row = $stmt->fetch() )
            {
                return $row[0];
            }
            else
            {
                return -1;
            }
        }

        // 회원 체크 
        public function selectUser($mem_id, $password)
        {
            $sql = "SELECT * FROM dbo.hb_member WHERE mem_id=:mem_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':mem_id', $mem_id, PDO::PARAM_STR);
            $stmt->execute();

            if($user = $stmt->fetch())
            {
                $key = $user['hash_key'];
                $encrypted_password = $user['password'];
                $hash = checkPw($key, $password);

                if ($encrypted_password == $hash)
                {
                    return $user;
                }
            }
            else 
            {
                return NULL;
            }
        }

        public function otpUser($mem_id)
        {
            $sql = "SELECT * FROM dbo.hb_member WHERE mem_id=:mem_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':mem_id', $mem_id, PDO::PARAM_STR);
            $stmt->execute();

            if($user = $stmt->fetch())
            {
                return $user;
            }
            else 
            {
                return NULL;
            }
        }

        // 회원 업데이트
        public function UserUpdate($mem_id, $name, $password, $email)
        {
            $hash = hashPassword($password) ;
            $encrypted_password = $hash['encrypted'] ; 
            $key = $hash['key'] ; 

            try
            {
                $this->db->beginTransaction();
                $sql='UPDATE dbo.hb_member SET name=:name, email=:email, password=:password, hash_key=:hash_key WHERE mem_id = :mem_id';
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':mem_id',$mem_id,PDO::PARAM_STR) ;
                $stmt->bindValue(':name',$name,PDO::PARAM_STR) ;
                $stmt->bindValue(':password',$encrypted_password,PDO::PARAM_STR) ;
                $stmt->bindValue(':email',$email,PDO::PARAM_STR) ;
                $stmt->bindValue(':hash_key',$key,PDO::PARAM_STR) ;
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
                $stmt = $this->db->prepare("SELECT * FROM dbo.hb_member WHERE mem_id = :mem_id");
                $stmt->bindValue(':mem_id', $mem_id, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                return $user;
            } 
            else
            {
                return false;
            }
        }

        public function pwUpdate($mem_id, $password )
        {
            $hash = hashPassword($password) ;
            $encrypted_password = $hash['encrypted'] ; 
            $key = $hash['key'] ; 

            try
            {
                $this->db->beginTransaction();
                $sql='UPDATE dbo.hb_member SET password=:password, hash_key=:hash_key WHERE mem_id = :mem_id';
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':mem_id',$mem_id,PDO::PARAM_STR) ;
                $stmt->bindValue(':password',$encrypted_password,PDO::PARAM_STR) ;
                $stmt->bindValue(':hash_key',$key,PDO::PARAM_STR) ;
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
                $stmt = $this->db->prepare("SELECT * FROM dbo.hb_member WHERE mem_id = :mem_id");
                $stmt->bindValue(':mem_id', $mem_id, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                return $user;
            } 
            else
            {
                return false;
            }
        }

        // 회원 삭제
        public function UserDelete($mem_id){
            try
            {
                $this->db->beginTransaction();
                $stmt = $this->db->prepare("DELETE FROM dbo.hb_member WHERE mem_id=:mem_id");
                $stmt->bindValue(':mem_id',$mem_id, PDO::PARAM_STR);
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
    }
?>
