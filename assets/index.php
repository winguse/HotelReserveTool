<?php require_once 'header.php';?>
<div class="page-header">
	<h1><?=APP_NAME ?></h1>
</div>

<form class="form-horizontal" method="POST" action="./login">
  <div class="form-group">
    <label for="username" class="col-sm-2 control-label">用户名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="username" name="username" placeholder="Username">
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-sm-2 control-label">密码</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="password" name="password" placeholder="Password">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">登陆</button>
    </div>
  </div>
</form>

<?php 
	$app = \Slim\Slim::getInstance();
//	echo "user->role=".$app->user->role;
?>
<?php require_once 'footer.php';?>