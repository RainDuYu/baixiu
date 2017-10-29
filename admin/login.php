<?php

	// login.php 程序需要承担两个功能
	// a) 以 get 方式发起的请求，展示页面
	// b) 以 post 方式发起的请求，处理登录逻辑
	// 只有 post 时才执行
	// if(empty($_POST)) {
		// 没有以 post 方式提或者 没有设置 name 属性
	// }
	require '../functions.php';
	$message='';
	if(!empty($_POST)) {
		// 肯定是以 post 方式提交的
		$email = $_POST['email']; // 用户以 post 方式提交的邮箱
		$password = $_POST['password']; 
		// 用户以 post 方式提交的密码
		//验证登陆需要先查一查有没有对应用户
		// 如果有对应用户，再查询密码是否匹配
		$rows=query('SELECT * FROM users WHERE email="' . $email . '"');
		// 取出资源中的结果
		if($rows[0]) {
			if($rows[0]['password'] == $password) {
				//     '/'~~http://域名或ip/
				//      '/'表示根目录
				//通过会话cookie记录登录状态
				//浏览器再次发起请求时可以判断这个状态
				//存一个session，服务器会自动设置一个响应头Set-Cookie给浏览器，浏览器在本地存一个cookie,这个cookie默认叫PHPSESSID	
				session_start();
				$_SESSION['user_info']=$rows[0];
				header('Location:  /admin');
				exit;
			} else {
				$message='用户名或密码错误';
			}
		} else {
			// echo '登录失败！';
			$message='邮箱不存在';
		}
	}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>Sign in &laquo; Admin</title>
	<?php include './inc/style.php'; ?>
	<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
	<div class="login">
		<form action="./login.php" method="post" class="login-wrap">
			<img class="avatar" src="../assets/img/default.png">
			<!-- 有错误信息时展示 -->
			<?php if(!empty($message)){ ?>
			<div class="alert alert-danger">
				<strong>错误！</strong> 
				<?php echo $message; ?>
			</div>
			<?php } ?>
			<div class="form-group">
				<label for="email" class="sr-only">邮箱</label>
				<input id="email" type="email" name="email" class="form-control" placeholder="邮箱" autofocus>
			</div>
			<div class="form-group">
				<label for="password" class="sr-only">密码</label>
				<input id="password" name="password" type="password" class="form-control" placeholder="密码">
			</div>
			<input class="btn btn-primary btn-block" type="submit" value="登 录">
		</form>
	</div>
</body>
</html>
