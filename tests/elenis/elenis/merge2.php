<?php
ini_set('gd.jpeg_ignore_warning', 1);
$src = imagecreatefrompng('image1.png');
$background = imagecolorallocate($src, 0, 0, 0);
// removing the black from the placeholder
imagecolortransparent($src, $background);
imagealphablending($src, false);
imagesavealpha($src, true);
//imagefill($src,0,0,$background); 

$dest = imagecreatefromjpeg('image2.jpg');

header('Content-Type: image/png');



$thumb = imagecreatetruecolor(370, 370);
list($width, $height) = getimagesize('image2.jpg');

imagecopyresized($thumb, $dest, 0, 0, 0, 0, 370, 370, $width, $height);
imagecopymerge($thumb, $src, 0, 0, 0, 0, 370, 370, 100); //have to play with these numbers for it to work for you, etc.
header('Content-Type: image/png');
imagepng($thumb);
imagedestroy($dest);
imagedestroy($src);
imagedestroy($thumb);
exit;



?>