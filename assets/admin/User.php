

<form class="form-horizontal" method="POST" action="<?=APP_BASE_PATH ?>/admin/User/bulk-add">
	<div class="form-group">
		<label for="user-infos" class="col-sm-2 control-label">批量添加用户信息</label>
		<div class="col-sm-10">
			<textarea class="form-control" rows="3" name="user-infos" id="user-infos" placeholder="格式：用户名 密码 邮箱 学校

空格，TAB均可；每个一行。"></textarea>
		</div>
	</div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">提交</button>
    </div>
  </div>
</form>