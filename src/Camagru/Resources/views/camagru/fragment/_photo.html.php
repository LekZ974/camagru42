<div class="photo">
    <div class="photomaton">
        <img id="cadre" src="/image/Cadre-photo.jpg">
        <img id="snap">
        <img id="filterSelect">
    </div>
    <div class="camera">
        <video id="video" autoplay></video>
        <div class="photo-action">
            <input id="selectImg" type="file" accept="image/png, image/jpg, image/jpeg" onchange="loadFile(event); saveCondition()">
            <div class="warning">
                <p>*only files png/jpg/jpeg</p>
            </div>
            <button id="startbutton">Take Snapshot</button>
            <button  id="save-button" class="disabled" onclick="saveImg()">Save</button>
        </div>
        <canvas id="canvas" style="display:none;"></canvas>
    </div>
    <br>
</div>