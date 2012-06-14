<?php
// Include class file.
require 'youtube_thumbnail.php';

// Set up a basic config.
$config = array(
	'output_format'  => 'png',      // which format save() should use.
	'output_path'    => 'images/',  // Where save() should put the image.
	//'input_format' => 'hqdefault' // Which thumbnail to fetch. Default is "best available", so let's use that.
);

// Init a new Youtube_Thumbnail object.
$thumb = new Youtube_Thumbnail('83fJ7hlZkpA', $config);

/**
 * A wuick and easy method to fetch and save the image to disk.
 * $proxy->load()->save();
 * However, we want to incrase the contrast, smooth and resize it, so let's do that instead!
 */

// Fetch the image.
$thumb->load();

// Get the image object.
$image = $thumb->getImgData();

// Increase the contrast and smooth it.
imagefilter($image, IMG_FILTER_CONTRAST, -13.37);
imagefilter($image, IMG_FILTER_SMOOTH, 1337);

// Resize and crop it down to 168x108.
$thumb_width     = 168;
$thumb_height    = 108;
$width           = imagesx($image);
$height          = imagesy($image);
$original_aspect = $width / $height;
$thumb_aspect    = $thumb_width / $thumb_height;

if($original_aspect >= $thumb_aspect) {

	$new_height = $thumb_height;
	$new_width  = $width / ($height / $thumb_height);

} else {

	$new_width  = $thumb_width;
	$new_height = $height / ($width / $thumb_width);

}

$new_image = imagecreatetruecolor($thumb_width, $thumb_height);

imagecopyresampled($new_image,
                   $image,
                   0 - ($new_width - $thumb_width) / 2,
                   0 - ($new_height - $thumb_height) / 2,
                   0, 0,
                   $new_width, $new_height,
                   $width, $height);

// Set the image to our new grayscale image!
$thumb->setImgData($new_image);

// Save the image to disk.
$thumb->save();