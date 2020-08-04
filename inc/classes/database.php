<?php
	namespace classes;
	
	use classes\config;
	use mysqli;
	
	class database {
		public $connect;
		
		function __construct() {
			$config = new config();
			$this->data = $config->getDatabaseData();
			$this->connect = $this->init();
		}
		
		private function init() {
			$database = new mysqli($this->data['host'], $this->data['user'], $this->data['password'], $this->data['database']);
			
			if($database->connect_errno) {
				die('<h1>Ошибка подключения к базе данных.</h1>');
			}
			
			$database->set_charset('utf8');
			
			return $database;
		}
		
		private function createTables() {
			$this->database->query('
				CREATE TABLE IF NOT EXISTS last_messages (
				 id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
					name text,
					down_message text,
					up_message text
				);
			') or die ($database->error);
			
			$this->database->query('
				CREATE TABLE IF NOT EXISTS answers (
					id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
					question text NOT NULL,
					answer text NOT NULL,
					priority int DEFAULT 0
				);
			') or die ($database->error);	
			
			$this->database->query('
				CREATE TABLE IF NOT EXISTS admins (
					id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id int NOT NULL
				);
			') or die ($database->error);
		}
	}