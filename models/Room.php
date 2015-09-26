<?php

class Room{
	public $id;
	public $hotel_id;
	public $breakfast;
	public $internet;
	public $type_name;
	public $volume;
	public $total;
	public $price;
	public $description;
}

class RoomDetail extends Room{
	public $booked;
}