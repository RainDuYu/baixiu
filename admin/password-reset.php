<?php 
	require '../functions.php';
	//检测登录
	checkLogin();
	$message='';
	$user_id=$_SESSION['user_info']['id'];
	$cogs = array('password-reset');
	$active = '';
	if(!empty($_POST)){
		//获取用户输入的密码
		$password=$_POST['old'];
		// 获取数据库中的密码
		$sql='SELECT * FROM users WHERE id=' . $user_id;
		$rows=query($sql);
			if($rows[0]['password']==$password){
				$_SESSION['user_info']=$rows[0];
				unset($_POST['old']);
				$result=update('users',$_POST,$user_id);
				if($result){
					header('Location:  /admin/login.php');
					exit;
				}
			}else{
				$message='密码输入错误';
			}
	}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>Password reset &laquo; Admin</title>
	<?php include'./inc/style.php';  ?>
	<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
	<div class="main">
	 <?php include'./inc/nav.php';  ?>
		<div class="container-fluid">
			<div class="page-title">
				<h1>修改密码</h1>
			</div>
			<!-- 有错误信息时展示 -->
			<?php if(!empty($message)){ ?>
			<div class="alert alert-danger">
				<strong>错误！</strong>
				<?php echo $message; ?>
			</div>
			<?php } ?>
			<form action="./password-reset.php" method="post" class="form-horizontal" >
				<div class="form-group">
					<label for="old" class="col-sm-3 control-label">旧密码</label>
					<div class="col-sm-7">
						<input id="old" name="old" class="form-control" type="password" placeholder="旧密码">
					</div>
				</div>
				<div class="form-group">
					<label for="password" class="col-sm-3 control-label">新密码</label>
					<div class="col-sm-7">
						<input id="password" name="password" class="form-control" type="password" placeholder="新密码">
					</div>
				</div>
				<div class="form-group">
					<label for="confirm" class="col-sm-3 control-label">确认新密码</label>
					<div class="col-sm-7">
						<input id="confirm" class="form-control" type="password" placeholder="确认新密码">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-7">
						<button type="submit" class="btn btn-primary">修改密码</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php include'./inc/aside.php';  ?>
	<?php include'./inc/script.php'; ?>
</body>
</html>
