<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div>
    <div class="row">
        <?php
        foreach ($gallery as $image){
            echo "<div class='column'>
    <img src='".$image['id'].".png' onclick='openModal();currentSlide(".$image['id'].")' class='hover-shadow'>
  </div>";
        } ?>
    </div>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close cursor" onclick="closeModal()">&times;</span>
            <?php foreach ($gallery as $image){
                echo "<div class='mySlides'>
                <div class='numbertext'>".$image['id']."/".count($gallery)."</div>
                <img src='".$image['id'].".png'>
            <div class='box-comments'>
                <h3>Commentaires</h3>
                <a href='/comments?id=".$image['id']."'>Voir les commentaires de la photo".$image['id']."</a>
                
            </div>
            </div>
            ";
            } ?>
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>

            <div class="caption-container">
                <p id="caption"></p>
            </div>
            <?php foreach ($gallery as $image){
                echo "<div class='column'>
    <img class= 'demo' src='".$image['id'].".png' onclick='currentSlide(".$image['id'].")' class='hover-shadow'>
  </div>";
            } ?>
        </div>
    </div>
    <p>@TO DO</p>
</div>
</body>
</html>

<script>

</script>