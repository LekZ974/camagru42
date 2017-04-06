<?php

namespace Camagru\Controller;

use Camagru\Response;
use Camagru\Database;

class SecurityController extends Base\AbstractController
{
    /**
     * @param array $request
     *
     * @return Response
     */
    public function loginAction($request)
    {
        if (!isset($_SESSION['connect']))
        {
            if (isset($_POST['login']) && isset($_POST['password']))
            {
                $login = $_POST['login'];
                $password = hash('whirlpool', $_POST['password']);
                $db = new Database();
                $stmt = $db->getPDO()->prepare('SELECT login, password, verified FROM users WHERE login = :login and password = :password and verified = 1');
                $stmt->bindValue(":login", $login);
                print_r($stmt->fetchColumn());
                $stmt->bindValue(":password", $password);
                $stmt->execute();
                if ($stmt->fetchColumn() != null)
                {
                    $_SESSION['user'] = $login;
                    $_SESSION['connect'] = "Connected";
                    return $this->render('security/checkAccount.html.php', ['request' => $request]);
                }
                else
                {
                    $_SESSION['user'] = "Anon";
                    return $this->render('security/checkAccount.html.php', ['request' => $request]);
                }
            }
            return $this->render('security/login.html.php', ['login']);
        }
        else
        {
            return $this->render('page/home.html.php', ['request' => $request]);
        }
    }
    public function createAccountAction($request)
    {
        return $this->render('page/home.html.php', ['request' => $request]);
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function logoutAction($request)
    {
        $_SESSION = [];
        session_destroy();
        setcookie('login',"");
        setcookie('password',"");
        return $this->render('security/checkAccount.html.php', ['request' => $request]);
    }
}
