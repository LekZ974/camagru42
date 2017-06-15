(function() {

    var streaming    = false,
        video        = document.querySelector('#video'),
        cover        = document.querySelector('#cover'),
        canvas       = document.querySelector('#canvas'),
        photo        = document.querySelector('#snap'),
        takesnap     = document.querySelector('#startbutton'),
        width        = 320,
        height       = 0;

    navigator.getMedia = ( navigator.getUserMedia ||
    navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia ||
    navigator.msGetUserMedia);

    navigator.getMedia(
        {
            video: true,
            audio: false
        },
        function(stream) {
            if (navigator.mozGetUserMedia) {
                video.mozSrcObject = stream;
            } else {
                console.log(navigator);

                var vendorURL = window.URL || window.webkitURL;
                video.src = vendorURL.createObjectURL(stream);
            }
            video.play();

        },
        function(err) {
        }
    );

    video.addEventListener('canplay', function(ev){
        if (!streaming) {
            height = video.videoHeight / (video.videoWidth/width);
            video.setAttribute('width', width);
            video.setAttribute('height', height);
            canvas.setAttribute('width', width);
            canvas.setAttribute('height', height);
            streaming = true;
        }
    }, false);


    function takepicture() {
        canvas.width = width;
        canvas.height = height;
        canvas.getContext('2d').drawImage(video, 0, 0, width, height);
        var data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
        photo.style.display = "inline-block";
    }

    takesnap.addEventListener('click', function(ev){
        takepicture();
        ev.preventDefault();
    }, false);

})();

function saveCondition(){
    var snap = document.getElementById('snap');
    var filter = document.getElementById('filterSelect');
    if (snap.src !== "" && filter.src !== "" && snap.src !== "data:," && filter.alt !== "null"){
        document.getElementById('save-button').classList.remove('disabled');
    }
    else {
        document.getElementById('save-button').classList.add('disabled');
    }
}

function saveImg() {
    var pic = document.getElementById('snap');
    var filt = document.getElementById('filterSelect');

    var xhr = getXMLHttpRequest();

    if (pic.src !== "" && filt.src !== "" && pic.src !== "data:," && filt.alt !== "null"){
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                savedConfirm();
                addgallery()
            }
        };
        xhr.open("POST", "/save", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("pic=" + encodeURIComponent(pic.src) + "&filt=" + encodeURIComponent(filt.src));
    }
    else{
        alert('ok');
    }
}

function addgallery() {
    var content = document.getElementsByClassName('content')[0];

    var gallery = document.getElementById('mini-gallery');
    var camagru = document.getElementById('camagru');

    content.removeChild(gallery);

    gallery = document.createElement("div");
    gallery.setAttribute("id", "mini-gallery");
    gallery.setAttribute("class", "my-gallery");

    content.appendChild(gallery);

    var xhr = getXMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            gallery.innerHTML = this.responseText;
        }
    };
    xhr.open("GET", "/mini-gallery", true);
    xhr.send();
    camagru.parentNode.insertBefore(gallery,camagru.parentNode.firstChild)
}

function savedConfirm(){
    var divtempo = document.createElement("div");
    var text = document.createTextNode("Votre photo a bien été sauvegardée");

    divtempo.setAttribute("id", "tempo");
    divtempo.appendChild(text);
    document.body.appendChild(divtempo);
    window.setTimeout(function() {
        document.body.removeChild(divtempo);
    }, 1600);
}

var loadFile = function(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('snap');
        output.src = reader.result;
        output.style.display = "inline-block";
    };
    reader.readAsDataURL(event.target.files[0]);
};

document.addEventListener('click', saveCondition);