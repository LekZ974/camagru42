<?php
    $layout = 'layout/page.html.php';
    header('Refresh:5; url=/');
    if (isset($identity) && $identity != "Anon")
    {
        $message = "Bienvenu ".$identity." tu seras redirigé dans 5s";
    }
    else if ($_SESSION['connect'] == "")
    {
        $message = "Vous êtes déconnecté, à bientôt!";
    }
    else
        $message = "Vous n'êtes pas enregistré / vous n'avez pas activé votre compte";
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