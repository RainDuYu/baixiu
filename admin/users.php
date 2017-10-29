<?php 
	//引入功能文件
	require '../functions.php';
	//检测登录
	checkLogin();
	$actives=array('');
	$active='users';
	$message='';
	$title='添加用户';
	$btnText='添加';
	//获取地址参数
	$action=isset($_GET['action'])?$_GET['action']:'add';
	if(!empty($_POST)){ 
		//以post提交，并且提交了数据
		//添加操作
		if($action=='add'){
			//状态默认是未激活
			$_POST['status'] = 'unactivated';
			//执行插入操作
			$result = insert('users', $_POST);
			if($result){ //插入成功
					header('Location: /admin/users.php');
			}else{//插入失败
		$message='添加新用户失败';
			}
		}
		//更新操作
		if($action=='update'){
			//获取用户id，根据用户id对数据进行修改
			$id=$_POST['id'];
			//删除某个变量,相当于js中的delete
			unset($_POST['id']);
			//id字段是主键，不能被修改,所以要将其从数组中删除
			$result=update('users',$_POST,$id);
			//执行修改操作
			if($result){//修改成功
				header('Location: /admin/users.php');
				exit;
			}
		}
		if($action=='deleteAll'){
			$sql = 'DELETE FROM users WHERE id in (' . implode(',',  $_POST['ids']) . ')';
			$result=delete($sql);
			header('Content-Type:application/json');
			if($result){
				$info=array('code'=>10000,'message'=>'删除成功');
				echo json_encode($info);
			}else{
				$info=array('code'=>10001,'message'=>'删除失败');
				echo json_encode($info);
			}
			exit;
		}
	}
	//查询所有用户
	$lists=query('SELECT * FROM users');
	//编辑或删除操作
	//先判断有没有user_id
		 $user_id = isset($_GET['user_id'])?$_GET['user_id']:'';
		 //编辑操作
		if($action=='edit'){
			//用action判断是添加操作还是修改操作
			$action='update';
			$title='编辑用户';
			$btnText='更新';
			//查询结果
			$rows=query('SELECT * FROM users WHERE id=' . $user_id);
		}else if($action=='delete'){
			//执行删除操作
			// delete from users where 
			$result=delete('DELETE FROM users where id=' . $user_id);
			if($result){
				header('Location: /admin/users.php');
				exit;
			}
		}
 ?>
 
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>Users &laquo; Admin</title>
	<link rel="stylesheet" href="../assets/css/admin.css">
