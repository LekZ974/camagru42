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
                var vendorURL = window.URL || window.webkitURL;
                video.src = vendorURL.createObjectURL(stream);
            }
            video.play();
        },
        function(err) {
            console.log("An error occured! " + err);
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

    console.log(content);

    // document = document.createElement("content");
    // document.body.appendChild(content);

    var xhr = getXMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            content.innerHTML = this.responseText;
        }
    };
    xhr.open("GET", "/gallery", true);
    xhr.send();
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

//Filters effect

var idx = 0;
var filters = ['grayscale', 'sepia', 'blur', 'brightness',
    'contrast', 'hue-rotate', 'hue-rotate2',
    'hue-rotate3', 'saturate', 'invert', ''];

function changeFilter() {
    var el = document.getElementById('video');
    var im = document.getElementById('snap');
    el.className = '';
    var effect = filters[idx++ % filters.length]; // loop through filters.
    if (effect && idx != 11) {
        el.classList.toggle(effect);
        im.classList.toggle(effect);
        console.log(idx);
    }
    else{
        idx = 0;
        im.classList.remove('grayscale', 'sepia', 'blur', 'brightness',
            'contrast', 'hue-rotate', 'hue-rotate2',
            'hue-rotate3', 'saturate', 'invert');
    }
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