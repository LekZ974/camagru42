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
                $message = $this->signInAction($login, $password);
                return $this->render('security/checkAccount.html.php', ['request' => $request, 'message' => $message]);
            }
            if (isset($_POST['createLogin']) && isset($_POST['createPassword']) && isset($_POST['mail']))
            {
                $login = $_POST['createLogin'];
                $password = hash('whirlpool', $_POST['createPassword']);
                $mail = htmlspecialchars($_POST['mail']);
                $message = $this->signUpAction($login, $password, $mail);
                return $this->render('security/checkAccount.html.php', ['request' => $request, 'message' => $message]);
            }
                return $this->render('security/login.html.php', ['login']);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['request' => $request]);
        }
    }
    public function signUpAction($login, $password, $mail)
    {
        $db = new Database();
//        check login
        $stmt = $db->getPDO()->prepare('SELECT login FROM users WHERE login = :login');
        $stmt->bindValue(':login', $login);
        $stmt->execute();
        if ($stmt->fetchColumn() == $login)
        {
            return "l'identifiant ".$login." existe déjà";
        }
//        check mail
        $stmt = $db->getPDO()->prepare('SELECT email FROM users WHERE email = :mail');
        $stmt->bindValue(':mail', $mail);
        $stmt->execute();
        if ($stmt->fetchColumn() == $mail)
        {
            return "l'email : ".$mail." existe déjà";
        }
//        check password
        $token = md5(microtime(TRUE)*100000);
        $activateLink = "http://localhost:8080/activate?v=".$token;
        $stmt = $db->getPDO()->prepare('INSERT INTO users (login, password, email, token, verified, created) VALUES (:login, :password, :mail, :token, :verified, :created)');
        if ($stmt->execute([
            ':login'    => $login,
            ':password' => $password,
            ':mail'     => $mail,
            ':token'    => $token,
            ':verified' => false,
            ':created'  => date()
        ]))
        {
            $this->sendMail($mail, $login, $token);
            return "Tu vas recevoir un mail de confirmation pour finaliser ton inscription";
        }
        else
            return "Une erreur s'est produite";


    }

    /**
     * @param string $login
     * @param string $password
     *
     * @return string
     */
    public function signInAction($login, $password)
    {
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
            return "Bienvenu ".$_SESSION['user']." tu seras redirigé dans un instant. Si ce n'est pas le cas cliques <a href='/'>ici</a>";
        }
        else
        {
            return "Le nom d'utilisateur ou le mot de passe incorrect / tu n'as pas activé ton compte";
        }
    }

    /**
     * @param string $mail
     * @param string $login
     * @param string $token
     *
     */
    public function sendMail($mail, $login, $token)
    {
        $subject = 'Activation de ton compte Camagru';
        $message = 'Bienvenue sur Camagru,' . "\r\n" . 'Pour activer ton compte, cliques sur le lien ci dessous ou copier/coller dans ton navigateur internet.'
            . "\r\n" . "\r\n" . 'http://localhost:8080/activate?log=' . urlencode($login) . '&key=' . urlencode($token) . "\r\n" . "\r\n"
            . '---------------' . "\r\n" . 'C\'est un mail automatique, donc pas besoin d\'y répondre.';
        $message = wordwrap($message, 70, "\r\n");
        $headers = 'From: ahoareau@student.42.fr' . "\r\n";
        mail($mail, $subject, $message, $headers);
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    public function activateAccountAction($request)
    {
        $login = $_GET['log'];
        $token = $_GET['key'];

        $message = "lien d'activation non reconnu";
        if (isset($login) && isset($token))
        {
            $db = new Database();
            $stmt = $db->getPDO()->prepare('UPDATE users SET verified = 1 WHERE token = :token and verified = :verified');
            $stmt->BindValue(':token', $token);
            $stmt->BindValue(':verified', false);
            $stmt->execute();
            if ($stmt->rowCount() == 0)
            {
                $message = "Erreur : ton compte est déjà activé, si ce n'est pas le cas contactes l'administrateur";
            }
            else
            {
                $message = "ton compte est activé";
            }
        }
        return $this->render('security/checkAccount.html.php', ['request' => $request, 'message' => $message]);
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
        $message = "C'est bon t'es déconnecté, à bientôt!";
        return $this->render('security/checkAccount.html.php', ['request' => $request, 'message' => $message]);
    }

    public function forgotAction($request)
    {
        if (!isset($_SESSION['connect']))
        {
            $login = $_POST['login'];
            $mail = $_POST['mail'];
            if (isset($login) && isset($mail))
            {
                $db = new Database();
                $stmt = $db->getPDO()->prepare('SELECT login, email FROM users WHERE login = :login and email = :mail');
                $stmt->bindValue(':login', $login);
                $stmt->bindValue(':mail', $mail);
                $stmt->execute();
                if ($stmt->fetchColumn() != null)
                {

                    return $this->render('security/checkAccount.html.php', ['request' => $request, 'message' => $message]);
                }
                else
                {
                    $message = "Le nom d'utilisateur ou l'email ne sont pas enregistrés";
                    return $this->render('security/checkAccount.html.php', ['request' => $request, 'message' => $message]);
                }
            }
        }
        else
        {
            $message = "Tu es déjà connecté ".$_SESSION['user']."!!";
            return $this->render('security/checkAccount.html.php', ['request' => $request, 'message' => $message]);
        }
        return $this->render('security/recup-password.html.php', ['request' => $request, 'message' => $message]);
    }
}
