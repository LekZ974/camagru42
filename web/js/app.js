function setImage(elem) {
    var img = elem.querySelector('#filter');
    var splitted = img.src.split('/');
    var name = splitted.find(function (el) {
        if (el.indexOf('.png') != -1){
            return el;
        }
    });
    if (name == "none.png")
    {
        document.getElementById('filterSelect').src = null;
        document.getElementById('filterSelect').alt = null;
        document.getElementById('filterSelect').style.display = "none";
    }
    else
    {
        document.getElementById('filterSelect').style.display = "inline-block";
        document.getElementById('filterSelect').src="image/filter/"+name;
        document.getElementById('filterSelect').alt=name;
    }



}