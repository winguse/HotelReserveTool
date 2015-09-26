<?php require_once dirname(__FILE__). '/../header.php';?>
<div class="alert alert-info">点击 <a href="#" class="label label-default">灰色</a> 按钮就可以完成预定和信息录入，系统会自动保存，结果点 <a href="<?=APP_BASE_PATH?>/results">这里</a> 查看。</div>

<h3>入住计划</h3>
<?php 
	if($start_date == 0 || $end_date == 0x7fffffff)
	{
?>
<div class="alert alert-warning">请选择您的入住计划！</div>
<?php
	}
?>
<table class="table">
	<thead>
		<tr>
			<th>计划入住日期</th>
			<th>计划离店日期</th>
		</tr>
	</thead>
	<tbody>
		<tr>
<?php
$dates = array('start_date' => $start_date, 'end_date' => $end_date);
$interval = 3600 * 24;
foreach($dates as $name => $date){
?>
		<td>
<?php
	$middle = ($date == 0 || $date == 0x7fffffff) ? INIT_BOOK_DATE : $date;
	for($i = -2; $i <= 2; $i++){
		$current = $middle + $i * $interval;
?>
			<a class="label label-<?=$current == $date ? 'danger' : 'default' ?>" href="<?=APP_BASE_PATH.'/user/plan/'.$name.'/'.$current.'/'.$token?>"><?=date('Y-m-d', $current)?></a>
<?php
	}
	
?>
		</td>
<?php
}
?>
		</tr>
	</tbody>
</table>
<hr/>

<?php

$total_price = 0;
$total_volume = 0;
$total_room = 0;
foreach($hotels as $hotel){
?>
<h3><?=$hotel->name?></h3>
<p>电话：<?=$hotel->phone?></p>
<p>地址：<a target="_blank" href="<?=$hotel->map_url?>"><?=$hotel->address?></a></p>
<p><?=$hotel->description?></p>
<table class="table">
	<thead>
		<tr>
			<th>房间类型</th>
			<th>可住人数</th>
			<th>网络</th>
			<th>早餐</th>
			<th>描述</th>
			<th>价格</th>
			<th>剩余数量</th>
			<th>总数量</th>
			<th>预订数</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach($hotel->rooms as $room){
		$left = $room->total - $room->booked;
?>
		<tr>
			<td><?=$room->type_name?></td>
			<td><?=$room->volume?>人</td>
			<td><?=$room->internet ? '有' : '无' ?></td>
			<td><?=$room->breakfast ? '有' : '无' ?></td>
			<td><?=$room->description?></td>
			<td>￥<?=$room->price?></td>
			<td><?=$left?></td>
			<td><?=$room->total?></td>
			<td>
<?php
	if($room->user_booked_count > 0){
		$total_price += $room->user_booked_count * $room->price;
		$total_volume += $room->user_booked_count * $room->volume;
		$total_room += $room->user_booked_count;
		
	}
	
	for($i = -3; $i <= $left && $i <= 3; $i++){
		$book_count = $i + $room->user_booked_count;
		if($book_count < 0) continue;
?>
				<a class="label label-<?=$i == 0 ? 'danger' : 'default' ?>" href="<?=APP_BASE_PATH.'/user/room/'.$room->id.'/book/'.$book_count.'/'.$token?>"><?=$book_count?></a>
<?php 
	}
?>
			</td>
		</tr>
<?php
	}
?>
	</tbody>
</table>
<hr/>
<?php
}
?>
<h3>小计</h3>
<p>共 <?=$total_room?> 房间，<?=$total_volume?> 人入住，￥<?=$total_price?> 每天，<?php 
	if($start_date == 0 || $end_date == 0x7fffffff)
	{
?><b>请选择您的入住计划</b><?php 
	}else{
		$days = intval(($end_date - $start_date) / 24 / 3600);
		echo '共 '.$days.' 天， ￥'.$total_price * $days;
	
	}
?>。</p>
<?php require_once dirname(__FILE__). '/../footer.php';?>