
const fileInput = document.querySelector('input#upload-image-receipt');
const imagePreview = document.querySelector('.receipt-preview');

fileInput.style.opacity=0;

fileInput.addEventListener('change', () => {
  while(imagePreview.firstChild) {
    imagePreview.removeChild(imagePreview.firstChild);
  }

  const inputFiles = fileInput.files;
  console.log(inputFiles);
  if(inputFiles.length === 0) {
    imagePreview.innerHTML = '<p>Brak zdjęć</p>';
  } else {
    const image = document.createElement('img');
    image.src = URL.createObjectURL(inputFiles[0]);
    image.width = 460;
    imagePreview.appendChild(image);
  }
})

function uploadImageReceipt() {
  const inputFiles = fileInput.files;
  if(inputFiles.length <= 0) {
    return;
  }

  const request = new XMLHttpRequest();
  request.open("POST", "imageHandler.php", true);
  request.onreadystatechange = () => {
    if(request.readyState == 4 && request.status == 200) {
      document.querySelector("#php-output").innerHTML = request.responseText;
    }
  }
  const form = new FormData();
  form.append("file", inputFiles[0])
  request.send(form);
}