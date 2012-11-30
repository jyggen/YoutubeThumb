# YoutubeThumb

A PHP class created to help you load, edit and save thumbnails from videos on Youtube.

[Find YoutubeThumb on Packagist/Composer](https://packagist.org/packages/jyggen/youtubethumb)

## Examples

```php
use jyggen\YoutubeThumb as Thumb;

$config = array(
  'output_format'  => 'png',      // which output format to use.
  'output_path'    => 'images/',  // where to output the images.
  'input_format'   => 'hqdefault' // which thumbnail to fetch, defaults to null (aka. "best available").
);

// Create a new object passing the Youtube ID and the config.
$thumb = new Thumb('83fJ7hlZkpA', $config);
