<?php 
  require_once '../function.php';
  foo();
  function addUsers() {
    //接收到文件判断一下文件是否符合标准
    //判断是否上传成功
    var_dump($_FILES['head']);
    if($_FILES['head']['error'] !==0){
      $GLOBALS['error'] = '上传图片失败';
      return;
    }
    //判断上传的文件是否符合标准
    $images = array('image/png','image/jpeg');
    if(!in_array($_FILES['head']['type'], $images)){
      $GLOBALS['error'] = '上传图片类型不标准';
      return;
    }
    //判断上传的文件的大小
    if($_FILES['head']['size'] < 0 || $_FILES['head']['size'] > 20*1024*1024){
      $GLOBALS['error'] = '上传图片大小不符合';
      return;
    }
    //如果都符合文件上传的标准 我们移动文件
    $oldImage = $_FILES['head']['tmp_name'];
    $newImage = '../static/uploads/'.$_FILES['head']['name'];
    $headImage = substr($newImage, 2);
    var_dump($headImage);
    $moveImage = move_uploaded_file($oldImage, $newImage);
    if(!$moveImage){
      $GLOBALS['error'] = '移动图片出错,请重新上传';
      return;
    }
    if(empty($_POST['email'])){
      $GLOBALS['error'] = '请填写邮箱';
      return;
    }
    if(empty($_POST['slug'])){
      $GLOBALS['error'] = '请填写别名';
      return;
    }
    if(empty($_POST['nickname'])){
      $GLOBALS['error'] = '请填写名称';
      return;
    }
    if(empty($_POST['password'])){
      $GLOBALS['error'] = '请填写密码';
      return;
    }
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];
    //全部确认完毕  添加数据库
    setServerValue("insert into users values(null,'{$slug}','{$email}','{$password}','{$nickname}','{$headImage}',null,'unactivated');");
  }

  //如果提交方式为post 声明提交了数据 
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    addUsers();
  }
  //连接数据库显示出数据库中所有数据
  $users = getServerAllValue("select * from users");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $error ?>
        </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="head">头像</label>
              <input id="head" class="form-control" name="head" type="file" placeholder="头像" accept="image/*">
            </div>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($_POST['slug']) ? $_POST['slug'] : '' ?>">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称" value="<?php echo isset($_POST['nickname']) ? $_POST['nickname'] : '' ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="password" placeholder="密码" value="<?php echo isset($_POST['password']) ? $_POST['password'] : '' ?>">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $v): ?>
                <tr>
                  <td class="text-center"><input type="checkbox"></td>
                  <td class="text-center"><img class="avatar" src="<?php echo $v['avatar'] ?>"></td>
                  <td><?php echo $v['email'] ?></td>
                  <td><?php echo $v['slug'] ?></td>
                  <td><?php echo $v['nickname'] ?></td>
                  <td><?php echo $v['status'] === 'activated' ? '激活' : '未激活' ?></td>
                  <td class="text-center">
                    <a href="post-add.php" class="btn btn-default btn-xs">编辑</a>
                    <a href="/admin/users_delete.php?id=<?php echo $v['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
              </tr>
              <?php endforeach ?>
             </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
