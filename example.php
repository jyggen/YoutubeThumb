<?php
// Include class file.
require 'youtube_thumbnail.php';

// Set up a basic config.
$config = array(
	'output_format' => 'png',    // which format save() should use.
	'output_path'   => 'images/' // Where save() should put the image.
	//'input_format' => 'hqdefault' // Which thumbnail to fetch. Default is "best available", so let's use that.
);

// Init a new Youtube_Thumbnail object.
$thumb = new Youtube_Thumbnail('gzDS-Kfd5XQ', $config);

/**
 * Quick and easy method fetch and save the image to disk.
 * $proxy->load()->save();
 * We want this image in grayscale though, let's do that instead!
 */

// Fetch the image.
$thumb->load();

// Get the image data.
$image = imagecreatefromstring($thumb->getImgData());

// Covert to grayscale.
imagefilter($image, IMG_FILTER_GRAYSCALE);

/**
 * Retrieve the data of the new image. There's no
 * easy way to retrieve the data string. One way
 * is to tell GD to output the image, then use
 * PHP buffering to capture it to a string.
 */
ob_start();
imagepng($image);
$imgdata = ob_get_contents();
ob_end_clean();

// Set the image data to our new grayscale image!
$thumb->setImgData($imgdata);

// Save the image to disk.
$thumb->save();