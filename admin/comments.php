<?php 
	 require '../functions.php';
	//检测登录
	checkLogin();
	$user_id=$_SESSION['user_info']['id'];
	$cogs = array('comments');
	$active = '';
	$sql='SELECT count(*) AS total FROM comments';
	$total=query($sql)[0]['total'];
	$pageSize=10;
	$pageCount=ceil($total/$pageSize);
	// 一次显示六个页面
	$pageLimit=6;
	//获取用户当前操作页码
	$currentPage=isset($_GET['page'])?$_GET['page']:1;
	//上一页
	$prevPage=$currentPage - 1;
	//下一页
	$nextPage=$currentPage + 1;
	//计算起始页码
	$start=$currentPage-ceil($pageLimit/2);
	if($start<=1){
	  $start=1;
	}
	$end=$start+$pageLimit-1;
	if($end>=$pageCount){
	  $end=$pageCount;
	  $start=$end-($pageLimit-1);
	  if($start<=1){
	    $start=1;
	  }
	}
	$pages=range($start,$end);
	$offset=($currentPage - 1)*$pageSize;
	// 数据库查询
	$sql = 'SELECT comments.id, comments.author, comments.content, comments.email ,comments.created,comments.status FROM comments LIMIT '. $offset . ', ' . $pageSize;
	$lists=query($sql);
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<title>Comments &laquo; Admin</title>
	<?php include'./inc/style.php'; ?>
	<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
	<div class="main">
		<?php include'./inc/nav.php'; ?>
		<div class="container-fluid">
			<div class="page-title">
				<h1>所有评论</h1>
			</div>
			<div class="page-action">
				<ul class="pagination pagination-sm pull-right">
					<?php if($currentPage>1){ ?>
			            <li>
			              <a href="/admin/comments.php?page=<?php echo $prevPage; ?>">上一页</a>
			            </li>
			          <?php } ?>
			          <?php foreach($pages as $key=>$val){ ?>
			            <?php if($currentPage==$val){ ?>
			              <li class='active'>
			                <a href="/admin/comments.php?page=<?php echo $val; ?>">
			                    <?php echo $val; ?>
			                </a>
			              </li>
			            <?php } else {?>
			                <li>
			                <a href="/admin/comments.php?page=<?php echo $val; ?>">
			                    <?php echo $val; ?>
			                </a>
			              </li>
			            <?php } ?>
			          <?php } ?>          
			          <?php if($currentPage<$pageCount){ ?>
			            <li>
			               <a href="/admin/comments.php?page=<?php echo $nextPage; ?>">下一页</a>
			            </li>
			          <?php } ?>
				</ul>
			</div>
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="text-center" width="60">序号</th>
						<th>作者</th>
						<th>评论</th>
						<th>评论在</th>
						<th>提交于</th>
						<th>状态</th>
						<th class="text-center" width="100">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach( $lists as $key=>$val){ ?> 
					<tr>
						<td class="text-center">
							<?php echo $key+1; ?>
						</td>
						<td>
							<?php echo $val['author'];  ?>
						</td>
						<td>
							<?php echo $val['content'];  ?>
						</td>
						<td>
							<?php echo $val['email'];  ?>
						</td>
						<td>
							<?php echo $val['created'];  ?>
						</td>
						<td>
							<?php echo $val['status'];  ?>
						</td>
						<td class="text-center">
							<?php if($val['status']=='rejected') {?>
								<a href="post-add.php" class="btn btn-info btn-xs">批准</a>
							<?php }else{ ?>
								<a href="post-add.php" class="btn btn-warning btn-xs">驳回</a>
							<?php } ?>
							 <a href="/admin/comments.php" class="btn btn-danger btn-xs">删除</a>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php include'./inc/aside.php'; ?>
	<?php include'./inc/script.php'; ?>
</body>
</html>
