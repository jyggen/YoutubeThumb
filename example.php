<?php
// Include class file.
require 'youtube_thumbnail.php';

// Set up a basic config.
$config = array(
	'output' => array(
		'format' => 'png',    // which format save() should use.
		'path'   => 'public/' // Where save() should put the image.
	),
	//'format' => 'hqdefault' // Which thumbnail to fetch. Default is "best available", so let's use that.
);

// Init a new Youtube_Thumbnail object.
$thumb = new Youtube_Thumbnail('gzDS-Kfd5XQ', $config);

/**
 * Quick and easy method fetch and save the image to disk.
 * $proxy->fetch()->save();
 * We want this image in grayscale though, let's do that instead!
 */

// Fetch the image.
$thumb->fetch();

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