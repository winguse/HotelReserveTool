<?php require_once dirname(__FILE__). '/../header.php';?>
<h1>Hotel #<?=$item->id ?></h1>
<form class="form-horizontal" method="post" action="<?=APP_BASE_PATH ?>/admin/Hotel/<?=$item->id ?>">


	<div class="form-group">
		<label for="name" class="col-sm-2 control-label">酒店名</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="name" name="name" placeholder="酒店名" value="<?=html_encode($item->name)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="description" class="col-sm-2 control-label">酒店描述</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="description" name="description" placeholder="酒店描述" value="<?=html_encode($item->description)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="address" class="col-sm-2 control-label">地址</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="address" name="address" placeholder="地址" value="<?=html_encode($item->address)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="phone" class="col-sm-2 control-label">电话</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="phone" name="phone" placeholder="电话" value="<?=html_encode($item->phone)?>">
		</div>
	</div>
	
	<div class="form-group">
		<label for="map_url" class="col-sm-2 control-label">地图链接</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="map_url" name="map_url" placeholder="地图链接" value="<?=html_encode($item->map_url)?>">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">提交</button>
		</div>
	</div>
</form>

<?php require_once dirname(__FILE__). '/../footer.php';?>