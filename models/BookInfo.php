<?php

class BookInfo{
	public $id;
	public $user_id;
	public $room_id;
	public $book_count;
}


class ExtendedBookInfo extends BookInfo{
	public $username;
	public $school;
	public $hotel_name;
	public $room_type;
	public $price;
	public $volume;
	public $meta;
}