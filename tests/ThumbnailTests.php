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

class ThumbnailTests extends \PHPUnit_Framework_TestCase
{

    public function testForge()
    {

        $this->assertInstanceof('jyggen\\Youtube\\Thumbnail', Thumbnail::forge('K7IwAErGJQ8'));
        $this->setExpectedException('jyggen\\Youtube\\Exception\\DuplicateInstanceException', 'You can not instantiate');
        Thumbnail::forge('K7IwAErGJQ8');

    }

    public function testInstance()
    {

        $this->assertInstanceof('jyggen\\Youtube\\Thumbnail', Thumbnail::instance('K7IwAErGJQ8'));
        $this->assertSame(Thumbnail::instance('K7IwAErGJQ8'), Thumbnail::instance('K7IwAErGJQ8'));
        $this->assertFalse(Thumbnail::instance('ZHIhuHNRDEs'));

    }

    public function testConstructException()
    {

        $this->setExpectedException('jyggen\\Youtube\\Exception\\InvalidIdException', 'Invalid ID');
        Thumbnail::forge('this1sN0tAYOuTub3ID');

    }

    public function testRetrieveException()
    {

        $this->setExpectedException('jyggen\\Youtube\\Exception\\InvalidIdException', 'doesn\'t exist.');
        Thumbnail::forge('AaBbCcDdEez');

    }

    public function testGetData()
    {

        $thumb = Thumbnail::instance('K7IwAErGJQ8');
        $data  = $thumb->getData();

        $this->assertEquals('gd', get_resource_type($data));

    }

    public function testSetData()
    {

        $thumb = Thumbnail::instance('K7IwAErGJQ8');
        $data  = $thumb->getData();
        $new   = imagecreatetruecolor(80, 40);

        $thumb->setData($new);

        $data = $thumb->getData();

        $this->assertEquals('gd', get_resource_type($data));

        $expectedHeight = imagesy($new);
        $expectedWidth  = imagesx($new);
        $actualHeight   = imagesy($data);
        $actualWidth    = imagesx($data);

        $this->assertEquals($expectedHeight, $actualHeight);
        $this->assertEquals($expectedWidth, $actualWidth);

    }

    public function testSetDataByReference()
    {

        $thumb = Thumbnail::instance('K7IwAErGJQ8');
        $data  = $thumb->getData();
        $new   = imagecreatetruecolor(80, 40);
        $data  = $thumb->getData();

        $this->assertEquals('gd', get_resource_type($data));

        $expectedHeight = imagesy($new);
        $expectedWidth  = imagesx($new);
        $actualHeight   = imagesy($data);
        $actualWidth    = imagesx($data);

        $this->assertEquals($expectedHeight, $actualHeight);
        $this->assertEquals($expectedWidth, $actualWidth);

    }

    public function testGetName()
    {

        $thumb = Thumbnail::instance('K7IwAErGJQ8');
        $name  = bin2hex('K7IwAErGJQ8');

        $this->assertEquals($name, $thumb->getName());

    }

    public function testSetName()
    {

        $thumb = Thumbnail::instance('K7IwAErGJQ8');
        $name  = 'supermegafoxyawesomehot';

        $thumb->setName($name);
        $this->assertEquals($name, $thumb->getName());

    }

    public function testReset()
    {

        $thumb = Thumbnail::instance('K7IwAErGJQ8');

        $data = $thumb->getData();

        $previousHeight = imagesy($data);
        $previousWidth  = imagesx($data);

        $thumb->reset();

        $data = $thumb->getData();

        $this->assertEquals('gd', get_resource_type($data));

        $newHeight = imagesy($data);
        $newWidth  = imagesx($data);

        $this->assertNotEquals($previousHeight, $newHeight);
        $this->assertNotEquals($previousWidth, $newWidth);

    }

    public function testSave()
    {

        if(!file_exists('our_img_path')) { mkdir('our_img_path'); }

        $thumb = Thumbnail::instance('K7IwAErGJQ8');

        $this->assertTrue($thumb->save('./our_img_path/'));
        $this->assertTrue(file_exists('./our_img_path/'.$thumb->getName().'.png'));

        unlink('./our_img_path/'.$thumb->getName().'.png');

        $this->assertTrue($thumb->save('./our_img_path/', 'png'));
        $this->assertTrue(file_exists('./our_img_path/'.$thumb->getName().'.png'));

        unlink('./our_img_path/'.$thumb->getName().'.png');

        $this->assertTrue($thumb->save('./our_img_path/', 'gif'));
        $this->assertTrue(file_exists('./our_img_path/'.$thumb->getName().'.gif'));

        unlink('./our_img_path/'.$thumb->getName().'.gif');

        $this->assertTrue($thumb->save('./our_img_path/', 'jpg'));
        $this->assertTrue(file_exists('./our_img_path/'.$thumb->getName().'.jpg'));

        unlink('./our_img_path/'.$thumb->getName().'.jpg');

        $this->assertTrue($thumb->save('./our_img_path/', 'jpeg'));
        $this->assertTrue(file_exists('./our_img_path/'.$thumb->getName().'.jpeg'));

        unlink('./our_img_path/'.$thumb->getName().'.jpeg');

        rmdir('our_img_path');

    }

    public function testSaveInvalidFormat()
    {

        $thumb = Thumbnail::instance('K7IwAErGJQ8');

        $this->setExpectedException('jyggen\\Youtube\\Exception\\UnknownFormatException', 'image format');
        $thumb->save('./our_img_path/', 'lol');

    }

    public function testSaveFailed()
    {

        $thumb = Thumbnail::instance('K7IwAErGJQ8');

        $this->setExpectedException('PHPUnit_Framework_Error', 'No such file');
        $this->assertFalse($thumb->save('./our_img_path/'));

    }

}
