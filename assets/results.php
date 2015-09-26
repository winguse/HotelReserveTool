<?php require_once 'header.php';?>

<table class="table">
	<thead>
		<tr>
			<th>学校</th>
			<th>酒店-房间类型</th>
			<th>数量</th>
			<th>起止时间</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach($results as $result){
?>
		<tr>
			<td><?=$result->school?></td>
			<td><?=$result->hotel_name?> - <?=$result->room_type?></td>
			<td><?=$result->book_count?></td>
			<td><?=print_user_meta($result->meta)?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>

<?php 
	$app = \Slim\Slim::getInstance();
//	echo "user->role=".$app->user->role;
?>
<?php require_once 'footer.php';?>