# YoutubeThumb

A PHP class created to help you load, edit and save thumbnails from videos on Youtube.

[Find YoutubeThumb on Packagist/Composer](https://packagist.org/packages/jyggen/youtubethumb)

## Examples

```php
use jyggen\Youtube\Thumbnail;

// Load a thumbnail and save it to disk.
Thumbnail::forge('83fJ7hlZkpA')->save('my_path/');

// Maybe we wanna do some changes aswell. Retrieve the previously forged instance.
$thumb = Thumbnail::instance('83fJ7hlZkpA');

// Get the Thumbnail's GD resource.
$data  = $thumb->getData();

// Create a new GD resource and copy a portion of the thumbnail into our new image.
$dest = imagecreatetruecolor(80, 40);
imagecopy($dest, $data, 0, 0, 20, 13, 80, 40);

// Set our old thumbnail to the new resized one.
$thumb->setData($dest);

// Oh shit, did we fudge up something? Reset the image to its original state.
$thumb->reset();

// We want to save the image again, but with a different name and extension.
$thumb->setName('my_awesome_thumbnail');
$thumb->save('my_path/', 'gif');
```
## Thumbnail object

### Static Methods

* __forge(string $youtubeId)__  
Forge a new Thumbnail instance.
* __instance(string $youtubeId)__  
Retrieve an existing Thumbnail instance.

### Methods

* __reset()__  
Reset the thumbnail to its original state.
* __save(string $path, string $extension = 'png')__  
Save the thumbnail to disk.
* __getData()__  
Get the thumbnail's GD resource.
* __setData(resource $data)__  
Set the thumbnail's GD resource.
* __getName()__  
Get the output name without extension.
* __setName(string $name)__  
Set the output name without extension.
