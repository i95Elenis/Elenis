<?php
ini_set('display_errors', 1);
$base = new Imagick('image1.png');
$over = new Imagick('image2.jpg');

// Setting same size for all images
$over->resizeImage(344, 258, Imagick::FILTER_LANCZOS, 1);

// Copy opacity mask
//$base->compositeImage($mask, Imagick::COMPOSITE_DSTIN, 0, 0, Imagick::CHANNEL_ALPHA);

// Add overlay
$base->compositeImage($over, Imagick::COMPOSITE_DEFAULT, 25,55);
$base->flattenImages(); 
$base->writeImage('output.png');
header("Content-Type: image/png");

echo $base;
?>