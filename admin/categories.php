<?php 
	require '../functions.php';
	$message='';
	//检测登录
	checkLogin();
	$actives=array('category','posts','post');
	$active='category';
	// 获取action值，根据action值决定处理哪些逻辑
	$action=isset($_GET['action'])?$_GET['action']:'add';
	//编辑和删除操作必须拥有一个条件,一般根据主键(id)来确定
	$cat_id=isset($_GET['cat_id'])?$_GET['cat_id']:0;
	//分类列表数据
	$lists=query('SELECT * FROM categories');
	//添加
	if($action=='add'){
		//子标题变化
		$title='添加分类目录';
		//按钮根据操作而变化
		$btnText='添加';
		//只有点击添加按钮时才有必要操作数据库
		if(!empty($_GET)){
			//id值不能被修改
			unset($_GET["id"]);
			unset($_GET["action"]);
			$result=insert('categories',$_GET);          
			if($result){
				header('Location: /admin/categories.php');
			}
		}
	}else if($action=='edit'){//编辑
		//文字显示
		$action='update';
		$btnText='修改';
		$title='修改分类目录';
		//根据id查询
		$sql='SELECT *FROM categories WHERE id=' . $cat_id;
		//执行查询
		$rows=query($sql);
	}else if($action=='delete'){//删除
		///根据id查询
		$sql='DELETE FROM categories WHERE id=' . $cat_id;
		$result=delete($sql);
		//编辑后刷新页面
		if($result){
			header('Location: /admin/categories.php');
			exit;
		}
	}else if($action=='update'){//更新
		unset($_GET['action']);
		$cat_id=$_GET['id'];
		//id是主键,不允许被更改
		unset($_GET['id']);
		//执行更新操作
		$result=update('categories',$_GET,$cat_id);
		//执行成功后刷新当前页面 
		if($result){
			header('Location: /admin/categories.php');
			exit();
		}
		//更新失败信息提醒
		$message='更新失败';
	}else if($action=='deleteAll'){
		$sql = 'DELETE FROM categories WHERE id in (' . implode(',',  $_POST['ids']) . ')';
		echo $sql;
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
 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>Categories &laquo; Admin</title>
	<?php include'./inc/style.php'; ?>
	<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
	<div class="main">
		<?php include'./inc/nav.php'; ?>
		<div class="container-fluid">
			<div class="page-title">
				<h1>分类目录</h1>
			</div>
			<!-- 有错误信息时展示 -->
			<!-- message没有内容时不显示 -->
			<?php if(!empty($message)){ ?>
			<div class="alert alert-danger">
				<strong>错误！</strong>
				<?php echo $message; ?>
			</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-4">
					<form  action='/admin/categories.php' method="='get">
						<input type="hidden" name='action' value="<?php echo $action; ?>">
						<input type="hidden" name='id' value="<?php echo $cat_id; ?>">
						<h2><?php echo $title; ?></h2>
						<div class="form-group">
							<label for="name">名称</label>
							<!-- 名称里面有内容时显示内容,没有内容时显示占位符 -->
							<input id="name" class="form-control" name="name" type="text" value='<?php echo isset($rows[0]['name'])?$rows[0]['name']:''; ?>' placeholder="分类名称">
						</div>
						<div class="form-group">
							<label for="slug">别名</label>
							<input id="slug" class="form-control" name="slug" type="text" value='<?php echo isset($rows[0]['slug'])?$rows[0]['slug']:'';  ?>' placeholder="slug">
							<p class="help-block">https://zce.me/category/<strong>slug</strong></p>
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
								<input type="checkbox" id='toggle' ></th>
								<th>名称</th>
								<th>Slug</th>
								<th class="text-center" width="100">操作</th>
							</tr>
						</thead>
						<tbody>
						<!-- 循环获取数据库中的内容，并依此显示 -->
						<?php foreach($lists as $key=>$val){ ?>
							<tr>
							 <td class="text-center">
							 <input type="checkbox" class='chk'></td>
							 <td><?php echo $val['name']; ?></td>
							 <td><?php echo $val['slug']; ?></td>
							 <td class="text-center">
							 <!-- href获取地址，是对哪一个id进行操作 -->
							 <!-- action=edit/delete表示获取什么操作 -->
								 <a href="/admin/categories.php?action=edit&cat_id=<?php echo $val['id']; ?>" class="btn btn-info btn-xs" action='edit'>编辑</a>
								 <a href="/admin/categories.php?action=delete&cat_id=<?php echo $val['id']; ?>" class="btn btn-danger btn-xs" action='delete'>删除</a>
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
			//点击全选按钮后,所有的子元素和全选按钮的状态一致
			$("#toggle").on('click',function(){
			//this指原生DOM
			if(this.checked){
				$('.chk').prop('checked',true);
				$('.delete').show();//显示内容(标签)
			}else{
				$('.chk').prop('checked',false);
				$('.delete').hide();
			}
		})
		$('.chk').on('change',function(){
			var size=$('.chk:checked').size();
			//如果大于0则显示批量操作
			if(size>0){
				$('.delete').show();
				return;
			}
			//如果小于等于0则批量操作按钮隐藏
			$('.delete').hide();
		})
		//有一个没有选中，则全部选中按钮也不会打对勾
			var toggle=document.getElementById('toggle');
			var chk=document.getElementsByClassName('chk');
			for(var i=0;i<chk.length;i++){
				chk[i].onclick=function(){
					//开闭原则
					var bool=true;
					for(var j=0;j<chk.length;j++){
						if(chk[j].checked==false){
							bool=false;
						}     
					}
					//全选按钮和bool值保持一致
					toggle.checked=bool; 
				}
			}
		$('.delete').on('click',function(){ 
			var ids=[];
			$('.chk:checked').each(function(){
				ids.push($(this).val());
			});
			$.ajax({
				url:'/admin/categories.php?action=deleteAll',
				type:'get',
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
