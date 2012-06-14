<?php
class Youtube_Thumbnail
{

	protected $data, $id;
	protected $config = array('output_format' => 'png', 'output_path' => './images/', 'input_format' => null);

	public function __construct($id, array $config=array()) {

		if(!preg_match('/[a-zA-Z0-9_-]{11}/', $id)) {
			throw new Exception('Invalid ID!');
		}

		$this->id     = $id;
		$this->config = array_merge($this->config, $config);

	}

	public function load() {

		$request = $this->request('http://gdata.youtube.com/feeds/api/videos/'.$this->id.'?v=2&alt=json');

		if($request['info']['http_code'] == 404) {
			throw new Exception('Clip '.$this->id.' doesn\'t exist!');
		}

		$data   = json_decode($request['data'], true);
		$thumbs = array();
		$image  = null;
		$format = $this->getConfig('input_format');

		foreach($data['entry']['media$group']['media$thumbnail'] as $img) {

			$thumbs[$img['yt$name']] = array(
				'url'    => $img['url'],
				'height' => $img['height'],
				'width'  => $img['width'],
				'size'   => $img['height']*$img['width'],
			);

		}

		if($thumbs != null && array_key_exists($format, $thumbs)) {

			$image = $tumbs[$format]['url'];

		} else {

			usort($thumbs, array($this, 'cmp_by_size'));

			$key   = count($thumbs)-1;
			$image = $thumbs[$key]['url'];

		}

		$request    = $this->request($image);
		$this->data = imagecreatefromstring($request['data']);

		return $this;

	}

	public function save() {

		$path = $this->getConfig('output_path');
		$ext  = $this->getConfig('output_format');
		$name = $path.bin2hex($this->id).'.'.$ext;

		switch($ext) {
			case 'bmp':
				$func = 'imagebmp';
				break;
			case 'gif':
				$func = 'imagegif';
				break;
			case 'jpg':
			case 'jpeg':
				$func = 'imagejpeg';
				break;
			case 'png':
				$func = 'imagepng';
				break;
		}

		$func($this->data, $name);

		return $this;

	}

	public function getImgData() {

		return $this->data;

	}

	public function setImgData($data) {

		$this->data = $data;

	}

	protected function getConfig($item) {

		if (array_key_exists($item, $this->config)) {
			return $this->config[$item];
		} else {
			throw new Exception('Couldn\'t find "'.$item.'" in configuration!');
		}

	}

	protected function request($url) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return array(
			    'data' => $data,
			    'info' => $info,
			   );

	}

	protected function cmp_by_size($a, $b) {

		return $a['size'] - $b['size'];

	}

}