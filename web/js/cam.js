/**
 * Created by ahoareau on 3/16/17.
 */

// Feature detection
function hasGetUserMedia() {
    return !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia || navigator.msGetUserMedia);
}

if (hasGetUserMedia()) {
    // Gaining access to an input device

    // Providing Fallback

    function fallback() {
        video.src = 'fallbackvideo.webm';
    }
    var hdConstraints = {
        video: {
            mandatory: {
                minWidth: 1280,
                minHeight: 720
            }
        }
    };

    function success(stream) {
        video.src = window.URL.createObjectURL(stream);
    }

    if (!navigator.getUserMedia) {
        fallback();
    } else {
        navigator.getUserMedia(hdConstraints, success, fallback);
    }

//Taking screenshot

    var video = document.querySelector('video');
    var canvas = document.querySelector('canvas');
    var ctx = canvas.getContext('2d');
    var localMediaStream = null;

    function snapshot() {
        if (localMediaStream) {
            ctx.drawImage(video, 0, 0);
            document.querySelector('img').src = canvas.toDataURL('image/png');
        }
    }

// Not showing vendor prefixes or code that works cross-browser.
    navigator.getUserMedia({video: true}, function(stream) {
        video.src = window.URL.createObjectURL(stream);
        localMediaStream = stream;
    }, fallback);

//Filters effect

    navigator.getMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
    navigator.getMedia({ video: true }, function(stream) {
        var video = document.querySelector('video');
        video.src = window.URL.createObjectURL(stream);
    }, function(e) {
        alert("Une erreur est survenue : ", e);
    });

    var idx = 0;
    var filters = ['grayscale', 'sepia', 'blur', 'brightness',
        'contrast', 'hue-rotate', 'hue-rotate2',
        'hue-rotate3', 'saturate', 'invert', ''];

    function changeFilter() {
        var el = document.getElementById('video');
        var im = document.getElementById('img');
        el.className = '';
        var effect = filters[idx++ % filters.length]; // loop through filters.
        if (effect) {
            el.classList.add(effect);
            im.classList.add(effect);
        }
    }
//****************************************************
} else {
    alert('getUserMedia() is not supported in your browser');
}

function saveImg() {
    var pic = document.getElementById('img');

    var xhr = getXMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // addgallery();
            savedConfirm();
            }
        }

    xhr.open("POST", "/save", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("pic=" + encodeURIComponent(pic.src));
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
