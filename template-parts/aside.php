<aside class="widgets">
	<section class="widget widget-menu">
		<nav class="main-menu">
			<ul class="menu">
				<li class="menu-item <?php echo $page['menu'] == 'home' ? 'active' : null ?>">
					<a href="<?php echo $home ?>">Главная</a>
				</li>
				<li class="menu-item <?php echo $page['menu'] == 'dialogs' ? 'active' : null ?>">
					<a href="<?php echo $home ?>dialogs.php">Диалоги</a>
				</li>
				<li class="menu-item <?php echo $page['menu'] == 'answers' ? 'active' : null ?>">
					<a href="<?php echo $home ?>answers.php">Ответы</a>
				</li>
				<li class="menu-item <?php echo $page['menu'] == 'admins' ? 'active' : null ?>">
					<a href="<?php echo $home ?>admins.php">Администраторы</a>
				</li>
				<li class="menu-item <?php echo $page['menu'] == 'help' ? 'active' : null ?>">
					<a href="<?php echo $home ?>help.php">Помощь</a>
				</li>
			</ul>
		</nav>
	</section>
</aside>