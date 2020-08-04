<?php
	namespace classes;
	
	use classes\config;
	use classes\database;
	use classes\tools;

	class actions {
		public $in_json = ['result' => null, 'notice' => null, 'data' => null];
		
		function __construct($input) {
			$database = new database();
			$this->database = $database->connect;
			$this->tools = new tools();
			$this->data = $input;
		} 

		function checkIssetPOST() {
			if (!isset($this->data) || empty($this->data)) {
				$this->in_json['result'] = 'error';
				$this->in_json['notice'] = 'Никаких данных для запроса не передано.';
				$result = json_encode($this->in_json);
			} else if (!isset($this->data['page']) || empty($this->data['page'])) {
				$this->in_json['result'] = 'error';
				$this->in_json['notice'] = 'Страница запроса не указана.';
				$result = json_encode($this->in_json);
			} else if (!isset($this->data['type']) || empty($this->data['type'])) {
				$this->in_json['result'] = 'error';
				$this->in_json['notice'] = 'Тип запроса не указан.';
				$result = json_encode($this->in_json);
			} else {
				$result = true;
			}
			
			return $result;
		}
		
		function checkIssetVars(array $vars) {
			$params = [];
			
			foreach ($vars as $param) {
				if (isset($this->data[$param]) && !empty($this->data[$param])) {
					$params[$param] = addslashes($this->data[$param]);
				} else {
					$this->json('error', 'Параметр ' . $param . ' не передан.', null);
					
					break;
				}
			}
			
			return $params;
		}
		
		function end($type, $notice, $params = null) {
			if ($this->database->error) {
				$this->in_json['result'] = 'error';
				$this->in_json['notice'] = $this->database->error;
			} else {
				$this->in_json['result'] = $type;
				$this->in_json['notice'] = $notice;
				$this->in_json['data'] = $params;
			}
		}

		function json(string $type, string $notice, array $params = null) {
			$this->end($type, $notice, $params);
			
			echo json_encode($this->in_json);
			exit;
		}

		function redirect(string $to, string $type, string $notice, array $params = null) {
			$this->end($type, $notice, $params);
			
			header('Location: ' . $this->tools->getHomeUrl() . $to . '?' . http_build_query($this->in_json));
			exit;
		}
	}
	
	class enter extends actions {
		public $url = 'enter.php';
		
		public function login() {
			$config = new config();
			$password = $config->getAccessPassword();
			
			if (isset($password) && $password === $this->data['password']) {
				$access = md5($this->data['password']);
				
				setcookie('access', $access, time() + 60 * 60 * 24 * 7, '/');
				
				header('Location: /');
				exit;
			} else {
				$this->redirect($this->url, 'error', 'Неправильный пароль.', null);
			}
		}
	}

	class answers extends actions {
		public $url = 'answers.php';
		
		public function add() {
			$params = $this->checkIssetVars(['question', 'answer', 'priority']);
			$query = $this->database->query('INSERT INTO answers (question, answer, priority) VALUES ("' . $params['question'] . '", "' . $params['answer'] . '", "' . $params['priority'] . '")');
			
			$this->redirect($this->url, 'success', 'Данные успешно добавлены.', null);
		}
		
		public function replace() {
			$params = $this->checkIssetVars(['find', 'replace']);
			$answers_num = $this->database->query('SELECT COUNT(id) FROM answers');
			
			if (!$answers_num) {
				$this->redirect($this->url, 'error', 'Ошибка запроса.', null);
			}
			
			$answers_num = $answers_num->fetch_assoc();
			
			if ($answers_num['COUNT(id)'] > 0) {
				$this->database->query('UPDATE answers SET question = REPLACE(question, "' . $params['find'] . '", "' . $params['replace'] . '"), answer = REPLACE(answer, "' . $params['find'] . '", "' . $params['replace'] . '")');
				$this->redirect($this->url, 'success', 'Строки успешно заменены. Для просмотра обновите страницу.', $params);
			} else {
				$this->redirect($this->url, 'error', 'А ответов-то нет.', null);
			}
		}		
		
		public function edit() {
			$params = $this->checkIssetVars(['id', 'question', 'answer', 'priority']);
			$this->database->query('UPDATE answers SET question = "' . $params['question'] . '", answer = "' . $params['answer'] . '", priority = "' . $params['priority'] . '" WHERE id =' . $params['id']);
			
			$this->json('success', 'Данные успешно изменены.', $params);
		}
		
		public function delete() {
			$params = $this->checkIssetVars(['id']);
			$this->database->query('DELETE FROM answers WHERE id =' . $params['id']);
			
			$this->json('success', 'Данные успешно удалены.', $params);
		}
		
		public function import() {
			global $_FILES;
			
			if ($_FILES['file']['error'] == 0) {
				$format = pathinfo($_FILES['file']['name']);
				
				if ($format['extension'] == 'txt' || $format['extension'] == 'bin') {
					$file = file($_FILES['file']['tmp_name']);
					$sql = 'INSERT INTO answers (question, answer, priority) VALUES ';
					
					foreach($file as $answer_item) {
						$item = explode('\\', $answer_item); // симвом "\" + закомментаирование
						$item[0] = addslashes(isset($item[0]) ? $item[0] : 'Ну шо тут сказати? Слава Україні.');
						$item[1] = addslashes(isset($item[1]) ? $item[1] : 'Героям слава.');
						$item[2] = addslashes(isset($item[2]) && $item[2] > 0 && $item[2] <= 5 ? $item[1] : 1);
						$sql .= '("' . $item[0] . '", "' . $item[1] . '", "' . $item[2] . '"), ';
					}
					
					$sql = substr($sql, 0, -2);
					
					$this->database->query($sql);
					$this->redirect($this->url, 'success', 'Новый пак успешно установлен.', null);
				} else {
					$this->redirect($this->url, 'error', 'Неправильный формат файла.', null);
				}
			} else {
				$this->redirect($this->url, 'error', 'При загрузке файла происзошла ошибка.', null);
			}
		}
		
		public function reset() {
			$query = $this->database->query('TRUNCATE TABLE answers');
			
			$this->redirect($this->url, 'success', 'Все данные успешно удалены.', null);
		}
	}
?>