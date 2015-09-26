<?php
class User{
	public $id;
    public $username;
    public $password;
    public $email;
    public $school;
    public $role;
    public $meta;
    
    public function getMetaArray(){
    	if($this->meta){
    		return json_decode($this->meta, true);
    	}
    	return array(
    		'start_date' => 0,
    		'end_date' => 0x7fffffff
    	);
    }
    
    public function setMetaArray($meta_array){
    	$this->meta = json_encode($meta_array);
    }
}