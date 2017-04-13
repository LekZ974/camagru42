<?php
    $layout = 'layout/page.html.php';
    header('Refresh:2; url=/');
?>

<html>
<body>
<div id="alert-page">
        <div id="alert-check" class="modal">
            <span class="alert animate"><?= $message ?></span>
        </div>
</div>
</body>
</html>
<script>
    document.getElementById('alert-check').style.display='block';
</script>