<?php include './inc/style.php' ?>
</head>
<body>
	<div class="main">
	<?php include'./inc/nav.php'; ?>
		<div class="container-fluid">
			<div class="page-title">
				<h1>用户</h1>
			</div>
			<!-- 有错误信息时展示 -->
			<?php if(!empty($message)) { ?>
			<div class="alert alert-dange">
				<strong>错误！</strong>
				<?php echo $message; ?>
			</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-4">
				 <form action="/admin/users.php?action=<?php echo $action; ?>" method="post">
						<h2><?php echo $title; ?></h2>
						<div class="form-group">
							<label for="email">邮箱</label>
							<!-- hidden不是用来展示页面的，单纯做数据提交 -->
							<?php if($action!='add'){ ?>
							<input type="hidden" name='id'  value="<?php echo $rows[0]['id']; ?>">
							<?php } ?>
							<input id="email" class="form-control" name="email" type="email" value="<?php echo isset($rows[0]['email']) ? $rows[0]['email'] : ''; ?>" placeholder="邮箱">
						</div>
						<div class="form-group">
							<label for="slug">别名</label>
							<input id="slug" class="form-control" name="slug" type="text" value="<?php echo isset($rows[0]['slug']) ? $rows[0]['slug'] : ''; ?>" placeholder="slug">
							<p class="help-block">https://zce.me/author/<strong>slug</strong></p>
						</div>
						<div class="form-group">
							<label for="nickname">昵称</label>
							<input id="nickname" class="form-control" name="nickname" value="<?php echo isset($rows[0]['nickname']) ? $rows[0]['nickname'] : ''; ?>" type="text" placeholder="昵称">
						</div>
						<div class="form-group">
							<label for="password">密码</label>
							<input id="password" class="form-control" name="password" type="text" value="<?php echo isset($rows[0]['password']) ? $rows[0]['password'] : ''; ?>" placeholder="密码">
						</div>
						<div class="form-group">
							<button class="btn btn-primary" type="submit"><?php echo $btnText; ?></button>
						</div>
					</form>
				</div>
				<div class="col-md-8">
					<div class="page-action">
						<!-- show when multiple checked -->
						<a class="btn btn-danger btn-sm delete" href="javascript:;" style="display: none">批量删除</a>
					</div>
					<table class="table table-striped table-bordered table-hover">
						<thead>
							 <tr>
								<th class="text-center" width="40">
								<input type="checkbox" id="toggle"></th>
								<th class="text-center" width="80">头像</th>
								<th>邮箱</th>
								<th>别名</th>
								<th>昵称</th>
								<th>状态</th>
								<th class="text-center" width="100">操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($lists as $key=>$val) {?>
							<tr>
								<td class="text-center"><input type="checkbox" value="<?php echo $val['id']; ?>" class='chk'></td>
								<td class="text-center"><img class="avatar" src="<?php echo  $val['avatar']; ?>"></td>
								<td><?php echo $val['email'] ?></td>
								<td><?php echo $val['slug']; ?></td>
								<td><?php echo $val['nickname']; ?></td>
								<?php if($val['status']=='activated'){ ?>
								<td>已激活</td>
								<?php }else if($val['status']=='unactivated') {?>
								<td>未激活</td>
								<?php } else if($val['status']=='forbidden'){?>
								<td>已禁用</td>
								<?php }else{ ?>s
								<td>已删除</td>
								<?php } ?>
								<td class="text-center">
									<a href="/admin/users.php?action=edit&user_id=<?php echo $val['id']; ?>" class="btn btn-default btn-xs">编辑</a>
									<a href="/admin/users.php?action=delete&user_id=<?php echo $val['id']; ?>" class="btn btn-danger btn-xs">删除</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php include'./inc/aside.php'; ?>
	<?php include'./inc/script.php'; ?>
	<script>
	//change事件或者click事件
		$("#toggle").on('click',function(){
			//this指原生DOM
			if(this.checked){
				$('.chk').prop('checked',true);
				$('.delete').show();
			}else{
				$('.chk').prop('checked',false);
				$('.delete').hide();
			}
		})
		$('.chk').on('change',function(){
			var size=$('.chk:checked').size();
			//如果大于0则批量操作
			if(size>0){
				$('.delete').show();
				return;
			}
			//如果小于等于0则按钮隐藏
			$('.delete').hide();
		})
		//有一个没有选中，则全部选中按钮也不会打对勾
			var toggle=document.getElementById('toggle');
			var chk=document.getElementsByClassName('chk');
			for(var i=0;i<chk.length;i++){
				chk[i].onclick=function(){
					var bool=true;
					for(var j=0;j<chk.length;j++){
						if(chk[j].checked==false){
							bool=false;
						}     
					}
					toggle.checked=bool; 
				}
			}
		$('.delete').on('click',function(){	
			var ids=[];
			$('.chk:checked').each(function(){
				ids.push($(this).val());
			});
			// 发送 ajax 请求
			$.ajax({
				url:'/admin/users.php?action=deleteAll',
				type:'post',
				// 将所有的选中的用户的 id 传给后端
				data:{ids:ids},
				success:function(info){
					alert(info.message);
					if(info.code==10000){
						location.reload();
					}
				}
			});
		})
	</script>     
</body>
</html>
