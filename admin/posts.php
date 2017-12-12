<?php

// 载入全部公共函数
require_once '../function.php';
// 判断是否登录
foo();


$option = getServerAllValue('select * from categories');

$where = ' 1=1';
//获取一下分类的选择 并且判断是否选择了分类
if(isset($_GET['categori']) && $_GET['categori'] !== 'all'){
  $where .= ' and posts.category_id = '.$_GET['categori'];
  $categori = 'categori='.$_GET['categori'];
}
//获取文章的状态
if(isset($_GET['status']) && $_GET['status'] !== 'all'){
  $where .= " and posts.status ='{$_GET['status']}'";
  // $where .= ' and posts.status = '.$_GET['status'] ;
  $status = "status={$_GET['status']} ";
  var_dump($status);
}
// $status_val = isset($status) ? $status : '';
//判断一下
if(isset($status)){
  $num = empty($_GET['page']) ? 1 : (int)$_GET['page'];
  $size = 225;
  $offset = ($num -1 ) * $size;
  $count = getServerOnce("select count(1) as count from posts where posts.status ='{$_GET['status']}'")['count'];
  $pages = (int)ceil($count/$size);
  $start = $num - 2;
  if($start<1){
    $start = 1;
  }
    $end = $start + 4;
  if ($end > $pages ) {
    $end = $pages ;
    $start = ($end-4) < 1 ?  1  :  $end-4;
    $num = true;
  }
}else{
$num = empty($_GET['page']) ? 1 : (int)$_GET['page'];
//设置一个页面显示几条数据
$size = 20;
//根据规律发现 每页的开头的数据为  页数 - 1 * 一页显示的数据
$offset = ($num -1 ) * $size;

//从数据库查询有多少数据
$count = getServerOnce('select count(1) as count from posts')['count'];
//求出按照每页显示的数据来分页数
$pages = (int)ceil($count/$size);
//设置一下当前页面左边的li个数
$start = $num - 2;
//当起始值小于1时 为了不让0 -1 显示，把起始值规定死为1
if($start<1){
  $start = 1;
}
//当起始值小于1时 左边会少两个li  为了补全5个li 此时设置最大值为起始值加4
$end = $start + 4;
//如果最大值超过数据的最大值
if ($end > $pages ) {
  $end = $pages ;
  $start = ($end-4) < 1 ?  1  :  $end-4;
}

  $num = false;
}
// if($num <2){
//   $start = 1;
//   $end = $num +4;
// }
// 获取数据
// posts 也算是一个 开发领域的专用名词，发表物
$posts = getServerAllValue('select
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  users.nickname as user_name,
  categories.name as category_name
from posts
inner join users on posts.user_id = users.id
inner join categories on posts.category_id = categories.id where' . $where .' order by posts.created desc limit '.
$offset .  ',' . $size . ';');


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <style>
    ul li.color{
      background-color:#ccc;
    }
  </style>
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?>" method='get'>
          <select name="categori" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($option as $v): ?>
              <option value="<?php echo $v['id'] ?>" <?php echo isset($_GET['categori']) && $_GET['categori'] ==$v['id'] ? 'selected' : '' ?>><?php echo $v['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all" >所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status']) && $_GET['status'] === 'drafted' ? 'selected' : '' ?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status']) && $_GET['status'] === 'published' ? 'selected' : '' ?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status']) && $_GET['status'] === 'trashed' ? 'selected' : '' ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="/admin/posts.php?page=<?php echo ($_GET['page'] - 1) < 1 ? 1 : $_GET['page'] - 1 ?>">上一页</a></li>
          <?php for($i = $start; $i <= $end; $i ++): ?>
            <li  <?php echo isset($num) && $num == $i  ?  'class="active" ' : '' ?>><a href="/admin/posts.php?<?php echo $num ?  "categori=all&$status&page=$i  " : "page=$i "?>"><?php echo $i ?></a></li>
          <?php endfor ?>
          <li><a href="/admin/posts.php?page=<?php echo ($num + 1) > $pages ? $pages : $num + 1  ?>">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']); ?></td>
            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
