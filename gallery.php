<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Wedding Gallery</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      text-align: center;
      padding: 40px;
    }
    h1 {
      color: #444;
      margin-bottom: 30px;
    }
    .gallery {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
    }
    .gallery a img {
      width: 200px;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <h1>Wedding Photo Gallery</h1>
  <div class="gallery">
    <?php
    $dir = "uploads/";
    $thumbDir = "uploads/thumbs/";
    $images = array_diff(scandir($dir), ['.', '..', 'thumbs']);

    foreach ($images as $img) {
      $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
      if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        $thumb = $thumbDir . $img;
        $full = $dir . $img;
        if (file_exists($thumb)) {
          echo "<a href='$full' data-lightbox='gallery'><img src='$thumb'></a>";
        } else {
          echo "<a href='$full' data-lightbox='gallery'><img src='$full'></a>";
        }
      }
    }
    ?>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
</body>
</html>
