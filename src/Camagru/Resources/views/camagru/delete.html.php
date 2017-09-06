<?php
$layout = 'layout/page.html.php';
?>

<html>
<body>
<div id="alert-page">
    <div id="alert-check" class="modal">
        <span class="alert animate"><?= $statement[0] ?></span>
        <?php
        if ($statement[1] === true){
            echo <<<FORM
        <form class= "form animate" method="post">
            <button class="sucess-btn" type="submit" name="valid">Yes</button>
            <button class="cancel-btn" type="submit" name="cancel">NO</button>
        </form>
FORM;
        }
            ?>

    </div>
</div>
</body>
</html>
<script>
    document.getElementById('alert-check').style.display='block';
</script>