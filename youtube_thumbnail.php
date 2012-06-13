<?php
class Youtube_Thumbnail
{

	protected $data, $id;
	protected $config = array();

	public function __construct($id, array $config=array()) {

		if(!preg_match('/[a-zA-Z0-9_-]{11}/', $id)) {
			throw new Exception('Invalid ID!');
		}

		$this->id     = $id;
		$this->config = array_merge($this->config, $config);

	}

	public function load() {

		$method = $this->getConfig('request_method');

		if($method == 'curl') {
			$this->loadCurl();
		} elseif($method == 'file_get_contents') {
			$this->loadFileGetContents();
		} else {
			throw new Exception('Invalid Request Method!');
		}

	}

	public function save() {

	}

	public function getImgData() {

		return $this->data;

	}

	public function setImgData($data) {

		$this->data = $data;

	}

	protected function getConfig($item) {

		if (isset($this->config[$item])) {
			return $this->config[$item];
		} else {
			throw new Exception('Couldn\'t find "'.$item.'" in configuration!');
		}

	}

}

#http://gdata.youtube.com/feeds/api/videos/gzDS-Kfd5XQ?v=2&prettyprint=true&alt=json