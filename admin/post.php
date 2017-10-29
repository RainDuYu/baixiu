<?php 
	require '../functions.php';
	checkLogin();
	$actives=array('category','posts','post');
	$active='post';
	$action=isset($_GET['action'])?$_GET['action']:'add';
	if(!empty($_POST)||$action=='upfile'){
		if($action=='add'){
			unset($_POST['id']);
			$result=insert('posts',$_POST);
			if($result){
				header('Location: /admin/posts.php');
				exit;
			}
			$message='添加文章失败';
		}else if($action=='upfile'){
			$path='../uploads/thumbs';
			if(!file_exists($path)){
					mkdir($path);
			}
			$ext=explode('.',$_FILES['feature']['name'])[1];   
			$filename=time();
			$dest=$path . '/' . $filename . '.' .$ext;
			move_uploaded_file($_FILES['feature']['tmp_name'],$dest);
			echo substr($dest,2);
			exit;
		} else if($action='update'){
			$id=$_POST['id'];
			unset($_POST['id']);
			$result=update('posts',$_POST,$id);
			if($result){
				header('Location: /admin/post.php');
				exit;
			}
		}
	}
	$sql = 'SELECT * FROM categories';
	$lists = query($sql);
	if($action=='edit'){
		$action='update';
		$pid = $_GET['pid'];
		$sql='SELECT * FROM posts WHERE id=' . $pid;
		$rows=query($sql);
	}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>Add new post &laquo; Admin</title>
	<?php include'./inc/style.php'; ?>
	<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
	<div class="main">
		<?php include'./inc/nav.php'; ?>
		<div class="container-fluid">
			<div class="page-title">
				<h1>写文章</h1>
			</div>
			<form class="row" action='/admin/post.php' method="post">
			<input type="hidden" name='user_id' value='<?php echo $_SESSION['user_info']['id']; ?>'>
			 <input type="hidden" name="id" value="<?php echo $pid; ?>">
				<div class="col-md-9">
					<div class="form-group">
						<label for="title">标题</label>
						<input id="title" class="form-control input-lg" name="title" type="text" value="<?php echo isset($rows[0]['title'])?$rows[0]['title']:''; ?>" placeholder="文章标题">
					</div>
					<div class="form-group">
						<label for="content">内容</label>
						<textarea id="content" style="height:300px"  name="content"  cols="30" rows="10" placeholder="内容"><?php echo isset($rows[0]['content'])?$rows[0]['content']:''; ?></textarea>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="slug">别名</label>
						<input id="slug" class="form-control" name="slug" type="text" value="<?php echo isset($rows[0]['slug'])?$rows[0]['slug']:''; ?>" placeholder="slug">
						<p class="help-block">https://zce.me/post/<strong>slug</strong></p>
					</div>
					<div class="form-group">
						<label for="feature">特色图像</label>
						<!-- show when image chose -->
						<?php if(empty($rows[0]['feature'])){ ?>
							<img class="help-block thumbnail" style="display: none">
						<?php } else { ?>
						 <img class="help-block thumbnail" src="<?php echo $rows[0]['feature'] ; ?>">
						<?php } ?>
						<input id="feature" class="form-control" name="feature" type="file">
						<input type="hidden" value="<?php echo isset($rows[0]['feature'])?$rows[0]['feature']:''; ?>"  name="feature" id="thumb">
					</div>  
					<div class="form-group">
						<label for="category">所属分类</label>
						<select id="category" class="form-control" name="category_id">
						 <?php foreach($lists as $key=>$val) { ?>
								<?php if(isset($rows[0]['category_id'])){ ?>
									<option value="<?php echo $val['id']; ?>" <?php if($rows[0]['category_id']==$val['id']){ ?> selected<?php } ?>> <?php echo $val['name']; ?> </option>
								 <?php }else{ ?>
									<option value="<?php echo $val['id']; ?>" > <?php echo $val['name']; ?>  </option>
								 <?php } ?>              
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label for="created">发布时间</label>
						<?php if(isset($rows)) {?>
						<input id="created" class="form-control" name="created" value="<?php echo isset($rows[0]['created']) ? $rows[0]['created'] : ''; ?>" type="text">
						<?php }else{?>
								<input id="created" class="form-control" name="created" type="datetime-local">
						<?php }?>
					</div>
					<div class="form-group">
						<label for="status">状态</label>
						<select id="status" class="form-control" name="status">
							<option value="drafted" <?php if(isset($rows[0]['status'])?$rows[0]['status']:0 =='drafted'){ ?>selected <?php } ?>>草稿</option>
							<option value="published" <?php if(isset($rows[0]['status'])?$rows[0]['status']:1=='published'){ ?>selected <?php } ?>>已发布</option>
						</select>
					</div>
					<div class="form-group">
						<button class="btn btn-primary" type="submit">保存</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php include'./inc/aside.php'; ?>
	<?php include'./inc/script.php'; ?>
	<script src="/assets/vendors/ueditor/ueditor.config.js"></script>
	<script src="/assets/vendors/ueditor/ueditor.all.min.js"></script>
	<script>
		//富文本编辑器
		UE.getEditor('content',{
			autoHeightEnabled:true
		});
		$('#feature').on('change',function(){
			//通过原生DOM可以获得文件信息
			// this.files[0]
			//通过h5内置对象FormData可以实现文件数据管理
			var data=new FormData();
			data.append('feature', this.files[0]);
			var xhr=new XMLHttpRequest;
			xhr.open('post','/admin/post.php?action=upfile');
			//使用data管理数据,可以不用设置请求头,浏览器会自动设置
			xhr.send(data);
			xhr.onreadystatechange=function(){
				if(xhr.readyState==4&&xhr.status==200){
					$('.thumbnail').attr('src',xhr.responseText).show();
					$('#thumb').val(xhr.responseText);
				}
			}
		})
	</script>
</body>
</html>
