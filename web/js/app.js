/**
 * Created by ahoareau on 2/16/17.
 */
function openNav() {
    document.getElementById("menu").style.width = "15%";
}

function closeNav() {
    document.getElementById("menu").style.width = "0";
}

function sideNav(div) {
    if (div.classList.contains("change")) {
        div.classList.toggle("change");
        closeNav();
    }
    else {
        div.classList.toggle("change");
        openNav();
    }
}

function buttonChange(div) {
    if (div.classList.contains("change")) {
        div.classList.toggle("change");
    }
    else {
        div.classList.toggle("change");
    }
}

// Get the modal
var modal = document.getElementById('create');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};
