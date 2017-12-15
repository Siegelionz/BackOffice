<?php
//引入公共文件
require_once '../function.php';
//接收id值
//判断一下是否为空i
if(empty($_GET['id'])){
	exit('请填写正确的数字');
}
$id = $_GET['id'];
setServerValue("delete from users where id in ($id)");
header('Location: ./users.php');