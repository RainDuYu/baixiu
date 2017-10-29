<?php 
	session_start();
	//把session清掉就可以退出登录
	unset($_SESSION['user_info']);
	header('Location: /admin/login.php');
 ?>