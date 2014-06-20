<?php
$dest = imagecreatefrompng('image1.png');
$src = imagecreatefromjpeg('image2.jpg');

// Copy
imagecopy($dest, $src, 0, 0, 20, 13, 350, 180);

// Output and free from memory
header('Content-Type: image/gif');
imagegif($dest);

imagedestroy($dest);
imagedestroy($src);
?>
