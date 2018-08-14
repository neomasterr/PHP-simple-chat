<?php
	ini_set('display_errors', 'On');
	
	if (!($db_link = mysqli_connect('localhost', 'neoma158_simplechat', 'wxIZdZ4M', 'neoma158_simplechat')))
	{
		echo 'Mysql error: ',mysqli_error($db_link);
		exit;
	}

	mysqli_query($db_link, 'SET NAMES utf8');
	mysqli_select_db($db_link, 'neoma158_simplechat');

	$salt = 'SimpleChat';