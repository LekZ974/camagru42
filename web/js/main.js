/**
 * Created by lekz on 07/04/17.
 */
function init() {

    var spanText = document.getElementById('targetSpanId');
    console.log(spanText);
}


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

function buttonChange() {
    var div = document.getElementById("buttonConnect");
    if (div.classList.contains("change")) {
        div.classList.toggle("change");
    }
    else {
        div.classList.toggle("change");
    }
}

function checkForm() {
    var champA = document.getElementById("mail").value;
    var champB = document.getElementById("confirmMail").value;
    var champC = document.getElementById("createPassword").value;
    var champD = document.getElementById("confirmPassword").value;
    var divcomp = document.getElementById("divcomp");
    var check = "";
    if (champA == champB) {
        if (champC == champD) {
            check = 'ok';
        }
    }
    else {
        divcomp.innerHTML = "Erreur !";
        check = '';
    }
    if (check == "ok" && champA != "" && champB != "" && champC != "" && champD != "") {
        document.getElementById("createBtn").style.display = "block";
    }
    else {
        document.getElementById("createBtn").style.display = "none";
    }
}