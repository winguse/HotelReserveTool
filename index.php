<?php
session_start();
require_once 'Slim/Slim.php';
require_once 'config.php';
require_once 'libs/index.php';
require_once 'models/index.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim(array(
	'templates.path' => dirname(__FILE__).'/assets/',
	'cookies.path' => APP_BASE_PATH,
	'cookies.domain' => APP_DOMAIN,
	'cookies.lifetime' => '30 days',
	'cookies.secure' => true,
	'cookies.httponly' => true,
	'cookies.encrypt' => true,
	'cookies.secret_key' => APP_SECRET_KEY,
	'debug' => true,
	'mode' => 'development'
));
\Slim\Route::setDefaultConditions(array( 
	'id' => '\\d+'
));
$app->view->setData(array( //inject data into the view object. usually pass data to the view with the Slim application’s `render()` method
	'assets_path' => APP_BASE_PATH.'/assets',
	'api_path' => APP_BASE_PATH.'/api',
	'site_name' => APP_NAME
));

$app->container->singleton('pdo', function () { 
	return new PDO('mysql:dbname='.APP_DB_NAME.';host='.APP_DB_HOST, APP_DB_USER, APP_DB_PASSWORD);
});
$app->container->singleton('mcrypt', function () {
	return new McryptWrapper();
});
$app->container->singleton('dao', function () use ($app) {
	return new Dao($app->pdo);
});
$app->container->singleton('user', function () use ($app) {
	if(!isset($_SESSION['user'])) return null;
	$user = new User();
	foreach (json_decode($_SESSION['user'], true) AS $key => $value) {
		$user->{$key} = $value;
	}
	//var_dump($user);
	return $user;
});

$app->container->singleton('ref_map', function () {
	return array(
		'Hotel' => new ReflectionClass(new Hotel()),
		'User' => new ReflectionClass(new User()),
		'Room' => new ReflectionClass(new Room()),
		'BookInfo' => new ReflectionClass(new BookInfo()),
		'RoomDetail' => new ReflectionClass(new RoomDetail()),
		'ExtendedBookInfo' => new ReflectionClass(new ExtendedBookInfo()),
	);
});

$app->view->setData('username', $app->user ? $app->user->username : '');

$app->get('/', function() use ($app){
	if($app->user){
		if(($app->user->role & PERMISSION_ADMIN) == PERMISSION_ADMIN){
			$app->redirect(APP_BASE_PATH.'/admin');
		}else{
			$app->redirect(APP_BASE_PATH.'/user');
		}
		return;
	}
	$app->render('index.php');
});

$app->get('/logout', function() use ($app){
	unset($_SESSION['user']);
	$app->redirect(APP_BASE_PATH);
});

$app->post('/login', function() use ($app){
	$username = $app->request->post('username');
	$password = $app->request->post('password');
	$filter = new User();
	$filter->username = $username;
	$users = $app->dao->find($filter);
	if(count($users) != 1){
		$app->flash('error', '用户不存在。');
		$app->redirect(APP_BASE_PATH.'/');
	}else{
		$user = $users[0];
		$password = sha1($password.PASSWORD_SALT);
		if($user->password != $password){
			$app->flash('error', '密码错误。');
			$app->redirect(APP_BASE_PATH.'/');
		}else{
			$_SESSION['user'] = json_encode($user);
			if(($app->user->role & PERMISSION_ADMIN) == PERMISSION_ADMIN){
				$app->redirect(APP_BASE_PATH.'/admin');
			}else{
				$app->redirect(APP_BASE_PATH.'/user');
			}
		}
	}
});

$app->get(
	'/results',
	authenticate(PERMISSION_USER),
	function() use ($app){
		$results = $app->dao->find(new ExtendedBookInfo());
		$app->render('results.php', array('results' => $results));
	}
);


