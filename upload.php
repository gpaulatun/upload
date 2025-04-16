<?php
$target_dir = "uploads/";
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true);
}

if (!empty($_FILES['images'])) {
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $original_name = basename($_FILES['images']['name'][$key]);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_types)) continue;
        if (getimagesize($tmp_name) === false) continue;

        $new_name = uniqid("img_", true) . '.' . $ext;
        $target_file = $target_dir . $new_name;

        if (move_uploaded_file($tmp_name, $target_file)) {
            optimizeImage($target_file, $ext);
        }
    }
}

function optimizeImage($file, $ext) {
    switch ($ext) {
        case 'jpeg':
        case 'jpg':
            $image = imagecreatefromjpeg($file);
            $exif = @exif_read_data($file);

            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6:
                        $image = imagerotate($image, -90, 0);
                        break;
                    case 8:
                        $image = imagerotate($image, 90, 0);
                        break;
                }
            }

            imagejpeg($image, $file, 75); // optimize
            break;

        case 'png':
            $image = imagecreatefrompng($file);
            imagepng($image, $file, 6); // compress level 0 (no compression) to 9
            break;

        case 'gif':
            $image = imagecreatefromgif($file);
            imagegif($image, $file);
            break;
    }

    if (isset($image) && is_resource($image)) {
        imagedestroy($image);
    }
}
?>
