<?php
	require '../vendor/autoload.php';
	
	$actions = new classes\actions($_POST);
	$tools = new classes\tools();

	if ($actions->checkIssetPOST() === true) {
		$page = 'classes\\' . $actions->data['page'];
		$name = get_class($actions);
		
		if (is_subclass_of($page, $name)) {
			$class = new $page($_POST);
			$method = $actions->data['type'];
			
			if ($class->$method()) {
				$result = $class->$method();
			} else {
				//Такого типа запроса не существует.
				http_response_code(500);			
			}
		} else {
			//Такой страницы для запроса не существует.
			http_response_code(500);
		}		
	} else {
		//Нечего не передано или страница либо тип запроса не указаны.
		http_response_code(500);
	}

	echo $result;