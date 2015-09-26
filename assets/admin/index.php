<?php require_once dirname(__FILE__). '/../header.php';?>

<ul class="nav nav-tabs">
<?php
	foreach($sections as $section => $section_display_name){
?>
	<li role="presentation"<?php
		if($section == $active_section){
			?> class="active"<?php
		}
	?>><a href="./<?=$section ?>"><?=$section_display_name ?></a></li>
<?php 
	}
?>
</ul>

<?php
if(count($items) == 0){
?>
<p>没有找到有效记录。</p>
<?php
}else{
?>
<table class="table">
	<thead>
		<tr>
<?php
		$ref = new ReflectionClass($items[0]);
		$properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
		foreach($properties as $property){
			?>
			<th><?=$property->getName() ?></th>
			<?php
		}
?>
			<th>
			</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach($items as $item){
?>
		<tr>
<?php
		foreach($properties as $property){
?>
			<td><?=html_encode(admin_table_data_refine_hooker($property->getValue($item), $property->getName(), $ref->getName())) ?></td>
<?php
		}
		
?>
			<td>
				<a href="./<?=$active_section?>/<?=$item->id ?>" title="编辑"><span class="glyphicon glyphicon-pencil"></span></a>
				<a href="./<?=$active_section?>/<?=$item->id ?>/delete" title="删除"><span class="glyphicon glyphicon-trash"></span></a>
			</td>
		</tr>
<?php
	}
?>
		
	</tbody>
</table>
				<a href="./<?=$active_section?>/0" title="添加"><span class="glyphicon glyphicon-plus"></span></a>
<?php
}
require_once dirname(__FILE__).'/'.$active_section.'.php';
?>

<?php require_once dirname(__FILE__). '/../footer.php';?>