$app->group(
	'/admin',
	authenticate(PERMISSION_ADMIN),
	function() use ($app){
		$sections = array('User' => '用户', 'Hotel' => '酒店', 'Room' => '房间', 'ExtendedBookInfo' => '订单');
		$app->get(
			'/',
			function() use ($app){
				$app->redirect(APP_BASE_PATH.'/admin/User');
			}
		);
		
		/// special cases
		$app->post(
			'/User/:id',
			function($id) use($app){
				$user = new User();
				if($id != 0){
					$user->id = $id;
				}
				$user->username = $app->request->post('username');
				$user->password = sha1($app->request->post('password').PASSWORD_SALT);
				$user->email = $app->request->post('email');
				$user->school = $app->request->post('school');
				$user->role = intval($app->request->post('role'));
				$user->meta = $app->request->post('meta');
				if($id != 0){
					$app->dao->update($user);
				}else{
					$app->dao->add($user);
				}
				$app->redirect(APP_BASE_PATH.'/admin/user');
			}
		);
		$app->post(
			'/User/bulk-add',
			function() use($app){
				$user_infos = $app->request->post('user-infos');
				$user_infos = str_replace("\t", ' ', $user_infos);
				$user_infos = str_replace("\r", '', $user_infos);
				$rows = explode("\n", $user_infos);
				$error_message = '';
				foreach($rows as $row){
					$columns = explode(' ', $row);
					$user = new User();
					$user->username = $columns[0];
					$user->password = sha1($columns[1].PASSWORD_SALT);
					$user->email = $columns[2];
					$user->school = $columns[3];
					$user->role = 1;
					$user->meta = '';
					$ret = $app->dao->add($user);
					if($ret != 0){
						$error_message .= '数据库操作失败：' . $row . "。\n";
					}
				}
				if($error_message != '') 
					$app->flash('error', $error_message);
				$app->redirect(APP_BASE_PATH.'/admin/user');
			}
		);

		$app->get(
			'/ExtendedBookInfo/:id',
			function($id) use($app){
				$app->flash('error', '不支持订单修改。');
				$app->redirect(APP_BASE_PATH.'/admin/ExtendedBookInfo');
			}
		);

		$app->get(
			'/ExtendedBookInfo/:id/delete',
			function($id) use($app){
				$item = new BookInfo();
				$item->id = $id;
				$app->dao->delete($item);
				$app->redirect(APP_BASE_PATH.'/admin/ExtendedBookInfo');
			}
		);

		// general cases for object operation
		$app->get(
			'/:object_type',
			function($object_type) use ($app, $sections){
				if(!isset($app->ref_map[$object_type])){
					$app->redirect(APP_BASE_PATH.'/admin');
					return;
				}
				$ref = $app->ref_map[$object_type];
				$filter = $ref->newInstance();
				$items = $app->dao->find($filter);
				$app->render('admin/index.php', array('sections' => $sections, 'active_section' => $ref->getName(), 'items' => $items));
			}
		);
		$app->get(
			'/:object_type/:id',
			function($object_type, $id) use ($app){
				if(!isset($app->ref_map[$object_type])){
					$app->redirect(APP_BASE_PATH.'/admin');
					return;
				}
				$ref = $app->ref_map[$object_type];
				$item = $ref->newInstance();
				$item->id = $id;
				if($id != 0){
					$items = $app->dao->find($item);
					if(count($items) != 1){
						$app->flash('error', '未找到对象。');
						$app->redirect(APP_BASE_PATH.'/admin/'.$ref->getName());
						return;
					}
					$item = $items[0]; 
				}
				$app->render('admin/'.$ref->getName().'_form.php', get_item_parameters_refine($object_type, array('item' => $item), $app));
			}
		);
		$app->post(
			'/:object_type/:id',
			function($object_type, $id) use ($app){
				if(!isset($app->ref_map[$object_type])){
					$app->redirect(APP_BASE_PATH.'/admin');
					return;
				}
				$ref = $app->ref_map[$object_type];
				$item = $ref->newInstance();
				if($id != 0){
					$item->id = $id;
					$items = $app->dao->find($item);
					if(count($items) != 1){
						$app->flash('error', '未找到对象。');
						$app->redirect(APP_BASE_PATH.'/admin/'.$object_type);
						return;
					}
					$item = $items[0]; 
				}
				$properties = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
				foreach($properties as $property){
					if($property->getName() == 'id') continue;
			    	$property->setValue($item, $app->request->post($property->getName()));
				}
				if($id == 0){
					$ret = $app->dao->add($item);
				}else{
					$ret = $app->dao->update($item);
				}
				if($ret != 0){
					$app->flash('error', '数据库操作失败。');
				}
				$app->redirect(APP_BASE_PATH.'/admin/'.$ref->getName());
			}
		);
		$app->get(
			'/:object_type/:id/delete',
			function($object_type, $id) use($app){
				if(!isset($app->ref_map[$object_type])){
					$app->redirect(APP_BASE_PATH.'/admin');
					return;
				}
				$ref = $app->ref_map[$object_type];
				$item = $ref->newInstance();
				$item->id = $id;
				$app->dao->delete($item);
				$app->redirect(APP_BASE_PATH.'/admin/'.$ref->getName());
			}
		);
		
	}
);


