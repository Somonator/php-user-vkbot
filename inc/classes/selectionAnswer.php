<?php
	namespace classes;
	
	use classes\database;
	
	class selectionAnswer {
		function __construct() {
			$database = new database();
			$this->database = $database->connect;
		}
	
		public function answer($text) {
			if (preg_match('/!поиск/ui', $text)) {
				$ans = '!поиск';
			} else if(preg_match('/^инфа,/ui', $text)) {
				$ans = 'Инфа ' . rand(1, 100) . '%';
			} else if (preg_match('/или/ui', $text)) {
				$items = preg_split('/или/ui', $text, -1, PREG_SPLIT_NO_EMPTY);
				$rand = rand(0, count($items) - 1);
				$ans = $items[$rand];
			} else {
				$ans = $this->choiceAnswerFromDB($text);
			}
			
			return $ans;
		}
		
		public function choiceAnswerFromDB($text) {
			$query = $this->database->query('SELECT * FROM answers') or die ($this->database->error);
			
			if ($query->num_rows > 0) {
				$array_answers = [];
				$new_array = [];
				
				while ($row = $query->fetch_assoc()) {
					$item = [
						'answer' => $row['answer'],
						'priority' => $row['priority']
					];
					
					$array_answers[$row['question']] = $item;
				}
				
				foreach ($array_answers as $key => $item) {
					similar_text($key, $text, $p);
					
					if ($p > 80) {
						$priority = $item['priority'] == 0 ? 1 : $item['priority'];
						
						for ($i = 1;$i <= $priority; $i++) {
							$new_array[] = $item['answer'];
						}
					}
				}
				
				if (!empty($new_array)) {					
					$rand = rand(0, count($new_array) - 1);
					$answer = $new_array[$rand];				
				} else {
					$query = $this->database->query('SELECT answer FROM answers ORDER BY RAND() LIMIT 1') or die ($this->database->error);
					$random = $query->fetch_assoc();
					$answer = $random['answer'];					
				}
			} else {
				$answers = 'Кое-кто забыл добавить ответы для меня.';
			}
			
			return $answer;
		}	
	}
?>	