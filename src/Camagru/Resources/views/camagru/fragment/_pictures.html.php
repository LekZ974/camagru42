<div class="row">
    <?php
    foreach ($gallery as $image){
        echo "<div class='column'>
    <img id='picture-gallery' src='".$image['id'].".png' onclick='openModal();currentSlide(".$image['id'].")' class='hover-shadow'>
    <a href='/delete?id=".$image['id']."'>X</a>
  </div>";
    } ?>
</div>