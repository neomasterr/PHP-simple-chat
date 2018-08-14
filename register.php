<?php
	include 'core/core.php';
	include 'core/config.php';
	include 'core/db_config.php';
	if ((include 'core/sessions.php'))
	{
		header('Location: index.php');
		exit;
	}

	if (isset($_POST['login']) && isset($_POST['password']))
	{
		$login = trim(strval($_POST['login']));
		$password = trim(strval($_POST['password']));

		if (strlen($password) < 6)	{
			print_page('register', Array('message' => 'Password must be not lower than 6 characters', 'login' => $login));
		}

		if (!preg_match('/^\w{5,20}$/', $login)) {
			print_page('register', Array('message' => 'Incorrect login, please use only word characters (A-z, 0-9, _) with total length from 5 to 20 characters', 'login' => $login));
		}

		if (!($res = mysqli_query($db_link, 'SELECT `id` FROM `users` WHERE `login` = \''.$login.'\' LIMIT 1;'))) {
			print_page('register', Array('message' => 'Mysql error: '.mysqli_error($db_link), 'login' => $login));
		}

		if (mysqli_num_rows($res)) {
			print_page('register', Array('message' => 'Incorrect login, user already exists', 'login' => $login));
		}

		if (!mysqli_query($db_link, 'INSERT INTO `users` (`login`, `password`, `lastactivity`) VALUES(\''.$login.'\', \''.md5($password.$salt).'\', '.$_SERVER['REQUEST_TIME'].');')) {
			print_page('register', Array('message' => 'Mysql error: '.mysqli_error($db_link), 'login' => $login));
		}

		$userid = mysqli_insert_id($db_link);
		$key = md5($login.$_SERVER['REQUEST_TIME'].$salt);

		if (!mysqli_query($db_link, 'INSERT INTO `fastauth` (`user`, `key`, `available`) VALUES('.$userid.', \''.$key.'\', '.($_SERVER['REQUEST_TIME'] + $config['fastauth']['available']).') ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);'))
		{
			// handle error
			header('Location: login.php');
			exit;
		}

		header('Location: login.php?auth='.$key);
		exit;
	}
	else
	{
		print_page('register');
	}