<?php 
 require_once '../function.php';
function login(){
  //判断邮箱是否填写
  if(empty($_POST['email'])){
    $GLOBALS['error'] = '请填写邮箱';
    return;
  }
  //判断密码是否填写
  if(empty($_POST['password'])){
    $GLOBALS['error'] = '请填写密码';
    return;
  }
  //邮箱和密码都填写后，分别存放到变量中
  $user = $_POST['email'];
  $password = $_POST['password'];
  //引入公共文件
  // require '../config.php';
  //连接数据库来校验邮箱和密码是否匹配
  $value = getServerOnce("select * from users where email = '$user' ");
  // $connect = mysqli_connect(BAIXIU_DB_HOST,BAIXIU_DB_USER,BAIXIU_DB_PASS,BAIXIU_DB_NAME);
  // //判断是否连接成功
  // if(!$connect){
  //   $GLOBALS['error'] = '连接数据库失败';
  //   return;
  // }
  // //查询数据库中的数据
  // $query = mysqli_query($connect,"select * from users where email = '$user' ");
  // $value = mysqli_fetch_assoc($query);
  //接收到数据库的数据判断正确
  if($user != $value['email']){
    $GLOBALS['error'] = '邮箱与密码不正确';
    return;
  }
  if($password != $value['password']){
    $GLOBALS['error'] = '邮箱与密码不正确';
    return;
  }
  //邮箱和密码都匹配成功，向服务端申请开启一个箱子
  session_start();
  $_SESSION['cookie'] = $value;
  //邮箱与密码都正确  响应
  header('Location:./index.php');
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  login();
}
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
      <img class="avatar" src="/static/assets/img/default.png" id="avatar">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong> <?php echo $error ?>
        </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    //入口函数
    $(function($){
      //当失去邮件文本框焦点时触发事件
      $("#email").on("blur",function(){
        //获取文本框的值
        var text = this.value;
         var xhr = new XMLHttpRequest();
         xhr.addEventListener("readystatechange",function(){
            if(this.readyState === 4){
              var header = this.responseText;
              header === '' ? $("#avatar")[0].src ='/static/assets/img/default.png' : $("#avatar")[0].src = header ; 
            }
         })
         xhr.open('GET','./user_head.php?text='+text);
         xhr.send();
      })
    })
  </script>
</body>
</html>
