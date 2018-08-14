<?php
	function print_page($page = 'index', $data = Array())
	{
		include('assets/html/'.$page.'.html');
		exit;
	}