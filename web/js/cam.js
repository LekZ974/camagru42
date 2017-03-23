/**
 * Created by ahoareau on 3/16/17.
 */

function hasGetUserMedia() {
    return !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia || navigator.msGetUserMedia);
}

if (hasGetUserMedia()) {
    // Good to go!
} else {
    alert('getUserMedia() is not supported in your browser');
}
//Filters effect

    navigator.getMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
    navigator.getMedia({ video: true, audio: true }, function(stream) {
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
    el.className = '';
    var effect = filters[idx++ % filters.length]; // loop through filters.
    if (effect) {
        el.classList.add(effect);
    }
}
//****************************************************

//SNAPSHOT
var video = document.getElementById('video');
var canvas = document.getElementById('canvas');
console.log(canvas);
console.log(video);
var ctx = canvas.getContext('2d');
var localMediaStream = null;

function snapshot() {
    if (localMediaStream) {
        ctx.drawImage(video, 0, 0);
        // "image/webp" works in Chrome.
        // Other browsers will fall back to image/png.
        document.querySelector('img').src = canvas.toDataURL('image/webp');
    }
}

// video.addEventListener('click', snapshot, false);
//****************************************************

