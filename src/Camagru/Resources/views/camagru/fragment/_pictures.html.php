<div id='gallery' class="row">
    <?php
    $i = $index + 1;
    foreach ($gallery as $image){
        if ($image['owner'] == $_SESSION['user']){
            echo "<div class='column'>
    <img id='picture-gallery' src='".$image['id'].".png' onclick='openModal();currentSlide(".$i++.")' class='hover-shadow'>
    <a href='/delete?id=".$image['id']."' class = del-btn><img src='/image/Button-Delete-icon.png'></a>
  </div>";
        }
        else{
            echo "<div class='column'>
    <img id='picture-gallery' src='".$image['id'].".png' onclick='openModal();currentSlide(".$i++.")' class='hover-shadow'>
  </div>";
        }
    }
    ?>
</div>