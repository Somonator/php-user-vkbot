<?php
	$server = getcwd() . '/';
	require 'template-parts/header.php';
	
	$pagination = (isset($_GET['page']) && is_numeric($_GET['page'])) && $_GET['page'] > 1 ? $_GET['page'] * 50 : 0;
	$answers = $database->query('SELECT * FROM answers ORDER BY id DESC LIMIT ' . $pagination . ', 50') or die ($database->error);
	$answers_num = $database->query('SELECT COUNT(id) FROM answers') or die ($database->error);
	$answers_num = $answers_num->fetch_assoc();
	
	$action_import = $home . 'inc/actions.php';
?>

<div class="hide-forms">
	<div class="spoiler">
		<div class="spoiler-header">Добавить ответ</div>
		<form method="POST" action="<?php echo $action_import; ?>" class="spoiler-content add">
			<input type="hidden" name="page" value="answers">
			<input type="hidden" name="type" value="add">
			<label>Вопрос:
				<input type="text" name="question" required>
			</label>
			<label>Ответ:
				<input type="text" name="answer" required>
			</label>
			<label>Приоритет:
				<input type="number" min="1" max="5" name="priority" value="1" required>
			</label>
			<input type="submit" value="Добавить">
		</form>
	</div>
	<div class="spoiler">
		<div class="spoiler-header">Найти и заменить</div>
		<form method="POST" action="<?php echo $action_import; ?>" class="spoiler-content replace">
			<input type="hidden" name="page" value="answers">
			<input type="hidden" name="type" value="replace">		
			<label>Что нужно найти:
				<input type="text" name="find" required>
			</label>
			<label>На что нужно заменить:
				<input type="text" name="replace" required>
			</label>
			<input type="submit" value="Заменить">
		</form>
	</div>
	<div class="spoiler">
		<div class="spoiler-header">Изменить</div>
		<form method="POST" class="spoiler-content edit">
			<input type="hidden" name="page" value="answers">
			<input type="hidden" name="type" value="edit">
			<input type="hidden" name="id">			
			<label>Вопрос:
				<input type="text" name="question" required>
			</label>
			<label>Ответ:
				<input type="text" name="answer" required>
			</label>
			<label>Приоритет:
				<input type="number" min="1" max="5" name="priority" required>
			</label>
			<input type="submit" value="Заменить">
		</form>
	</div>
	<div class="spoiler" style="display: none;">
		<div class="spoiler-header">Удалить</div>
		<form method="POST" class="spoiler-content delete">
			<input type="hidden" name="page" value="answers">
			<input type="hidden" name="type" value="delete">		
			<input type="hidden" name="id">
			<input type="submit" value="Заменить">
		</form>
	</div>
	<div class="spoiler">
		<div class="spoiler-header">Импорт ответов из файла</div>
		<form method="POST" action="<?php echo $action_import ?>" action="<?php echo $action_import; ?>" enctype="multipart/form-data" class="spoiler-content">
			<input type="hidden" name="page" value="answers">
			<input type="hidden" name="type" value="import">		
			<label for="file">Выберите файл:
				<input type="file" name="file" accept=".txt,.bin" required>
			</label>
			<input type="submit" value="Импортировать">
		</form>
	</div>
	<div class="spoiler">
		<div class="spoiler-header">Полная очистка ответов</div>
		<form method="POST" action="<?php echo $action_import ?>" action="<?php echo $action_import; ?>" class="spoiler-content reset">
			<input type="hidden" name="page" value="answers">
			<input type="hidden" name="type" value="reset">
			<input type="submit" value="Очистить">
		</form>
	</div>	
</div>

<div class="manage-placeholder" style="display: none;">
	<div class="manage">
		<span class="edit">&#9998;</span>
		<span class="delete">&#10006;</span>
	</div>
</div>

<table class="answers" <?php echo !$answers_num['COUNT(id)'] > 0 ? 'style="display: none;"' : null ; ?>>
	<thead>
		<tr>
			<th>Вопрос</th>
			<th>Ответ</th>
			<th>Приоритет</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			if ($answers->num_rows > 0) {
				while ($row = $answers->fetch_assoc()) {
					echo '<tr id="' . $row['id'] . '">';
						echo '<td>' . $row['question'] . '</td>';
						echo '<td>' . $row['answer'] . '</td>';
						echo '<td>' . $row['priority'] . '</td>';
					echo '</tr>';
				} 
			}
		?>
	</tbody>
</table>

<?php 
	if (!$answers->num_rows > 0) {
		echo '<p class="no">Ответов не найдено.</p>';
	}
	
	
	if ($answers_num['COUNT(id)'] > 50) {
		echo '<h2>Страницы</h2>';
		echo '<select class="pagination">';
		
		for ($i = 1; $i <= $answers_num['COUNT(id)']/50; $i++) {
			$selected = isset($_GET['page']) && $_GET['page'] == $i ? 'selected' : null;
			
			echo '<option data-page="' . $i . '"' . $selected . '>Страница ' . $i . '</option>';
		}
		
		echo '</select>';
	}
	
	echo '<script>var ajax_url = "' . $action_import . '", max_num_pages = "' . round($answers_num['COUNT(id)'] / 50) . '";</script>';
	
	$answers->free();
	
	require 'template-parts/footer.php';
?>