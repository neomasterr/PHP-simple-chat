<?php
	include 'core/core.php';
	include 'core/config.php';
	include 'core/db_config.php';
	if (include 'core/sessions.php')
	{
		header('Location: index.php');
		exit;
	}

	if (isset($_GET['auth']))
	{
		$auth = strval($_GET['auth']);

		if (!preg_match('/^\w{32}$/', $auth)) {
			print_page('login', Array('message' => 'Please enter correct login'));
		}

		if (!($res = mysqli_query($db_link, 'SELECT `fastauth`.`id` as `fastauth_id`, `users`.`id`, `users`.`login` FROM `fastauth` LEFT JOIN `users` ON `users`.`id` = `fastauth`.`user` WHERE `key` = \''.$auth.'\' AND `available` > '.$_SERVER['REQUEST_TIME'].' LIMIT 1;'))) {
			print_page('login', Array('message' => 'Mysql error: '.mysqli_error($db_link)));
		}

		if (!($data = mysqli_fetch_assoc($res))) {
			print_page('login');
		}

		if (!mysqli_query($db_link, 'UPDATE `fastauth` SET `available` = 0 WHERE `id` = '.$data['fastauth_id'].' LIMIT 1;')) {
			// handle error
		}

		process_auth($db_link, $salt, $data, $_SERVER['REQUEST_TIME'] + $config['session']['time']);
	}
	else if (isset($_POST['login']) && isset($_POST['password']))
	{
		$login = trim(strval($_POST['login']));

		if (!preg_match('/^\w{5,16}$/', $login)) {
			print_page('login', Array('message' => 'Please enter correct login'));
		}

		$password = md5(trim(strval($_POST['password'])).$salt);

		if (!($res = mysqli_query($db_link, 'SELECT `id`, `login` FROM `users` WHERE `login` = \''.$login.'\' AND `password` = \''.$password.'\' LIMIT 1;'))) {
			print_page('login', Array('message' => 'Mysql error: '.mysqli_error($db_link), 'login' => $login));
		}

		if (!($data = mysqli_fetch_assoc($res))) {
			print_page('login', Array('message' => 'Login or password are incorrect', 'login' => $login));
		}

		process_auth($db_link, $salt, $data, $_SERVER['REQUEST_TIME'] + $config['session']['time']);
	}
	else
	{
		print_page('login');
	}

	function process_auth($db_link, $salt, $user, $expires)
	{
		$key = md5($user.$salt.$_SERVER['REQUEST_TIME']);

		if (!mysqli_query($db_link, 'INSERT INTO `sessions` (`user`, `v`, `expires`) VALUES('.$user['id'].', \''.$key.'\', '.$expires.');'))
		{
			print_page('login', Array('message' => 'Mysql error: '.mysqli_error($db_link)));
		}

		setcookie('v', $key, $expires);
		setcookie('login', $user['login'], $expires);
		header('Location: chat.php');
		exit;
	}