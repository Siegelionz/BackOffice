<?php 
  require_once '../function.php';
  foo();

  function addData() {
    //执行校验
    if(empty($_POST['name'])){
      $GLOBALS['classifyError'] = '名称不能为空';
      return;
    }
    if(empty($_POST['slug'])){
      $GLOBALS['classifyError'] = '别名不能为空';
      return;
    }
    //文本框都填写上 把值暂存到变量中
    $name = $_POST['name'];
    $slug = $_POST['slug'];

    setServerValue("insert into categories values (null,'{$slug}','{$name}')");

  }
  function upDate() {
    if(empty($_POST['name'])){
      $GLOBALS['classifyError'] = '名称不能为空';
      return;
    }
    if(empty($_POST['slug'])){
      $GLOBALS['classifyError'] = '别名不能为空';
      return;
    }
    $id = $_POST['id'];
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $affected_rows = setServerValue("update categories set slug = '{$slug}',name = '{$name}' where id = {$id}");


    if($affected_rows === 1){
      $GLOBALS['success'] = '修改成功';
    }
    
  }
  //如果提交方式为post 执行一系列程序
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // addData();
    //判断一个用户是添加数据还是修改数据
    if(empty($_POST['id'])){
      addData();
    }else{
      upDate();
    }
  }

  //接收数据库里的数据 并显示把数据显示到表格中
  $classify = getServerAllValue('select * from categories');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <?php if (isset($classifyError)): ?>
       <!-- 有错误信息时展示 -->
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $classifyError ?>
        </div>
      <?php endif ?>
        <?php if (isset($success)): ?>
          <div class="alert alert-success">
            <strong>成功！</strong> <?php echo $success; ?>
          </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <h2>添加新分类目录</h2>
            <input type="hidden" name="id" id="id" value=0 >
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($_POST['slug']) ? $_POST['slug'] : '' ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
            <button  id="add" class="btn btn-primary btn-update" type="submit">添加</button>
            <a id="btn" class="btn btn-primary"  style="display:none">取消</a>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="block" class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
               <?php foreach ($classify as $v): ?>
                 <tr>
                   <td class="text-center"><input type="checkbox" data-id="<?php echo $v['id'] ?>"</td>
                   <td><?php echo $v['name'] ?></td>
                   <td><?php echo $v['slug'] ?></td>
                   <td class="text-center">
                   <a id="update" class="btn btn-info btn-xs" data-name="<?php echo $v['name'] ?>"data-slug="<?php echo $v['slug'] ?>" data-id="<?php echo $v['id'] ?>">编辑</a>
                   <a href="/admin/categories-delete.php?id=<?php echo $v['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                   </td>
                 </tr>
               <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    //创建一个数组来存放被选中的id值
    var arr = [];
    //入口函数
    $(function($){
      //当input标签发生改变时
      $('tbody input').on('change',function(){
        //获取this
        var $this = $(this);
        //获取input标签的自定义id值
        var $id = $this.data('id');
        //判断这个标签的属性checked的值
        if($this.prop('checked')){
          //如果为true 把当前的标签的id值放入数组中
          arr.push($id);
        }else{
          //如果checked为false 删除这个id值
          arr.splice(arr.indexOf($id),1);
        }
        //判断这个数组的长度  只要有值 批量删除就显示出来
        arr.length ? $('#block').fadeIn() : $('#block').fadeOut();
        //当批量删除出现时我们给它传数组里的值 进行批量删除
        $('#block').prop('href',"/admin/categories-delete.php?id="+arr);
      })

      //当我们选中最上面的选项时 下面的选项全部选中 并且批量删除出现
      $('thead input').on('change',function(){
        //当全选按钮被选中 获取tbody下的所有input并设置它们属性checked与thead的checked值一样
        //然后执行tbody的change事件
        $('tbody input').prop('checked',$(this).prop('checked')).trigger('change');
      })

      //当我们点击编辑按钮 修改信息
      $('.btn-info').on('click',function(){
        var name = $(this).data('name');
        var slug = $(this).data('slug');
        //当我们点击编辑时  改变左边编辑表单隐藏域的values值
        var id = $(this).data('id');
        $('form h2').text('修改分类目录');
        $('form button.btn-update').text('修改');
        $('#name').val(name);
        $('#slug').val(slug);
        $('#id').val(id);
        $('#btn').fadeIn();
      })
      $('#btn').on('click',function(){
        $('form h2').text('添加新分类目录');
        $('form button.btn-update').text('添加');
        $('#name').val('');
        $('#slug').val('');
        //如果我们不想编辑了，点取消原本给隐藏域设置的值变回原来的值
        $('#id').val(0);
        $('#btn').fadeOut();
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
