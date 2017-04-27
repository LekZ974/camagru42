<?php

$layout = 'layout/page.html.php';

?>
<html>
<body>
<div class="container">
    <img class="image" src="<?= $image[0]['id']?>.png">
    <a href="/comments?id=<?= $image[0]['id'] ?>&t=like">Like</a>(<?= $likes ?>)
    <form action='' method='POST'>
    <input type='text' name='comment' placeholder='Commentes la photo ici'>
    <button class='btn' type='submit' id='commentBtn' name='send'><span>Envoyer</span></button>
    </form>
    <?php
    print_r($likes);
    foreach ($data as $comment)
    {
        echo "
    <div class='comments'>
        <p>Post by ".$comment['login']." le ".$comment['post_at']."</p>
        <p>".$comment['comments']."</p>
    </div>
    ";
    }
    ?>
</div>
</body>
</html>