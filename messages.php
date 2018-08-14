<?php
	if (!isset($_GET['act'])) {
		echo '{"error":"Wrong action"}';
		exit;
	}

	$act = strval($_GET['act']);
	$acts = Array('get');

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