<?php
	if (!isset($_COOKIE['v']) || !preg_match('/^\w{32}$/', $_COOKIE['v'])) return false;
	
	if (!($res = mysqli_query($db_link, 'SELECT `id`, `user` FROM `sessions` WHERE `v`=\''.$_COOKIE['v'].'\' AND `expires`>'.$_SERVER['REQUEST_TIME'].' LIMIT 1;')))
	{
		echo 'Mysql error: ',mysqli_error($db_link);
		return false;
	}

	if ($row = mysqli_fetch_assoc($res))
	{
		$user = Array('id' => $row['user']);
		return true;
	}
	else
	{
		unset($_COOKIE['v']);
		return false;
	}