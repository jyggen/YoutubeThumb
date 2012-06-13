<?php
class Youtube_Thumbnail
{
	
	protected $config, $id;
	
	public function __construct($id, array $config=array()) {
		
		if(!preg_match('/[a-zA-Z0-9_-]{11}/', $id)) {
			throw new Exception('Invalid ID!');		
		}
		
		$this->id     = $id;
		$this->config = $config;
		
	}
	
	public function fetch() {
	
	}

}

#http://gdata.youtube.com/feeds/api/videos/gzDS-Kfd5XQ?v=2&prettyprint=true&alt=json