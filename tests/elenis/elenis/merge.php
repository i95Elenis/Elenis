<?php
$src = imagecreatefrompng('image1.png');
$dest = imagecreatefromjpeg('image2.jpg');

imagealphablending($src , true);
imagesavealpha($src , false);

$thumb = imagecreatetruecolor(344, 258);
//$thumb = imagecreatetruecolor(240, 170);

list($width, $height) = getimagesize('image2.jpg');
// Resize
imagecopyresized($thumb, $dest, 0, 0, 0, 0, 329.74093264248705, 247.30569948186528, $width, $height);
//header('Content-Type: image/jpeg');
//imagepng($thumb);

//imagealphablending($thumb, true);
//imagesavealpha($thumb, false);
$white = imagecolorallocate($thumb, 255, 255, 255);
imagecolortransparent($thumb, $color);

imagealphablending($thumb, false);
imagesavealpha($thumb, false);

imagecopymerge($src, $thumb, 0, 0, -54.637305699481864, -55.59585492227979, 329.74093264248705, 247.30569948186528, 55); //have to play with these numbers for it to work for you, etc.

header('Content-Type: image/jpeg');
imagepng($src);

imagedestroy($dest);
imagedestroy($src);


?>