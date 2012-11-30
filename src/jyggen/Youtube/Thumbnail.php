<?php
/**
* A PHP class created to help you load, edit and save thumbnails from videos on Youtube.
*
* @package YoutubeThumb
* @version 2.0
* @author Jonas Stendahl
* @license MIT License
* @copyright 2012 Jonas Stendahl
* @link http://github.com/jyggen/YoutubeThumb
*/

namespace jyggen\Youtube;

class Thumbnail
{

    protected static $_instances;
    protected $data, $id, $name, $source;

    public static function forge($youtubeId)
    {

        // Check if there's already an instance with that Youtube ID.
        if (isset(static::$_instances[$youtubeId])) {
            throw new Exception\DuplicateInstanceException('You can not instantiate two thumbnails using the same Youtube ID "'.$youtubeId.'".');
        }

        // Create a Thumbnail object and set its Youtube ID.
        $instance = new Thumbnail($youtubeId);

        // Store the instance.
        static::$_instances[$youtubeId] = $instance;

        // Return it.
        return $instance;

    }

    public static function instance($youtubeId)
    {

        // Return the instance if available, otherwise false.
        return (isset(static::$_instances[$youtubeId])) ? static::$_instances[$youtubeId] : false;

    }

    public function __construct($youtubeId)
    {

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $youtubeId) === 0) {
            throw new Exception\InvalidIdException('Invalid ID "'.$youtubeId.'".');
        }

        $this->id   = $youtubeId;
        $this->name = bin2hex($this->id);

        $this->retrieve();
        $this->reset();

    }

    public function reset()
    {

        $this->data = imagecreatefromstring($this->source);

    }

    public function save($path, $extension='png')
    {

        $name = $path.$this->name.'.'.$extension;

        switch($extension) {
            case 'gif':
                $success = imagegif($this->data, $name);
                break;
            case 'jpg':
            case 'jpeg':
                $success = imagejpeg($this->data, $name);
                break;
            case 'png':
                $success = imagepng($this->data, $name);
                break;
            default:
                throw new Exception\UnknownFormatException('Unknown image format "'.$extension.'".');
                break;
        }

        return $success;

    }

    public function getData()
    {

        return $this->data;

    }

    public function setData($data)
    {

        $this->data = $data;

    }

    public function getName()
    {

        return $this->name;

    }

    public function setName($name)
    {

        $this->name = $name;

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

    protected function retrieve()
    {

        $request = $this->request('http://gdata.youtube.com/feeds/api/videos/'.$this->id.'?v=2&alt=json');

        if ($request['info']['http_code'] == 404) {
            throw new Exception\InvalidIdException('Clip "'.$this->id.'" doesn\'t exist.');
        }

        $data   = json_decode($request['data'], true);
        $thumbs = array();

        foreach ($data['entry']['media$group']['media$thumbnail'] as $img) {

            $thumbs[$img['yt$name']] = array(
                'url'    => $img['url'],
                'height' => $img['height'],
                'width'  => $img['width'],
                'size'   => $img['height']*$img['width'],
            );

        }

        usort($thumbs, function($a, $b){
            return $a['size'] - $b['size'];
        });

        $request      = $this->request($thumbs[count($thumbs)-1]['url']);
        $this->source = $request['data'];

    }

}
