<?php
ini_set('display_errors', 1);
$base = new Imagick('image2.jpg');
$over = new Imagick('image1.png');
$base->resizeImage(329, 247, Imagick::FILTER_LANCZOS, 1);
$base->compositeImage($over, $over->getImageCompose(), -19,-64);
$over2 = new Imagick('image1.png');
$over2->compositeImage($base, $base->getImageCompose(), 19,64);
$over2->writeImage('output.png');
header("Content-Type: image/png");
echo $over2;
?>