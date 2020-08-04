<?php
	require '../vendor/autoload.php';
	
	$database = new classes\database();
	$database = $database->connect;
	
	$workWithApi = new classes\workWithApi();
	$answers = new classes\selectionAnswer();
	$time = 0;
	
	while ($time <= 250) {
		$unread = $workWithApi->geUnreadMessages();

		if (!empty($unread)) {
			foreach($unread as $item) {
				if ($item['conversation']['can_write']['allowed'] == 1) {
					$id = $item['conversation']['peer']['id'];
					$text = $item['last_message']['text'];
					$answer = $answers->answer($text);
					
					$name = addslashes($item['conversation']['name']);
					$text_db = addslashes(!empty($text) ? $text : 'Сообщение с вложениями.');
					$answer_db = addslashes($answer);
					
					$workWithApi->messageSend($id, $answer);
					
					$database->query('INSERT INTO last_messages (name, down_message, up_message) VALUES ("' .  $name . '", "' . $text_db . '", "' . $answer_db . '")');
					
					$time = $time + 5;
				}
			}
		}
		
		sleep(5);
		
		$time = $time + 5;
	}
	
	echo 'Тестовый режим.';
?>