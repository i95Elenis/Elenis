<?php

ini_set('display_errors', 1);

$base = '/chroot/home/elenisco/elenis.com/html/tests/elenis/elenis/image2.jpg';
$over = '/chroot/home/elenisco/elenis.com/html/tests/elenis/elenis/image1.png';
$over2 = '/chroot/home/elenisco/elenis.com/html/tests/elenis/elenis/image1.png';

system('convert '. $base . ' -resize 242x160 '. '/chroot/home/elenisco/elenis.com/html/tests/elenis/elenis/image3.jpg');
system('composite -geometry -65-29 '. $over. ' '. '/chroot/home/elenisco/elenis.com/html/tests/elenis/elenis/image3.jpg' .' '.  '/chroot/home/elenisco/elenis.com/html/tests/elenis/elenis/image4.jpg');
system('composite -geometry +65+29 '. '/chroot/home/elenisco/elenis.com/html/tests/elenis/elenis/image4.jpg'. ' '. $over2 .' '.  '/chroot/home/elenisco/elenis.com/html/tests/elenis/output109.png');

header("Content-Type: image/png");
echo file_get_contents('/chroot/home/elenisco/elenis.com/html/tests/elenis/output109.png');
?>