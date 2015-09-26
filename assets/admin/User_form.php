<?php require_once dirname(__FILE__). '/../header.php';?>
<h1>User #<?=$item->id ?></h1>
<form class="form-horizontal" method="post" action="<?=APP_BASE_PATH ?>/admin/User/<?=$item->id ?>">


	<div class="form-group">
		<label for="username" class="col-sm-2 control-label">用户名</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="username" name="username" placeholder="用户名" value="<?=html_encode($item->username)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="password" class="col-sm-2 control-label">密码</label>
		<div class="col-sm-10">
			<input type="password" class="form-control" id="password" name="password" placeholder="密码">
		</div>
	</div>


	<div class="form-group">
		<label for="email" class="col-sm-2 control-label">Email</label>
		<div class="col-sm-10">
			<input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?=html_encode($item->email)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="school" class="col-sm-2 control-label">学校</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="school" name="school" placeholder="学校" value="<?=html_encode($item->school)?>">
		</div>
	</div>
	
	<div class="form-group">
		<label for="role" class="col-sm-2 control-label">角色</label>
		<div class="col-sm-10">
			<select class="form-control" name="role" id="role">
			  <option value="1">用户</option>
			  <option value="3" <?=($item->role == 3 ? 'selected' : '')?>>管理员</option>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="meta" class="col-sm-2 control-label">元数据</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="meta" name="meta" placeholder="元数据" value="<?=html_encode($item->meta)?>">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">提交</button>
		</div>
	</div>
</form>

<?php require_once dirname(__FILE__). '/../footer.php';?>