$app->group(
	'/user',
	authenticate(PERMISSION_USER),
	function() use ($app){
		$app->get(
			'/',
			function() use ($app){
				$hotels = $app->dao->find(new Hotel());
				$rooms = $app->dao->find(new RoomDetail());
				$book_info_filter = new BookInfo();
				$book_info_filter->user_id = $app->user->id;
				$book_infos = $app->dao->find($book_info_filter);
				
				$room_id2user_booked_count = array();
				foreach($book_infos as $book_info){
					if(!isset($room_id2user_booked_count[$book_info->room_id])){
						$room_id2user_booked_count[$book_info->room_id] = 0;
					}
					$room_id2user_booked_count[$book_info->room_id] += $book_info->book_count;
				}
				
				$hotel_id2rooms = array();
				foreach($rooms as $room){
					if(!isset($hotel_id2rooms[$room->hotel_id])){
						$hotel_id2rooms[$room->hotel_id] = array();
					}
					if(isset($room_id2user_booked_count[$room->id])){
						$room->user_booked_count = $room_id2user_booked_count[$room->id];
					}else{
						$room->user_booked_count = 0;
					}
					if(!$room->booked) $room->booked = 0;
					$hotel_id2rooms[$room->hotel_id][] = $room;
				}
				
				foreach($hotels as $hotel){
					$hotel->rooms = $hotel_id2rooms[$hotel->id];
				}
				
				$_SESSION['token'] = $token = rand();
				
				$start_date = $end_date = 0;
				$meta = $app->user->getMetaArray();
				$start_date = $meta['start_date'];
				$end_date = $meta['end_date'];
				
				$app->render('user/index.php', array('hotels' => $hotels, 'token' => $token, 'start_date' => $start_date, 'end_date' => $end_date));
			}
		);
		
		$app->get(
			'/plan/:name/:timestamp/:token',
			function($name, $timestamp, $token) use ($app){
				$error_message = '';
				if($_SESSION['token'] == $token){
					$_SESSION['token'] = rand();
					$meta = $app->user->getMetaArray();
					if($name == 'start_date'){
						if($timestamp >= $meta['end_date']){
							$error_message = '入住时间不能晚于离店时间。';
						}
					}elseif($name == 'end_date'){
						if($timestamp <= $meta['start_date']){
							$error_message = '离店时间不能早于入住时间。';
						}
					}else{
						$error_message = '非法请求。';
					}
					if($error_message == ''){
						$meta[$name] = $timestamp;
						$app->user->setMetaArray($meta);
						$app->dao->update($app->user);
						$_SESSION['user'] = json_encode($app->user);
					}
				}else{
					$error_message = '非法请求，请勿多开页面。';
				}
				if($error_message != ''){
					$app->flash('error', $error_message);
				}
				$app->redirect(APP_BASE_PATH.'/user');
			}
		)->conditions(array('timestamp' => '\d+'));
		
		$app->get(
			'/room/:room_id/book/:count/:token',
			function($room_id, $count, $token) use ($app){
				$error_message = '';
				
				if($count < 0){
					$error_message = '预订数量非法！';
				}else{
					$lock_indentifier = sem_get(ftok(__FILE__,'m'));
					sem_acquire($lock_indentifier);
					
					if($_SESSION['token'] == $token){
						$_SESSION['token'] = rand();
						$book_info_filter = new BookInfo();
						$book_info_filter->user_id = $app->user->id;
						$book_info_filter->room_id = $room_id;
						$book_infos = $app->dao->find($book_info_filter);
						$booked_count = 0;
						foreach($book_infos as $book_info){
							$booked_count += $book_info->book_count;
						}
						
						$room_detail = new RoomDetail();
						$room_detail->id = $room_id;
						$room_details = $app->dao->find($room_detail);
						if(count($room_details) != 1){
							$error_message = '房间信息有误，找到 ' . count($room_details) . ' 个这样的房间。';
						}else{
							$room_detail = $room_details[0];
							$left = $room_detail->total - $room_detail->booked + $booked_count - $count;
							if($left < 0){
								$error_message = '剩余房间不足，预订失败！';
							}else{
								$ret = 0;
								foreach($book_infos as $book_info){
									$ret += $app->dao->delete($book_info);
								}
								if($ret != 0){
										$error_message = '删除老预订信息时出错，预订失败！';
								}elseif($count > 0){
									$book_info = new BookInfo();
									$book_info->user_id = $app->user->id;
									$book_info->room_id = $room_id;
									$book_info->book_count = $count;
									$ret = $app->dao->add($book_info);
									if($ret != 0){
										$error_message = '预订信息没有正确写入数据库，预订失败！';
									}
								}
							}
						}
					
					}else{
						$error_message = '非法请求，请勿多开页面！';
					}
					sem_release($lock_indentifier);
				}
				
				if($error_message != ''){
					$app->flash('error', $error_message);
				}
				
				$app->redirect(APP_BASE_PATH.'/user');
			}
		)->conditions(array('room_id' => '\d+', 'count' => '\d+'));
	}
);



function get_item_parameters_refine($object_type, $arr, $app){
	if($object_type == 'Room'){
		$hotels = $app->dao->find(new Hotel());
		$arr['hotels'] = $hotels;
	}
	return $arr;
}

$app->run();
