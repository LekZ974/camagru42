<?php
include __DIR__.'/database.php';

try{
    echo '- START -'.PHP_EOL;
    print_r("user=".$DB_USER.PHP_EOL);
    print_r("password=".$DB_PASSWORD.PHP_EOL);
    print_r("tab=".$DB_TAB_U.PHP_EOL);
    print_r("tab=".$DB_TAB_PIC.PHP_EOL);
    print_r("tab=".$DB_TAB_C.PHP_EOL);
    print_r("tab=".$DB_TAB_C2.PHP_EOL);
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
    echo '- Droping tables -'.PHP_EOL;
    $pdo->query("DROP TABLE IF EXISTS users");
    $pdo->query("DROP TABLE IF EXISTS pictures");
    $pdo->query("DROP TABLE IF EXISTS comments");
    $pdo->query("DROP TABLE IF EXISTS likes");
    echo '- Create tables -'.PHP_EOL;
    $pdo->query("CREATE TABLE users ( 
    id               INTEGER               PRIMARY KEY AUTOINCREMENT,
    login            VARCHAR( 255 )        NOT NULL,
    email            VARCHAR( 255 )        NOT NULL,
    password         VARCHAR( 512 )        NOT NULL,
    token            VARCHAR( 255 )        NOT NULL,
    verified         BOOLEAN               NOT NULL,
    created          DATETIME              NOT NULL
    );");
    $pdo->query("CREATE TABLE pictures (
    id               INTEGER               PRIMARY KEY AUTOINCREMENT,
    owner            VARCHAR(255)          NOT NULL,
    base64           LONGBLOB              NOT NULL,
    likes            INTEGER               ,
    comments         TEXT                  ,
    created_at       DATETIME              NOT NULL
    );");
    $pdo->query("CREATE TABLE comments (
    pic_id           INTEGER               ,
    login            VARCHAR(255)          NOT NULL,
    comments         TEXT                  NOT NULL,
    post_at          DATETIME              NOT NULL
    );");
    $pdo->query("CREATE TABLE likes (
    id               INTEGER               ,
    pic_id           INTEGER               ,
    login            VARCHAR(255)          
    );");
    if ($pdo)
    {
        $pdo->query($DB_TAB_U);
        $pdo->query($DB_TAB_PIC);
        $pdo->query($DB_TAB_C);
        $pdo->query($DB_TAB_C2);
        echo "Database : ".$DB_NAME." created".PHP_EOL;
    }
    else
    {
        die(print_r($pdo->errorInfo(), true));
    }
    $pdo = null;
} catch(Exception $e) {
    echo "Impossible d'accéder à la base de données SQLite : ".$e->getMessage();
    die();
}
?>