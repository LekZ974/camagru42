<!--enlever path / quand a lexterieur de lecole-->
<html>
<head>
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="/css/form.css" />
    <script src="/js/app.js" type="text/javascript" charset="UTF-8"></script>
</head>
<body>
<header>
    <div id="displayMenu" class="containerMenu" onclick="sideNav(this)">
        <div class="bar1"></div>
        <div class="bar2"></div>
        <div class="bar3"></div>
    </div>
    <div id="menu" class="sidenav">
        <span><?= $identity ?></span>
        <a href="/">Accueil</a>
        <a href="/login">Connexion</a>
        <a href="/galerie">Galerie</a>
        <a href="/Camagru">Camagru</a>
    </div>
    <a href="/logout"><div id="buttonConnect" class="containerConnect" onclick="buttonChange(this)">
            <div class="b1"></div>
            <div class="b2"></div>
            <div class="b3"></div>
            <div class="b4"></div>
        </div></a>
</header>
<div class="content">
    <?= $content ?>
</div>
<footer>
    <div id="footer">
        Copyright &copy Camagru ahoareau <?php echo date('Y');?>
    </div>
</footer>

</body>

</html>