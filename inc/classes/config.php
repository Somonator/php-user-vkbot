<?php
	namespace classes;
	
	use classes\tools;
	
	class config {
		protected $config;

		function __construct() {
			$this->config = [
				/* Путь, куда установлен проект. Оставьте пустым, если он установлен в корень */
				'install_path' => '',
	
				/* Данные доступа для базы MySQL */
				'host' => '',
				'database' => '',
				'user' => '',
				'password' => '',
	
				/* Ключ доступа Вк */
				'access_token' => '',
	
				/* Пароль к админке */
				'admin_password' => ''
			];
		}
		
		public function getAll() {
			return $this->config;
		}

		public function getPath() {
			return $this->config['install_path'];
		}
		
		public function getDatabaseData() {
			$array = [
				'host' => $this->config['host'],
				'database' => $this->config['database'],
				'user' => $this->config['user'],
				'password' => $this->config['password']
			];
			
			return $array;
		}
		
		public function getTokenVk() {
			return $this->config['access_token'];
		}
		
		public function getAccessPassword() {
			return $this->config['admin_password'];
		}
	}
?>