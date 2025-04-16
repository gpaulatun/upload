document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("upload-form");
  const fileInput = form.querySelector('input[type="file"]');
  const progressContainer = document.getElementById("progress-container");
  const progressBar = document.getElementById("upload-progress");
  const progressText = document.getElementById("progress-text");
  const gallery = document.getElementById("gallery");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const files = fileInput.files;
    if (files.length === 0) {
      alert("Please select images.");
      return;
    }

    if (files.length > 50) {
      alert("You can only upload up to 50 images at once.");
      return;
    }

    progressContainer.style.display = "block";
    progressBar.value = 0;
    progressText.textContent = `0 of ${files.length} uploaded`;

    for (let i = 0; i < files.length; i++) {
      const formData = new FormData();
      formData.append("images[]", files[i]);

      try {
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
    document.getElementById("upload-success").style.display = "block";
  });

  function uploadFile(formData) {
    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "upload.php", true);

      xhr.onload = () => {
        if (xhr.status === 200) {
          resolve();
        } else {
          reject(xhr.statusText);
        }
      };

      xhr.onerror = () => reject("Upload failed");
      xhr.send(formData);
    });
  }

  function loadGallery() {
    fetch("uploads/")
      .then(res => res.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");
        const links = [...doc.querySelectorAll("a")]
          .map(a => a.getAttribute("href"))
          .filter(href => /\.(jpg|jpeg|png|webp)$/i.test(href));

        gallery.innerHTML = links.map(href =>
          `<img src="uploads/${href}" alt="">`
        ).join("");
      });
  }

  loadGallery();
});
