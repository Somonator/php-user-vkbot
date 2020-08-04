<?php
	namespace classes;

	use classes\config;

	class tools {
		function __construct() {
			$config = new config;
			$this->install_path = $config->getPath();
		}

		public function getServerPath() {
			global $_SERVER;
			
			return $_SERVER['DOCUMENT_ROOT'] . '/' . $this->install_path . '/';
		}
		
		public function getHomeUrl() {
			global $_SERVER;
			
			$protocol = '//';
			$this->install_path .= !empty($this->install_path) ? '/' : '';
			$url = $protocol . $_SERVER['SERVER_NAME'] . '/' . $this->install_path;
			
			return $url;
		}
		
		public function isPage() {
			global $_SERVER;
			
			$home = $this->getHomeUrl();
			$add = $home . 'src/';
			
			if (strripos($_SERVER['REQUEST_URI'], 'enter')) {
				$page['title'] = 'Вход';
				$page['script'] = '';
				$page['menu'] = '';
			} else if (strripos($_SERVER['REQUEST_URI'], 'dialogs')) {
				$page['title'] = 'Диалоги';
				$page['script'] = '';
				$page['menu'] = 'dialogs';
			} else if (strripos($_SERVER['REQUEST_URI'], 'answers')) {
				$page['title'] = 'База ответов';
				$page['script'] = '<script src="' . $add . 'js/answers.js"></script>';
				$page['menu'] = 'answers';
			} else if (strripos($_SERVER['REQUEST_URI'], 'admins')) {
				$page['title'] = 'Администраторы';
				$page['script'] = '';
				$page['menu'] = 'admins';
			} else if (strripos($_SERVER['REQUEST_URI'], 'help')) {
				$page['title'] = 'Помощь';
				$page['script'] = '';
				$page['menu'] = 'help';
			} else {
				$page['title'] = 'Главная';
				$page['script'] = '';
				$page['menu'] = 'home';
			}
			
			return $page;
		}		
	}
?>