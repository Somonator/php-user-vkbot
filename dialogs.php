<?php
	$server = getcwd() . '/';
	require 'template-parts/header.php';
	
	if (!isset($_GET['dialog'], $_GET['type']) && empty($_GET['dialog']) && empty($_GET['type'])) {
		$dialogs = $workWithApi->getLastDialogs();
		
		echo '<div class="list-dialogs some-dialogs">';
			if (!empty($dialogs)) {
				foreach($dialogs as $item) {
					$unread = $item['last_message']['read_state'] ? 'unread' : '';
					$br = !empty($item['last_message']['text']) ? '<br>' : '';
					
					echo '<div class="dialog ' . $unread . '">';
						echo '<div class="avatar"><img src="' . $item['conversation']['photo']['photo_100'] . '" alt=""></div>';
						echo '<div class="data">';
							echo '<div class="name">' . $item['conversation']['name'] . '</div>';
							echo '<p class="message">' . $item['last_message']['from'] . $item['last_message']['text'] . $br . $item['last_message']['attachments'] . '</p>';
						echo '</div>';
						echo '<a href="?dialog=' . $item['ids']['id'] . '&type=' . $item['conversation']['type'] . '" class="go">></a>';
					echo '</div>';
				}
			} else {
				echo '<p class="no">Диалогов нет.</p>';
			}
		echo '</div>';
	} else {
		$dialog = $workWithApi->getSomeDialog($_GET['dialog'], $_GET['type']);
		
		echo '<div class="list-dialogs some-dialog">';
			echo '<a href="dialogs.php" class="go-back">Назад к диалогам</a>';
			if (!empty($dialog)) {
				foreach($dialog as $item) {
					$unread = @ !$item['read_state'] ? 'unread' : '';
					
					echo '<div class="dialog ' . $unread . '">';
						echo '<div class="avatar"><img src="' . $item['photo'] . '" alt=""></div>';
						echo '<div class="data">';
							echo '<div class="name">' . $item['name'] . '</div>';
							echo '<p class="message">' . $item['body'] . '</p>';
							if (!empty($item['attachments'])) {
								echo '<div class="list-attachments">';
								foreach ($item['attachments'] as $item) {
									switch ($item['type']) {
										case 'photo':
											echo '<div class="item photo">';
												echo '<img src="' . $item['photo']['photo_130'] . '" alt="">';
											echo '</div>';
										break;
										case 'video':
											echo '<div class="item video" style="background-image: url(' . $item['video']['photo_320'] . ');">';
												echo '<div class="type">Видео</div>';
												echo '<div class="title">' . $item['video']['title'] . '</div>';
											echo '</div>';
										break;
										case 'audio':
											echo '<div class="item audio">';
												echo '<div class="type">Аудиозапись</div>';
												echo '<div class="title">' . $item['audio']['artist'] . ' - ' . $item['audio']['title'] . '</div>';
											echo '</div>';
										break;
										case 'doc':
											$bg = $item['doc']['photo'] ? 'style="background-image: url(' . $item['doc']['photo']['photo_130'] . ');"' : '';
											echo '<div class="item doc" ' . $bg . '>';
												echo '<div class="type">Документ</div>';
												echo '<div class="title">' . $item['doc']['title'] . '</div>';
												echo '<a href="' . $item['doc']['url'] . '" class="go">Перейти</a>';
											echo '</div>';
										break;
										case 'link':
											$bg = $item['link']['photo'] ? 'style="background-image: url(' . $item['link']['photo']['photo_130'] . ');"' : '';
											echo '<div class="item link" ' . $bg  . '>';
												echo '<div class="type">Ссылка</div>';
												echo '<div class="title">' . $item['link']['title'] . '</div>';
												echo '<a href="' . $item['link']['url'] . '" class="go">Перейти</a>';
											echo '</div>';
										break;
										case 'market':
											$bg = $item['market']['thumb_photo'] ? 'style="background-image: url(' . $item['market']['thumb_photo'] . ');"' : '';
											echo '<div class="item market" ' . $bg . '>';
												echo '<div class="type">Товар</div>';
												echo '<div class="title">' . $item['market']['title'] . '</div>';
												echo '<div class="price">' . $item['market']['price']['amount'] / 100 . ' ' . $item['market']['price']['currency']['name'] . '</div>';
											echo '</div>';
										break;
										case 'market_album':
											$bg = $item['market_album']['photo'] ? 'style="background-image: url(' . $item['market_album']['photo']['photo_130'] . ');"' : '';
											echo '<div class="item market_album" ' . $bg . '>';
												echo '<div class="type">Подборка товаров</div>';
												echo '<div class="title">' . $item['market_album']['title'] . '</div>';
												echo '<div class="count">' . $item['market_album']['count'] . ' товаров</div>';
											echo '</div>';
										break;
										case 'wall':
											echo '<div class="item wall">';
												echo '<div class="type">Запись со стены</div>';
												echo '<div class="text">' . $item['wall']['text'] . '</div>';
											echo '</div>';
										break;
										case 'wall_reply':
											echo '<div class="item wall_reply">';
												echo '<div class="type">Комментарий на стене</div>';
												echo '<div class="text">' . $item['wall_reply']['text'] . '</div>';
											echo '</div>';
										break;
										case 'sticker':
											echo '<div class="item sticker">';
												echo '<img src="' . $item['sticker']['photo_128'] . '" alt="">';
											echo '</div>';
										break;
										case 'gift':
											echo '<div class="item gift">';
												echo '<img src="' . $item['gift']['thumb_256'] . '" alt="">';
											echo '</div>';
										break;
									}
								}
								echo '</div>';
							}
						echo '</div>';
					echo '</div>';
				}
			} else {
				echo '<p class="no">Диалог пустой.</p>';
			}
			echo '<a href="dialogs.php" class="go-back">Назад к диалогам</a>';
		echo '</div>';
	}

	require 'template-parts/footer.php';
?>