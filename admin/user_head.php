<?php 
// header('Content-Type: application/json');  
require_once '../function.php';                                                                          
$text = $_GET['text'];
//连接数据库
$head = getServerOnce("select * from users where email = '$text' ");
// $connect = mysqli_connect('127.0.0.1','root','zhangyutian','baixiu-item');
// if(!$connect){
//     $GLOBALS['error'] = '连接数据库失败';
//     return;
//   }
//   //查询数据库中的数据
//   $query = mysqli_query($connect,"select * from users where email = '$text' ");
//   $head = mysqli_fetch_assoc($query);
  // echo json_encode($head);
echo $head['avatar'];