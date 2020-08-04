<?php
	namespace classes;
	
	use classes\config;
	use VK\Client\VKApiClient;
	
	class workWithApi {
		function __construct() {
			$config = new config();
			$this->vk = new VKApiClient('5.73');
			$this->token = $config->getTokenVk();
			$this->bot = $this->getThisProfile();
			$this->chat_action = [
				'chat_photo_update' => 'Фотография беседы обновлена.',
				'chat_photo_remove' => 'Фотография беседы удалена.',
				'chat_create' => 'Беседа создана.',
				'chat_title_update' => 'Название беседы обновлено.',
				'chat_invite_user' => 'Пользователь приглашен.',
				'chat_kick_user' => 'Пользователь исключен или вышшел из беседы.',
				'chat_pin_message' => 'Сообщение прикреплено.',
				'chat_unpin_message' => 'Сообщение откреплено.',
				'chat_invite_user_by_link' => 'Пользователь присоединился к беседе по ссылке.',
			];
			$this->list_attachments = [
				'photo' => 'Фотографии',
				'video' => 'Видео',
				'audio' => 'Аудиозаписи',
				'doc' => 'Документы',
				'link' => 'Ссылки',
				'market' => 'Товары',
				'market_album' => 'Товары',
				'wall' => 'Записи со стены',
				'wall_reply' => 'Комментарии',
				'sticker' => 'Стикер',
				'gift' => 'Подарок'
			];
		}
		
		public function getThisProfile() {
			return $this->vk->users()->get($this->token, ['fields' => ['domain', 'photo_50', 'photo_100']]);
		}
		
		public function geUnreadMessages() {
			$unread = $this->vk->messages()->getConversations($this->token, [
				'filter' => 'unread',
				'count' => 1,
				'extended' => 1
			]);
			
			if ($unread['count'] > 0) {
				$key = 0;
				
				foreach($unread['items'] as $item) {
					switch ($item['conversation']['peer']['type']) {
						case 'user':
							$k = array_search($item['conversation']['peer']['id'], array_column($unread['profiles'], 'id'));
							$unread['items'][$key]['conversation']['name'] = $unread['profiles'][$key]['first_name'] . ' ' . $unread['profiles'][$k]['last_name'];								
						break;	
						case 'chat':
							$unread['items'][$key]['conversation']['name'] = $item['conversation']['chat_settings']['title'];							
						break;	
						case 'group':
							$k = array_search($item['conversation']['peer']['id'], array_column($unread['groups'], 'id'));
							$unread['items'][$key]['conversation']['name'] = $unread['groups'][$k]['name'];								
						break;
					}
					
					$key++;
				}
			}
			
			return $unread['items'];
		}
		
		public function getLastDialogs() {
			$dialogs = $this->vk->messages()->getConversations($this->token, [
				'count' => 50,
				'extended' => 1,
				'fields' => ['photo_50', 'photo_100', 'photo_200']
			]);
			
			$messages = [];
			
			if ($dialogs['count'] > 0) {
				$key = 0;
				
				foreach ($dialogs['items'] as $item) {	
					$read = $item['conversation']['out_read'] - $item['conversation']['in_read'];
					
					$messages[$key]['ids']['id'] = $item['conversation']['peer']['id'];
					$messages[$key]['ids']['local_id'] = $item['conversation']['peer']['local_id'];
					
					switch ($item['conversation']['peer']['type']) {
						case 'user':
							$k = array_search($item['conversation']['peer']['local_id'], array_column($dialogs['profiles'], 'id'));
							$messages[$key]['conversation']['name'] = $dialogs['profiles'][$k]['first_name'] . ' ' . $dialogs['profiles'][$k]['last_name'];
							$photo = $dialogs['profiles'][$k];
						break;	
						case 'chat':
							$messages[$key]['conversation']['name'] = $item['conversation']['chat_settings']['title'];
							$photo = @ $item['conversation']['chat_settings']['photo'];
						break;	
						case 'group':
							$k = array_search($item['conversation']['peer']['local_id'], array_column($dialogs['groups'], 'id'));
							$messages[$key]['conversation']['name'] = $dialogs['groups'][$k]['name'];
							$photo = $dialogs['groups'][$k];
						break;
					}
					
					$messages[$key]['conversation']['type'] = $item['conversation']['peer']['type'];
					$messages[$key]['conversation']['photo']['photo_50'] = isset($photo['photo_50']) ? $photo['photo_50'] : 'https://vk.com/images/camera_50.png';
					$messages[$key]['conversation']['photo']['photo_100'] = isset($photo['photo_100']) ? $photo['photo_100'] : 'https://vk.com/images/camera_100.png';
					$messages[$key]['conversation']['photo']['photo_200'] = isset($photo['photo_200']) ? $photo['photo_200'] : 'https://vk.com/images/camera_200.png';
					
					$messages[$key]['last_message']['date'] = $item['last_message']['date'];
					$messages[$key]['last_message']['from_id'] = $item['last_message']['from_id'];
					$messages[$key]['last_message']['from'] = $item['last_message']['out'] ? $this->bot[0]['first_name'] . ': ' : '';;
					$messages[$key]['last_message']['text'] = $item['last_message']['text'];
					$messages[$key]['last_message']['read_state'] = $read == 0 ? false : true;
					
					if (isset($item['last_message']['action']['type'])) {
						$messages[$key]['last_message']['text'] = $this->chat_action[$item['last_message']['action']['type']];
					}
					
					$messages[$key]['last_message']['attachments'] = '';
					
					if (!empty($item['last_message']['attachments'])) {
						$attachments = [];
						
						foreach ($item['last_message']['attachments'] as $item) {
							$attachments[] = $this->list_attachments[$item['type']];
						}
						
						$attachments = array_unique($attachments);
						$messages[$key]['last_message']['attachments'] = 'Вложения: ' . implode(', ', $attachments);
					}
					
					$key++;
				}
			}
			
			return $messages;
		}
		
		public function getSomeDialog($id, $type) {
			$dialog = $this->vk->messages()->getHistory($this->token, [
				'peer_id' => $id,
				'count' => 50,
				'extended' => 1,
				'fields' => ['photo_50', 'photo_100', 'photo_200']
			]);
			
			if ($dialog['count'] > 0) {
				$key = 0;
				
				foreach ($dialog['items'] as $item) {
					switch ($type) {
						case 'user':
							$k = array_search($item['from_id'], array_column($dialog['profiles'], 'id'));
							$dialog['items'][$key]['name'] = $item['out'] ? $this->bot[0]['first_name'] : $dialog['profiles'][$k]['first_name'];
							$dialog['items'][$key]['photo'] = $item['out'] ? $this->bot[0]['photo_100'] : $dialog['profiles'][$k]['photo_100'];
						break;	
						case 'chat':
							$k = array_search($item['from_id'], array_column($dialog['profiles'], 'id'));
							$dialog['items'][$key]['name'] = $dialog['profiles'][$k]['first_name'];
							$dialog['items'][$key]['photo'] = $dialog['profiles'][$k]['photo_100'];
						break;	
						case 'group':
							$k = array_search($item['from_id'], array_column($dialog['groups'], 'id'));
							$dialog['items'][$key]['name'] = $item['from_id'] < 0 ? $dialog['groups'][$k]['name'] : $this->bot[0]['first_name'];
							$dialog['items'][$key]['photo'] = $item['from_id'] < 0 ? $dialog['groups'][$k]['photo_100'] : $this->bot[0]['photo_100'];
						break;
					}
					
					if (isset($item['action'])) {
						$dialog['items'][$key]['body'] = $this->chat_action[$item['action']];
					}					
					
					$key++;
				}
			}
			
			return array_reverse($dialog['items']);
		}
		
		public function messageSend($user_id, $message) {
			sleep(2);

			$this->vk->messages()->markAsRead($this->token, ['peer_id' => $user_id]);
			
			sleep(2);
			
			$this->vk->messages()->setActivity($this->token, [ 
				'user_id' => $user_id,
				'type' => 'typing'
			]);

			sleep(1);
			
			$this->vk->messages()->send($this->token, [
				'peer_id' => $user_id,
				'random_id' => rand(0, 999999999),
				'message' => $message
			]);
		}
	}
?>