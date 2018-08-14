<?php
	if (!isset($_GET['act'])) {
		echo '{"error":"Wrong action"}';
		exit;
	}

	$act = strval($_GET['act']);
	$acts = Array('send', 'get');

	if (!in_array($act, $acts)) {
		echo '{"error":"Wrong action"}';
		exit;
	}

	include 'core/config.php';
	include 'core/db_config.php';

	if (!(include 'core/sessions.php')) {
		echo '{"error":"Please log in"}';
		exit;
	}

	switch ($act) {
		case 'send':
			if (!isset($_POST['message'])) {
				echo '{"error":"Empty message"}';
				exit;
			}
			$message = mb_substr(strval($_POST['message']), 0, $config['message']['maxlength']);
			if (!mysqli_query($db_link, 'INSERT INTO `messages` (`user`, `message`, `timestamp`) VALUES('.$user['id'].', \''.mysqli_real_escape_string($db_link, $message).'\', '.$_SERVER['REQUEST_TIME'].')'))
			{
				echo json_encode(Array('error' => 'Mysql error: '.mysqli_error($db_link)));
				exit;
			}

			echo '{"success":'.mysqli_insert_id($db_link).'}';
		break;		
		case 'get':
			$from = intval($_GET['from']);

			if (!($res = mysqli_query($db_link, 'SELECT `messages`.`id`, `login`, `timestamp`, `message` FROM `messages` LEFT JOIN `users` ON `users`.`id` = `messages`.`user` WHERE `messages`.`id` > '.$from.' ORDER BY `id` DESC LIMIT 20;')))
			{
				echo json_encode(Array('error' => 'Mysql error: '.mysqli_error($db_link)));
				exit;
			}

			$messages = Array();

			while ($message = mysqli_fetch_assoc($res))
			{
				$messages[] = $message;
			}

			echo json_encode(Array('messages' => $messages));
		break;
	}