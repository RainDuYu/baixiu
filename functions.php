<?php 
	//导入配置文件
	require __DIR__ . '/config.php';
	session_start();
	function checkLogin(){		 
		//如果读不到user_info，这个session认为是未登录
		if(!isset($_SESSION['user_info'])){
			header('Location: /admin/login.php');
			exit;
		}
	}

	//封装连接数据库
	function connect(){
		$connection=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD);
		// var_dump($connection);
		if(!$connection){
			die('数据库连接失败！');
			//等同于echo数据库连接失败+return
		}
			// 选择数据库
				mysqli_select_db($connection, DB_NAME);
				//设置编码集
			 mysqli_set_charset($connection, DB_CHARSET);
		return $connection;
	}

	//用来执行sql语句
	function query($sql){
		//连接数据库
		$connection=connect();
		//得到的是资源，需要转成数组
		$result=mysqli_query($connection,$sql);
		$rows=fetch($result);
		return $rows;
	}
	// 转成数组
	function fetch($result){
		$rows = array();
		while($row=mysqli_fetch_assoc($result)){
			$rows[]=$row;
		}
		return $rows;
	}

	function insert($table, $arr) {
		// 连接数据库
		$connection = connect();    	
		$keys = array_keys($arr);
		$values = array_values($arr);    	
		$sql = "INSERT INTO " . $table . " (" . implode(", ", $keys) . ") VALUES('" . 	implode(    "', '", $values) . "')";
		// 执行插入语句
		$result = mysqli_query($connection, $sql);   	
		// 返回插入结果
		return $result;
	}

	function delete($sql){
		//DELETE FROM 表名 WHERE 条件
		//连接数据库
		$connection=connect();
		$result=mysqli_query($connection,$sql);
		return $result;
	}

	function update($table,$arr,$id){
		//UPDATE 表名 set key(字段名)=值，字段名=值
		$connection=connect();
		$str='';
		foreach($arr as $key=>$val){
			$str .=$key . '=' . '"' . $val .'",';
		} 
		//截掉多余的
		$str=substr($str,0,-1);
		//sql语句必须有空格
		//UPDATE  SET WHERE前后需要有空格
		//拼凑修改语句
		$sql="UPDATE " . $table . " SET " . $str . " WHERE id=" .$id;
		//执行语句
		$result=mysqli_query($connection,$sql);
		return $result;
	}
 ?>