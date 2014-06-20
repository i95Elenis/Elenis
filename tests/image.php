
<?php
function LoadJpeg($imgname)
{
    /* Attempt to open */
    $im = @imagecreatefromjpeg($imgname);

    /* See if it failed */
    if(!$im)
    {
        echo "ttset";
        /* Create a black image */
        $im  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);

        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

        /* Output an error message */
        imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}

//header('Content-Type: image/jpeg');

//$img = LoadJpeg('http://elenidev2.vanwestmedia.com/media/custom_product_preview/quote/Bbirthdaywishes_1.jpg');
$img = LoadJpeg('/var/www/media/custom_product_preview/quote/Winter_35.jpg');

imagejpeg($img);
imagedestroy($img);
?>
