<?php
$layout = 'layout/page.html.php';
?>

<html>
<body>
<div id="alert-page">
    <div id="alert-check" class="modal">
        <span class="alert animate"><?= $statement[0] ?></span>
        <?php if ($statement[1] === true){
            echo "
            <form method=\"post\">
            <button type=\"submit\" name=\"valid\">Yes</button>
            <button type=\"submit\" name=\"cancel\">NO</button>
        </form>
        ";
        }
            ?>

    </div>
</div>
</body>
</html>
<script>
    document.getElementById('alert-check').style.display='block';
</script>