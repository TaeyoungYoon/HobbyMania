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
				$this->db->setAttribute(PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE, true); 
			}
			catch(PDOException $ex)
			{
				die("오류 : ".$ex->getMessage()) ;
			}
		}
	}

?>