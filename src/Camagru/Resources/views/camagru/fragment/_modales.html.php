<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close cursor" onclick="closeModal()">&times;</span>
        <?php
        $i = 1;
        foreach ($modalGallery as $image){
            echo "
        <div class='mySlides'>
            <div class='numbertext'>".$i++."/".count($modalGallery)."</div>
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
        <?php
        $i = $index + 1;
        foreach ($gallery as $image){
            echo "
        <div class='column'>
            <img class= 'demo' src='".$image['id'].".png' onclick='currentSlide(".$i++.")' class='hover-shadow'>
        </div>";
        }
        $next = $pages['pageNext'];
        $prev = $pages['pagePrev'];
        echo <<<HTML
        <div class="page-navigation">
            <a href="gallery?page=$prev" class="prev">Précédente</a>
            <a href="gallery?page=$next" class="next">Suivante</a>
        </div>
HTML;
        ?>
    </div>
</div>