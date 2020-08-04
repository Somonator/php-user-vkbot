<?php 
	$server = getcwd() . '/';
	require 'template-parts/header.php';
	
	$last_messages = $database->query('SELECT * FROM last_messages ORDER BY id DESC LIMIT 30') or die ($database->error);
	
	if ($last_messages->num_rows > 0) {
		echo '<div class="last-messages">';
		while ($row = $last_messages->fetch_assoc()) {
			echo '<div class="answer">';
				echo '<div class="name">' . $row['name'] . '</div>';
				echo '<div class="que">' . $row['down_message'] . '</div>';
				echo '<div class="ans">' . $row['up_message'] . '</div>';
			echo '</div>';
		}
		echo '</div>';
	} else {
		echo '<p class="no">Сообщений не найдено.</p>';
	}
	
	require 'template-parts/footer.php';
?>