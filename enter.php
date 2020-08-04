<?php 
	$server = getcwd() . '/';
	require 'template-parts/header.php';
?>

<form action="<?php echo $home ?>inc/actions.php" method="POST">
	<input type="hidden" name="page" value="enter">
	<input type="hidden" name="type" value="login">
	<label>Введите пароль:
		<input type="password" name="password">
	</label>
	
	<?php
		if (isset($_GET['result'], $_GET['notice'])) {
			echo '<p style="color: red;">' . str_replace('+', ' ', $_GET['notice']) . '</p>';
		}
	echo isset($notice) ? $notice : null ; 
	?>
	<input type="submit" value="Войти">
</form>

<p>На всякий случай оставляю <a href="<?php echo $pages ?>help.php">ссылку</a> на страницу помощи.</p>

<?php require 'template-parts/footer.php'; ?>