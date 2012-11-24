<?php
/**
* A PHP class created to help you load, edit and save thumbnails from videos on Youtube.
*
* @package YoutubeThumb
* @version 1.0
* @author Jonas Stendahl
* @license MIT License
* @copyright 2012 Jonas Stendahl
* @link http://github.com/jyggen/YoutubeThumb
*/

namespace jyggen;

class YoutubeThumb
{

    protected $data, $id;
    protected $config = array(
        'output_format' => 'png',
        'output_path'   => './images/',
        'input_format'  => null
    );

    public function __construct($id, array $config=array())
    {

        if (!preg_match('/[a-zA-Z0-9_-]{11}/', $id)) {
            throw new Exception('Invalid ID!');
        }

        $this->id     = $id;
        $this->config = array_merge($this->config, $config);

    }

    public function load()
    {

        $url     = 'http://gdata.youtube.com/feeds/api/videos/%s?v=2&alt=json';
        $request = $this->request(sprintf($url, $this->id));

        if ($request['info']['http_code'] == 404) {
            throw new Exception('Clip '.$this->id.' doesn\'t exist!');
        }

        $data   = json_decode($request['data'], true);
        $thumbs = array();
        $image  = null;
        $format = $this->getConfig('input_format');

        foreach ($data['entry']['media$group']['media$thumbnail'] as $img) {

            $thumbs[$img['yt$name']] = array(
                'url'    => $img['url'],
                'height' => $img['height'],
                'width'  => $img['width'],
                'size'   => $img['height']*$img['width'],
            );

        }

        if ($thumbs != null && array_key_exists($format, $thumbs)) {

            $image = $tumbs[$format]['url'];

        } else {

            usort($thumbs, array($this, 'cmpBySize'));

            $key   = count($thumbs)-1;
            $image = $thumbs[$key]['url'];

        }

        $request    = $this->request($image);
        $this->data = imagecreatefromstring($request['data']);

        return $this;

    }

    public function save()
    {

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

    public function getImgData()
    {

        return $this->data;

    }

    public function setImgData($data)
    {

        $this->data = $data;

    }

    protected function getConfig($item)
    {

        if (array_key_exists($item, $this->config)) {
            return $this->config[$item];
        } else {
            throw new Exception('Couldn\'t find "'.$item.'" in configuration!');
        }

    }

    protected function request($url)
    {

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

    protected function cmpBySize($a, $b)
    {

        return $a['size'] - $b['size'];

    }

}
