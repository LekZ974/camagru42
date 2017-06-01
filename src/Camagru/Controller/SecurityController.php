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
                if (isset($_POST['rememberMe']))
                {
                    setcookie('login', $login, time()+365*24*3600, null, null, false, true);
                    setcookie('password', $password, time()+365*24*3600, null, null, false, true);
                }
                $message = $this->signInAction($login, $password);
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
            }
            if (isset($_POST['createLogin']) && isset($_POST['createPassword']) && isset($_POST['mail']))
            {
                $login = $_POST['createLogin'];
                $password = hash('whirlpool', $_POST['createPassword']);
                $mail = htmlspecialchars($_POST['mail']);
                $message = $this->signUpAction($login, $password, $mail);
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
            }
                return $this->render('security/login.html.php', ['login']);
        }
        else
        {
            $message = "Tu es déjà connecté ".$_SESSION['user']."!!";
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
        }
    }

    /**
     * @param $login
     * @param $password
     * @param $mail
     * @return string
     */
    public function signUpAction($login, $password, $mail)
    {
        $db = new Database();
//        check login
        $stmt = $db->getPDO()->prepare('SELECT login FROM users WHERE login = ?');
        $stmt->execute([$login]);
        if ($stmt->fetchColumn() == $login)
        {
            return "l'identifiant ".$login." existe déjà";
        }
//        check mail
        $stmt = $db->getPDO()->prepare('SELECT email FROM users WHERE email = ?');
        $stmt->execute([$mail]);
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
            ':created'  => date('Y-m-d H:i:s'),
        ]))
        {
            $this->sendMail($mail, $login, $token, "activate");
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
        $stmt = $db->getPDO()->prepare('SELECT login, password, verified FROM users WHERE login = ? and password = ? and verified = ?');
        $stmt->execute([$login, $password, 1]);
        if ($stmt->fetchColumn() != null)
        {
            $_SESSION['user'] = $login;
            $_SESSION['connect'] = "connected";
            return "Bienvenu ".$_SESSION['user']." tu seras redirigé dans un instant. Si ce n'est pas le cas cliques <a href='/'>ici</a>";
        }
        else
        {
            return "Le nom d'utilisateur ou le mot de passe incorrect / tu n'as pas activé ton compte";
        }
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
        return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    public function logoutAction($request)
    {
        $message = "Connectes toi avant de te déconnecter!";
        if ($_SESSION['connect'])
        {
            $_SESSION = [];
            session_destroy();
            setcookie('login',"");
            setcookie('password',"");
            $message = "C'est bon t'es déconnecté, à bientôt!";
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
        }
        return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
    }

    /**
     * @param $request
     * @return Response
     */
    public function forgotAction($request)
    {
        if (!isset($_SESSION['connect']))
        {
            $login = $_POST['login'];
            $mail = $_POST['mail'];
            if (isset($login) && isset($mail))
            {
                $db = new Database();
                $stmt = $db->getPDO()->prepare('SELECT login, email, token FROM users WHERE login = :login and email = :mail');
                $stmt->bindValue(':login', $login);
                $stmt->bindValue(':mail', $mail);
                $stmt->execute();
                $token = $stmt->fetchColumn(2);
                if ($token != null)
                {
                    $this->sendMail($login, $mail, $token, "reset");
                    $message = "Tu vas recevoir un email pour réinitialiser ton mot de passe";
                    return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
                }
                else
                {
                    $message = "Le nom d'utilisateur ou l'email ne sont pas enregistrés";
                    return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
                }
            }
        }
        else
        {
            $message = "Tu es déjà connecté ".$_SESSION['user']."!!";
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
        }
        return $this->render('security/recup-password.html.php', ['_request' => $request]);
    }

    /**
     * @param $request
     * @return Response
     */
    public function resetPasswordAction($request)
    {
        $login = $_GET['log'];
        $token = $_GET['key'];

        if (isset($login) && isset($token))
        {
            $db = new Database();
            $stmt = $db->getPDO()->prepare('SELECT login, token FROM users WHERE login = :login and token = :token');
            $stmt->bindValue(':login', $login);
            $stmt->bindValue(':token', $token);
            $stmt->execute();
            if ($stmt->fetchColumn() !== 0)
            {
                if (isset($_POST['newPassword']) && isset($_POST['confirmPassword']) && $_POST['newPassword'] == $_POST['confirmPassword'])
                {
                    $newPassword = hash('whirlpool', $_POST['newPassword']);
                    $confirmPassword = hash('whirlpool', $_POST['confirmPassword']);
                    $stmt = $db->getPDO()->prepare('UPDATE users SET password = :newPassword WHERE login = :login and token = :token');
                    $stmt->bindValue(':token', $token);
                    $stmt->bindValue(':login', $login);
                    $stmt->bindValue(':newPassword', $newPassword);
                    $stmt->execute();
                    $message = "Ton mot de passe à été mis à jour";
                    return $this->render('security/checkAccount.html.php', ['_request' => $request, 'message' => $message]);
                }
            }
            else
            {
                $message = "Tu n'as pas le droit d'être ici!! Contactes un admin si besoin";
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
            }
            return $this->render('security/reset-password.html.php', ['_request' => $request]);
        }
    }
    /**
     * @param string $mail
     * @param string $login
     * @param string $token
     * @param string $type
     *
     */
    protected function sendMail($mail, $login, $token, $type)
    {
        $subject = null;
        $message = null;
        $headers = null;
        $queryString = 'log='.urlencode($login).'&key'.urlencode($token);
        if ($type == "activate")
        {
            $subject = 'Activation de ton compte Camagru';
            $message = <<<MAIL
            <html>
		<head>
		<title>Bienvenue sur Camagru $login!!</title>
		</head>
		<body>
			<br />
			<p>Pour activer ton compte, cliques sur le lien ci dessous ou copier/coller dans ton navigateur internet.</p>
			<a href="http://localhost:8080/activate?$queryString">Cliques ici pour activer ton compte.</a>
			<br />
			<p>---------------</p>
			<p>C'est un mail automatique, Merci de ne pas y répondre.</p>
		</body>
		</html>
MAIL;
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: ahoareau@student.42.fr' . "\r\n";
        }
        elseif ($type = "reset")
        {
            $subject = 'Réinitialisation de ton mot de passe Camagru';
            $message = <<<MAIL
		<html>
		<head>
		<title>Reinitialisation mot de passe</title>
		</head>
		<body>
			<p>Bonjour $login,</p>
			<br />
			<p>Quelqu’un a récemment demandé à réinitialiser ton mot de passe Camagru.</p>
			<a href="http://localhost:8080/Camagru/reset?$queryString">Cliques ici pour changer ton mot de passe.</a>
			<br />
			<p>---------------</p>
			<p>C'est un mail automatique, Merci de ne pas y répondre.</p>
		</body>
		</html>
MAIL;
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: ahoareau@student.42.fr' . "\r\n";
        }
        mail($mail, $subject, $message, $headers);
    }
}
