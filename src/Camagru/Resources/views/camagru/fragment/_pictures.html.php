<div class="row">
    <?php
    $i = $index + 1;
    foreach ($gallery as $image){
        if ($gallery[$i]['owner'] == $_SESSION['user']){
            echo "<div class='column'>
    <img id='picture-gallery' src='".$image['id'].".png' onclick='openModal();currentSlide(".$i++.")' class='hover-shadow'>
    <a href='/delete?id=".$image['id']."'>X</a>
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