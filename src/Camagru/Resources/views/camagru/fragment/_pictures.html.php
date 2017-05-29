<div class="row">
    <?php
    $i = 1;
    foreach ($gallery as $image){
        echo "<div class='column'>
    <img id='picture-gallery' src='".$image['id'].".png' onclick='openModal();currentSlide(".$i++.")' class='hover-shadow'>
    <a href='/delete?id=".$image['id']."'>X</a>
  </div>";
    } ?>
</div>