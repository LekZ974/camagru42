/**
 * Created by lekz on 07/04/17.
 */
function sideNav() {
    var div = document.getElementById("displayMenu");
    var menu = document.getElementById("menu");
    if (div.classList.contains("change")) {
        div.classList.toggle("change");
        menu.style.width = "0";
    }
    else {
        div.classList.toggle("change");
        menu.style.width = "15%";
    }
}

function checkForm() {
    var champA = document.getElementById("mail").value;
    var champB = document.getElementById("confirmMail").value;
    var champC = document.getElementById("createPassword").value;
    var champD = document.getElementById("confirmPassword").value;
    var divcomp = document.getElementById("divcomp");
    var check = "";
    if (champA && champB && champC && champD) {
        if (champA == champB && champC == champD) {
            check = 'ok';
        }
    }
    else {
        check = 'error';
    }
    if (check == "ok" && champA != "" && champB != "" && champC != "" && champD != "") {
        document.getElementById("createBtn").style.display = "block";
        divcomp.innerHTML = "";
    }
    else {
        if (champA != "" && champB != "" && champC != "" && champD != "") {
            divcomp.innerHTML = "Erreur! Verifies les champs!";
        }
        document.getElementById("createBtn").style.display = "none";
    }
}

function checkResetForm() {
    var champA = document.getElementById("newPassword").value;
    var champB = document.getElementById("confirmPassword").value;
    var divcomp = document.getElementById("divcomp");
    var check = "";
    if (champA == champB) {
            check = 'ok';
        }
    else {
        check = '';
        divcomp.innerHTML = "Mots de passe pas identique!";
    }
    if (check == "ok" && champA != "" && champB != "") {
        document.getElementById("createBtn").style.display = "block";
        divcomp.innerHTML = "";
    }
    else {
        document.getElementById("createBtn").style.display = "none";
    }
}

function openModal() {
    document.getElementById('myModal').style.display = "block";
}

function closeModal() {
    document.getElementById('myModal').style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

function showSlides(n) {
    var i;
    var slides = document.getElementsByClassName("mySlides");
    var dots = document.getElementsByClassName("demo");
    var captionText = document.getElementById("caption");
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex-1].style.display = "block";
    dots[slideIndex-1].className += " active";
    captionText.innerHTML = dots[slideIndex-1].alt;
}