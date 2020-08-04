<?php
	namespace classes;
	
	use classes\config;
	
	class checkData {
		function __construct() {
			$config = new config();
			$this->data = $config->getAll();
			$this->checkToken();
		}
		
		function checkToken() {
			if (empty($this->data['access_token'])) {
				die('<h1>Токен доступа не введен. Введите в config.php.</h1>');
			}
			
			$this->checkDatabase();
		}
		
		function checkDatabase() {
			if(empty($this->data['host']) || empty($this->data['user']) || empty($this->data['database'])) {
				die('<h1>Заполните все поля для подключения к базе данных в config.php.</h1>');
			}
			
			$this->checkAccess();
		}
		
		function checkAccess() {
			global $_SERVER;
			global $_COOKIE;
			
			if (!empty($this->data['admin_password'])) {
				if (!strripos($_SERVER['REQUEST_URI'], 'enter') && !strripos($_SERVER['REQUEST_URI'], 'help')) {
					if (isset($_COOKIE['access'])) {
						$down = md5($this->data['admin_password']);
						$up = md5($_COOKIE['access']);
						
						if (!$down === $up) {
							header('Location: /enter.php');
							exit;
						}
					} else {
						header('Location: /enter.php');
						exit;
					}
				}
			}
		}
	}