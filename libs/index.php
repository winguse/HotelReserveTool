<?php
require_once dirname(__FILE__).'/../config.php';

function authenticate($role = PERMISSION_NONE) {
    return function () use ($role) {
    	if($role == PERMISSION_NONE) return;
    	$app = \Slim\Slim::getInstance();
    	if($app->user == null || ($app->user->role & $role) != $role){
            $app->flash('error', 'Not authenticated');
            //$app->render('/index.php',array('role'=>$role));
            $app->redirect(APP_BASE_PATH);
    	}
    };
};

function html_encode($str){
	return htmlentities($str, ENT_QUOTES);
}

function admin_table_data_refine_hooker($data, $data_field, $data_type){
	if($data_field == 'meta' && ($data_type == 'ExtendedBookInfo' || $data_type == 'User')){
		return print_user_meta($data);
	}
	return $data;
}

function print_user_meta($meta_json){
	if(!$meta_json) return '';
	$meta = json_decode($meta_json, true);
	return date('Y-m-d', $meta['start_date']) . ' => '. date('Y-m-d', $meta['end_date']);
}

class McryptWrapper {
	private $td;
	private $key;
	private $iv;

	public function __construct(){
		$this->key = APP_SECRET_KEY;
		$this->td = mcrypt_module_open('des', '', 'ecb', '');
		$this->key = substr($this->key, 0, mcrypt_enc_get_key_size($this->td));
		$iv_size = mcrypt_enc_get_iv_size($this->td);
		$this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	}

	public function encrypt($plain_text){
		mcrypt_generic_init($this->td, $this->key, $this->iv);
		$result = mcrypt_generic($this->td, $plain_text);
		mcrypt_generic_deinit($this->td);
		return base64_encode($result);
	}

	public function decrypt($entrypted_text){
		mcrypt_generic_init($this->td, $this->key, $this->iv);
		$result = mdecrypt_generic($this->td, base64_decode($entrypted_text));
		mcrypt_generic_deinit($this->td);
		return $result;
	}

	function __destruct(){
		mcrypt_module_close($this->td);
	}
}