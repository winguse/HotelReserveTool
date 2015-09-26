<?php require_once dirname(__FILE__). '/../header.php';?>
<h1>Room #<?=$item->id ?></h1>
<form class="form-horizontal" method="post" action="<?=APP_BASE_PATH ?>/admin/Room/<?=$item->id ?>">


	<div class="form-group">
		<label for="hotel_id" class="col-sm-2 control-label">酒店</label>
		<div class="col-sm-10">
			<select class="form-control" name="hotel_id" id="hotel_id">
<?php

foreach($hotels as $hotel){
?>
			  <option value="<?=$hotel->id?>" <?=($item->hotel_id == $hotel->id ? 'selected' : '')?>><?=$hotel->name?></option>
<?php
}
?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="breakfast" class="col-sm-2 control-label">早餐</label>
		<div class="col-sm-10">
			<select class="form-control" name="breakfast" id="breakfast">
			  <option value="0">无</option>
			  <option value="1" <?=($item->breakfast ? 'selected' : '')?>>有</option>
			</select>
		</div>
	</div>


	<div class="form-group">
		<label for="internet" class="col-sm-2 control-label">网络</label>
		<div class="col-sm-10">
			<select class="form-control" name="internet" id="internet">
			  <option value="0">无</option>
			  <option value="1" <?=($item->internet ? 'selected' : '')?>>有</option>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="type_name" class="col-sm-2 control-label">房间类型</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="type_name" name="type_name" placeholder="房间类型" value="<?=html_encode($item->type_name)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="volume" class="col-sm-2 control-label">可入住人数</label>
		<div class="col-sm-10">
			<input type="number" class="form-control" id="volume" name="volume" placeholder="房间容量" value="<?=html_encode($item->volume)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="total" class="col-sm-2 control-label">房间数量</label>
		<div class="col-sm-10">
			<input type="number" class="form-control" id="total" name="total" placeholder="房间数量" value="<?=html_encode($item->total)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="price" class="col-sm-2 control-label">价格</label>
		<div class="col-sm-10">
			<input type="number" class="form-control" id="price" name="price" placeholder="价格" value="<?=html_encode($item->price)?>">
		</div>
	</div>

	<div class="form-group">
		<label for="description" class="col-sm-2 control-label">描述</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="description" name="description" placeholder="描述" value="<?=html_encode($item->description)?>">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">提交</button>
		</div>
	</div>
</form>

<?php require_once dirname(__FILE__). '/../footer.php';?>