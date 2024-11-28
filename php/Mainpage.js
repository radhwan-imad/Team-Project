let slideIndex = 1;
showSlides1(slideIndex);

// Next/previous controls
function plusSlides(n) {
  showSlides1(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides1(slideIndex = n);
}

function showSlides1(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) { slideIndex = 1 }
  if (n < 1) { slideIndex = slides.length }
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex - 1].style.display = "block";
  dots[slideIndex - 1].className += " active";
}

let slideIndexs = 0;
showSlides();

function showSlides() {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slideIndexs++;
  if (slideIndexs > slides.length) { slideIndexs = 1 }
  slides[slideIndexs - 1].style.display = "block";
  setTimeout(showSlides, 10000); // Change image every 5 seconds
}




function openFullImg(pic) {
  var fullimgBox = document.getElementById("fullimgBox");
  var fullImg = document.getElementById("fullImg");
  fullimgBox.style.display = 'flex';
  fullImg.src = pic;
}
function closeFullImg() {
  var fullimgBox = document.getElementById("fullimgBox");
  var fullImg = document.getElementById("fullImg");
  fullimgBox.style.display = 'none';
}