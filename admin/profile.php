<?php 
	//登录时将用户信息存进了session
	//用户的信息随时可能会发生改变(例如被管理员给注销)
	//所以需要重新获取一次,可以将用户的id从session中取出，利用id获取
	require '../functions.php';
	//登录检测
	checkLogin();
	//从session中获取id
	$user_id=$_SESSION['user_info']['id'];
	//根据id查询用户
	$rows=query('SELECT * FROM users WHERE id=' . $user_id );
	//以post方式提交的表单
	if(!empty($_POST)){
		//释放变量,不允许更改邮箱
		unset($_POST['email']);
		$result=update('users',$_POST,$user_id);
		//更新成功,刷新页面
		if($result){
			header('Location:/admin/profile.php');
			exit;
		}
		$message='更新失败';
	}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>Dashboard &laquo; Admin</title>
	<?php include'./inc/style.php'; ?>
	<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
	<div class="main">
	 <?php include'./inc/nav.php'; ?>
		<div class="container-fluid">
			<div class="page-title">
				<h1>我的个人资料</h1>
			</div>
			<?php if(isset($message)) {?>
			<div class="alert alert-danger">
				<strong>错误！</strong>
				<?php echo $message; ?>
			</div>
			<?php } ?>
			<!-- action 不写默认是当前页面-->
			<form action='/admin/profile.php' method='post' class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-3 control-label">头像</label>
					<div class="col-sm-6">
						<label class="form-image">
							<input id="avatar" type="file">
							<!-- 上传头像,上传过头像则显示上传头像 -->
							<?php if($rows[0]['avatar']){ ?>
							<img class='preview' src="<?php echo $rows[0]['avatar']; ?>">
							<?php }else{ ?>
							<!-- 没有上传过头像，则显示默认头像 -->
							<img class='preview' src="/assets/img/default.png">
							<?php } ?>
							<i class="mask fa fa-upload"></i>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="email" class="col-sm-3 control-label">邮箱</label>
					<div class="col-sm-6">
						<input id="email" class="form-control" type="type" name="email" value="<?php echo $rows[0]['email']; ?>" placeholder="邮箱" disabled>
						<p class="help-block">登录邮箱不允许修改</p>
					</div>
				</div>
				<div class="form-group">
					<label for="slug" class="col-sm-3 control-label">别名</label>
					<div class="col-sm-6">
						<input id="slug" class="form-control" name="slug" type="type" value="<?php echo $rows[0]['slug'] ?>" placeholder="slug">
						<p class="help-block">https://zce.me/author/<strong>zce</strong></p>
					</div>
				</div>
				<div class="form-group">
					<label for="nickname" class="col-sm-3 control-label">昵称</label>
					<div class="col-sm-6">
						<input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo $rows[0]['nickname'] ?>" placeholder="昵称">
						<p class="help-block">限制在 2-16 个字符</p>
					</div>
				</div>
				<div class="form-group">
					<label for="bio" class="col-sm-3 control-label">简介</label>
					<div class="col-sm-6">
						<textarea id="bio" name='bio' class="form-control" placeholder="Bio" cols="30" rows="6"><?php echo $rows[0]['bio'] ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-6">
						<button type="submit" class="btn btn-primary">更新</button>
						<a class="btn btn-link" href="password-reset.php">修改密码</a>
					</div>
				</div>
			</form>
		</div>
	</div>
		<div class="aside">
		<div class="profile">
			<?php if(empty($_SESSION['user_info']['avatar'])) { ?>
			<img class="avatar" src="/assets/img/default.png">
			<?php } else { ?>
			<img class="avatar" src="<?php echo $_SESSION['user_info']['avatar']; ?>">
			<?php } ?>
			<h3 class="name"><?php echo $_SESSION['user_info']['nickname']; ?></h3>
		</div>
		<ul class="nav">
			<?php if(isset($active)){ ?>
			<li <?php if($active == 'dashboard') { ?> class="active" <?php } ?>>
				<a href="/admin"><i class="fa fa-dashboard"></i>仪表盘</a>
			</li>
			<?php } else{?>
			<li class="active">
				<a href="/admin"><i class="fa fa-dashboard"></i>仪表盘</a>
			</li>
			<?php } ?>
			<?php if(isset($actives)){ ?>
			<li <?php if(in_array($active, $actives)) { ?> class="active" <?php } ?>>
				<a href="#menu-posts" class="collapsed" data-toggle="collapse">
					<i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
				</a>
				<ul id="menu-posts" class="collapse <?php if(in_array($active, $actives)) { ?> in <?php } ?>">
					<li <?php if($active == 'posts') { ?> class="active" <?php } ?>><a href="/admin/posts.php">所有文章</a></li>
					<li <?php if($active == 'post') { ?> class="active" <?php } ?>><a href="/admin/post.php">写文章</a></li>
					<li <?php if($active == 'category') { ?> class="active" <?php } ?>><a href="/admin/categories.php">分类目录</a></li>
				</ul>
			</li>
			<?php }else{ ?>
					<li>
				<a href="#menu-posts" class="collapsed" data-toggle="collapse">
					<i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
				</a>
				<ul id="menu-posts" class="collapse">
					<li><a href="/admin/posts.php">所有文章</a></li>
					<li><a href="/admin/post.php">写文章</a></li>
					<li><a href="/admin/categories.php">分类目录</a></li>
				</ul>
			</li>
			<?php } ?>
			<li>
				<a href="comments.php"><i class="fa fa-comments"></i>评论</a>
			</li>
			<?php if(isset($active)){ ?>
			<li <?php if($active == 'users') { ?> class="active" <?php } ?>>
				<a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
			</li>
			<?php }else{ ?>
			<li class="active">
				<a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
			</li>
			<?php } ?>
			<?php if(isset($active)){ ?>
			<li <?php if(in_array($active,$cogs)) { ?> class="active" <?php } ?> >
				<a href="#menu-settings" class="collapsed" data-toggle="collapse">
					<i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
				</a>
				<ul id="menu-settings" class="collapse <?php if(in_array($active, $cogs)) { ?> in <?php } ?>">
					<li <?php if($active == 'menus') { ?> class="active" <?php } ?>><a href="menus.php">导航菜单</a></li>
					<li <?php if($active == 'slides') { ?> class="active" <?php } ?>><a href="slides.php">图片轮播</a></li>
					<li <?php if($active == 'settings') { ?> class="active" <?php } ?>><a href="settings.php">网站设置</a></li>
				</ul>
			</li>
			<?php }else{ ?>
				<li >
				<a href="#menu-settings" class="collapsed" data-toggle="collapse">
					<i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
				</a>
				<ul id="menu-settings" class="collapse">
					<li><a href="menus.php">导航菜单</a></li>
					<li><a href="slides.php">图片轮播</a></li>
					<li><a href="settings.php">网站设置</a></li>
				</ul>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php include'./inc/script.php'; ?>
	<script>
		$('#avatar').on('change',function(){
			//将文件转成二进制
			var data=new FormData();
			data.append('avatar',this.files[0]);
			// jquery内部不支持xh2
			//声明xhr
			var xhr=new XMLHttpRequest;
			// 使用open打开一个链接
			xhr.open('post','/admin/upfile.php');
			//get方式可以不设请求头
			//发送数据
			xhr.send(data);
			NProgress.start();
			//事件监听
			xhr.onreadystatechange=function(){		
				if(xhr.readyState==4&&xhr.status==200){
					// console.log(xhr.responseText);
					 $('.preview').attr('src',xhr.responseText);
					 NProgress.done();
				}
			}
		})
	</script>
</body>
</html>
