<?php
	/*-- 회원 model db --*/
	// pdo 연결
	require_once ("db_info.php");

	class db_connection
	{
		protected $db ;

		public function __construct()
		{
			$this->dbConnect() ;
		}

		private function dbConnect()
		{
			try
			{
				$this->db = new PDO(DSN, DB_USER, DB_PASS) ;
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;
			}
			catch(PDOException $ex)
			{
				die("오류 : ".$ex->getMessage()) ;
			}
		}
	}

?>