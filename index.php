<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Wedding Photos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f9;
      padding: 40px;
      text-align: center;
    }
    .container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: auto;
    }
    img.logo {
      width: 160px; /* increased logo size */
      margin-bottom: 20px;
    }
    h1 {
      font-size: 1.5em;
      margin-bottom: 10px;
    }
    .note {
      font-size: 0.95em;
      color: #666;
      margin-bottom: 20px;
    }
    .upload-button {
      display: inline-block;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .upload-button:hover {
      background-color: #45a049;
    }
    #progress-container {
      margin-top: 20px;
      display: none;
    }
    progress {
      width: 100%;
      height: 20px;
    }
    #upload-success {
      display: none;
      color: #4CAF50;
      font-weight: bold;
      margin-top: 10px;
    }
    .public-note {
      font-size: 0.9em;
      color: #f44336;
      margin-top: 20px;
      text-align: center;
      max-width: 500px;
      margin: 20px auto;
      padding: 10px;
      background-color: #fff3f3;
      border: 1px solid #f44336;
      border-radius: 5px;
    }
    .gallery-link {
      display: inline-block;
      margin-top: 15px;
      padding: 8px 16px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .gallery-link:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="https://paulandela.site/wp-content/uploads/2025/03/Untitled.png" alt="Wedding Logo" class="logo">
    <h1>Upload your photos from our Wedding – 7/19/25</h1>
    <p class="note">Please upload up to 50 photos at a time</p>

    <form id="upload-form" enctype="multipart/form-data">
      <input type="file" id="image-input" name="images[]" multiple accept="image/*" style="display: none;">
      <label for="image-input" class="upload-button">Select Photos</label>
    </form>

    <div id="progress-container">
      <progress id="upload-progress" value="0" max="100"></progress>
      <p id="progress-text">0%</p>
    </div>

    <div id="upload-success">
      <p>✅ Upload successful!</p>
    </div>
  </div>

  <div class="public-note">
    <p><strong>Note:</strong> As this is a public gallery, please refrain from uploading any inappropriate photos. Thank you, and we hope you enjoyed our event!</p>
    <a class="gallery-link" href="gallery.php">View Public Gallery</a>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.getElementById("upload-form");
      const fileInput = document.getElementById("image-input");
      const progressContainer = document.getElementById("progress-container");
      const progressBar = document.getElementById("upload-progress");
      const progressText = document.getElementById("progress-text");
      const successDiv = document.getElementById("upload-success");

      fileInput.addEventListener("change", async () => {
        const files = fileInput.files;
        if (files.length === 0) return;

        if (files.length > 50) {
          alert("You can only upload up to 50 images at once.");
          return;
        }

        progressContainer.style.display = "block";
        progressBar.value = 0;
        progressText.textContent = `0 of ${files.length} uploaded`;

        for (let i = 0; i < files.length; i++) {
          try {
            const optimizedBlob = await optimizeImage(files[i]);
            const formData = new FormData();
            formData.append("images[]", optimizedBlob, files[i].name);

            await uploadFile(formData);

            const percent = Math.round(((i + 1) / files.length) * 100);
            progressBar.value = percent;
            progressText.textContent = `${i + 1} of ${files.length} uploaded`;
          } catch (err) {
            console.error("Upload failed for file:", files[i].name, err);
          }
        }

        fileInput.value = "";
        progressText.textContent = "Upload complete!";
        successDiv.style.display = "block";
      });

      function uploadFile(formData) {
        return new Promise((resolve, reject) => {
          const xhr = new XMLHttpRequest();
          xhr.open("POST", "upload.php", true);
          xhr.onload = () => xhr.status === 200 ? resolve() : reject(xhr.statusText);
          xhr.onerror = () => reject("Upload failed");
          xhr.send(formData);
        });
      }

      function optimizeImage(file) {
        return new Promise((resolve, reject) => {
          const img = new Image();
          const reader = new FileReader();

          reader.onload = () => img.src = reader.result;

          img.onload = () => {
            const canvas = document.createElement("canvas");
            const maxWidth = 1920;
            const scale = Math.min(1, maxWidth / img.width);
            canvas.width = img.width * scale;
            canvas.height = img.height * scale;

            const ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(blob => blob ? resolve(blob) : reject("Compression failed"), "image/jpeg", 0.75);
          };

          img.onerror = () => reject("Image load error");
          reader.onerror = () => reject("File read error");

          reader.readAsDataURL(file);
        });
      }
    });
  </script>
</body>
</html>
