<?php
	include 'core/config.php';
	include 'core/core.php';
	include 'core/db_config.php';

	if (!(include 'core/sessions.php'))
	{
		header('Location: login.php');
		exit;
	}

	if (isset($_GET['logout']))
	{
		if (isset($_SERVER['HTTP_COOKIE']))
		{
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				setcookie($name, '', $_SERVER['REQUEST_TIME']-1000);
				setcookie($name, '', $_SERVER['REQUEST_TIME']-1000, '/');
			}
		}
		header('Location: index.php');
		exit;
	}

	print_page('chat', Array('limit' => $config['message']['limit']));