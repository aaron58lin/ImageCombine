<?php
// Set default each image block width, height, margin by px
$tileWidth = 250;
$tileHeight = 250;
$pxMargin = 2;

// Get img urls from ajax post
$img[1] = $_POST['img1'];
$img[2] = $_POST['img2'];
$img[3] = $_POST['img3'];
$img[4] = $_POST['img4'];
$img[5] = $_POST['img5'];
$img[6] = $_POST['img6'];
$img[7] = $_POST['img7'];
$img[8] = $_POST['img8'];
$img[9] = $_POST['img9'];


$j = 0;

for ($i = 1; $i < 10; $i++) {
    if ($img[$i]) {
        $srcImagePaths[$j] = $img[$i];
        $j++;
    }
}

$numberOfTiles = count($srcImagePaths);  // get number of image urls user input

if ($numberOfTiles <= 1) {
    echo '<div class="text-warning h5">Please input at least two image URLs.</div>';
} else if ($numberOfTiles == 5 || $numberOfTiles == 7) { 
    echo '<div class="text-warning h5">Please add or remove one image URL.</div>';
} else {
    if ($numberOfTiles % 3 == 0) {
        $col = 3;    // if number of image urls is 3 times, make 3 columns
    } else {
        $col = 2;    // otherwise, make 2 columns
    }

    $leftOffSet = $topOffSet = $pxMargin / 2;

    $mapWidth = ($tileWidth + $pxMargin) * $col;
    $mapHeight = ($tileHeight + $pxMargin) * ceil($numberOfTiles / $col);

    $mapImage = imagecreatetruecolor($mapWidth, $mapHeight);
    $bgColor = imagecolorallocate($mapImage, 217, 217, 217);
    imagefill($mapImage, 0, 0, $bgColor);

    foreach ($srcImagePaths as $index => $srcImagePath) {

        $img = $srcImagePath;

        $info = getimagesize($img);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($img);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($img);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($img);
        } else {
            echo '<div class="text-danger h5">Some image URLs couldn\'t be recognized, please check and replace.</div>';
            exit;
        }
        $width = $info[0];
        $height = $info[1];

        $sx = 0;
        $sy = 0;

        if ($height > $width && $height > $tileHeight) {
            $newh = $tileHeight;
            $neww = $width * round($newh / $height, 2);
            $sx = round(($tileWidth - $neww) / 2);
        } else {
            $neww = $tileWidth;
            $newh = $height * round($neww / $width, 2);
            $sy = round(($tileHeight - $newh) / 2);
        }

        $img250 = imagecreatetruecolor($tileWidth, $tileHeight);


        $img250bg = imagecolorallocate($img250, 255, 255, 255);
        $black = imagecolorallocate($img250, 0, 0, 0);
        $gray = imagecolorallocate($img250, 191, 191, 191);
        $red = imagecolorallocate($img250, 255, 0, 0);
        imagefill($img250, 0, 0, $img250bg);
        imagecopyresampled($img250, $image, $sx, $sy, 0, 0, $neww, $newh, $width, $height);

        list($x, $y) = indexToCoords($index, $col);

        imagecopyresampled($mapImage, $img250, $x, $y, 0, 0, $tileWidth, $tileHeight, $tileWidth, $tileHeight);

    }

    imagejpeg($mapImage, "./himg/image1.jpg", 100);

    echo '<img style="max-width:90%" src=https://ubest.club/himg/image1.jpg?' . time() . '>'; // make sure no cache image shown

}

function indexToCoords($index, $col)
{
    global $tileWidth, $tileHeight, $pxMargin, $leftOffSet, $topOffSet;

    $x = ($index % $col) * ($tileWidth + $pxMargin) + $leftOffSet;
    $y = floor($index / $col) * ($tileHeight + $pxMargin) + $topOffSet;
    return array($x, $y);
}
