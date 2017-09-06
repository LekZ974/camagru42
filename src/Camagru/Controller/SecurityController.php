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
            $signIn = $this->signInAction($request, $_POST['login'], $_POST['password']);
            if ($signIn['bool'] === true)
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $signIn['statement'], 'anchor' => 'Camagru']);
            }
            $signUp = $this->signUpAction($request, $_POST['createLogin'], $_POST['createPassword'], $_POST['mail']);
            if ($signUp['bool'] === true)
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $signUp['statement'], 'anchor' => null]);
            }
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu es déjà connecté ".$_SESSION['user']."!!", 'anchor' => null]);
        }
        return $this->render('security/login.html.php', ['request' => $request]);
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

        return $this->render('security/checkAccount.html.php', ['request' => $request, 'statement' => $this->activeUser($login, $token)['statement'], 'anchor' => null]);
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    public function logoutAction($request)
    {
        if ($_SESSION['connect'])
        {
            $_SESSION = [];
            session_destroy();
            setcookie('login',"");
            setcookie('password',"");
            return $this->render('security/checkAccount.html.php', ['request' => $request, 'statement' => "C'est bon t'es déconnecté, à bientôt!", 'anchor' => null]);
        }
        return $this->render('security/checkAccount.html.php', ['request' => $request, 'statement' => "Connectes toi avant de te déconnecter!", 'anchor' => null]);
    }

    /**
     * @param $request
     * @return Response
     */
    public function forgotAction($request)
    {
        $login = $_POST['login'];
        $mail = $_POST['mail'];
        if (!isset($_SESSION['connect']))
        {
            if (isset($login, $mail))
            {
                if ($this->secureInput($login) === true && $this->secureInput($mail) === true)
                {
                    $token = $this->getTokenByUserAndMail($login, $mail);
                    if (null != $token) {
                        $this->sendMailWithToken($mail, $login, $token, "reset");
                        return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu vas recevoir un email pour réinitialiser ton mot de passe", 'anchor' => null]);
                    } else {
                        return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Ton email ou ton login est incorrect", 'anchor' => 'forgot']);
                    }
                }
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "les caractères < > \" ' / et \ sont interdit", 'anchor' => 'forgot']);
            }
            return $this->render('security/recup-password.html.php', ['_request' => $request]);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu es déjà connecté ".$_SESSION['user']."!!", 'anchor' => null]);
        }
    }

    /**
     * @param $request
     * @return Response
     */
    public function resetPasswordAction($request)
    {
        $login = $_GET['log'];
        $token = $_GET['key'];
        if (isset($login, $token))
        {
            if ($this->getUserByToken($login, $token) == true)
            {
                $newPassword = $_POST['newPassword'];
                $confirmPassword = $_POST['confirmPassword'];
                    if (isset($newPassword, $confirmPassword) && $newPassword == $confirmPassword)
                    {
                        if ($this->secureInput($newPassword) === true && $this->secureInput($confirmPassword) === true)
                        {
                            $this->updatePassword($login, $newPassword);

                            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'message' => "Ton mot de passe à été mis à jour", 'anchor' => null]);
                        }
                        return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "les caractères < > \" ' / et \ sont interdit", 'anchor' => 'reset?log='.$login.'&key='.$token]);
                    }
            }
            else
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas le droit d'être ici!! Contactes un admin si besoin", 'anchor' => null]);
            }
            return $this->render('security/reset-password.html.php', ['_request' => $request]);
        }
        return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas le droit d'être ici!! Contactes un admin si besoin", 'anchor' => null]);
    }

    /**
     * @param $request
     * @param $login
     * @param $password
     * @return array
     */
    protected function signInAction($request, $login, $password)
    {
        if (isset($login, $password))
        {
            if ($this->secureInput($login) === true && $this->secureInput($password) === true)
            {
                $this->rememberMe($_POST['rememberMe'], $login, $password);
                if ($this->getVerifyUser($login, $password) == $login) {
                    $_SESSION['user'] = $login;
                    $_SESSION['connect'] = "connected";
                    return ['_request' => $request, 'bool' => true, 'statement' => "Bienvenu " . $_SESSION['user'] . " tu seras redirigé dans un instant. Si ce n'est pas le cas cliques <a href='/'>ici</a>"];
                } else {
                    return ['_request' => $request, 'bool' => true, 'statement' => "Le nom d'utilisateur ou le mot de passe incorrect / tu n'as pas activé ton compte"];
                }
            }
            return ['_request' => $request, 'bool' => true, 'statement' => "les caractères < > \" ' / et \ sont interdit"];
        }
        return ['_request' => $request, 'bool' => false, 'statement' => "Une erreur s'est produite"];
    }

    /**
     * @param $login
     * @param $password
     * @return string
     */
    protected function getVerifyUser($login, $password)
    {
        $passwordHash = hash('whirlpool', $password);
        $db = new Database();
        $stmt = $db->getPDO()->prepare('SELECT login, password, verified FROM users WHERE login = ? and password = ? and verified = ?');
        $stmt->execute([$login, $passwordHash, 1]);
        return $stmt->fetchColumn();
    }

    /**
     * @param $condition
     * @param $login
     * @param $password
     */
    protected function rememberMe($condition, $login, $password)
    {
        $passwordHash = hash('whirlpool', $password);
        if ($condition === 'on')
        {
            setcookie('login', $login, time()+365*24*3600, null, null, false, true);
            setcookie('password', $passwordHash, time()+365*24*3600, null, null, false, true);
        }
    }

    /**
     * @param $request
     * @param $login
     * @param $password
     * @param $mail
     * @return array
     */
    protected function signUpAction($request, $login, $password, $mail)
    {
        if (isset($login, $password, $mail))
        {
            if ($this->secureInput($login) === true && $this->secureInput($password) === true)
            {
                $mail = htmlspecialchars($_POST['mail']);
                if ($this->getUser($login) === true) {
                    return ['_request' => $request, 'bool' => true, 'statement' => "l'identifiant " . $login . " existe déjà"];
                }
                if ($this->getEmail($mail) === true) {
                    return ['_request' => $request, 'bool' => true, 'statement' => "l'email : " . $mail . " existe déjà"];
                }
                $token = md5(microtime(TRUE) * 100000);
                if ($this->createUser($login, $mail, $password, $token) === true) {
                    $this->sendMailWithToken($mail, $login, $token, "activate");
                    return ['_request' => $request, 'bool' => true, 'statement' => "Tu vas recevoir un mail de confirmation pour finaliser ton inscription"];
                } else {
                    return ['_request' => $request, 'bool' => false, 'statement' => "Une erreur s'est produite"];
                }
            }
            return ['_request' => $request, 'bool' => true, 'statement' => "les caractères < > \" ' / et \ sont interdit"];
        }
        return ['_request' => $request, 'bool' => false, 'statement' => "Une erreur s'est produite"];
    }

    /**
     * @param $login
     * @return bool
     */
    protected function getUser($login)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare('SELECT login FROM users WHERE login = ?');
        $stmt->execute([$login]);
        if ($stmt->fetchColumn() == $login)
        {
            return true;
        }
        return false;
    }

    /**
     * @param $email
     * @return bool
     */
    protected function getEmail($email)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare('SELECT email FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() == $email)
        {
            return true;
        }
        return false;
    }

    /**
     * @param $login
     * @param $mail
     * @param $password
     * @param $token
     * @return bool
     */
    protected function createUser($login, $mail, $password, $token)
    {
        try {
            $passwordHash = hash('whirlpool', $password);
            $db = new Database();
            $stmt = $db->getPDO()->prepare('INSERT INTO users (login, password, email, token, verified, created) VALUES (:login, :password, :mail, :token, :verified, :created)');
            $stmt->execute([
                ':login' => $login,
                ':password' => $passwordHash,
                ':mail' => $mail,
                ':token' => $token,
                ':verified' => false,
                ':created' => date('Y-m-d H:i:s'),
            ]);
            return true;
        }
        catch (\Exception $error)
        {
            die('Erreur : ' . $error->getMessage());
        }
    }

    /**
     * @param $login
     * @param $password
     */
    protected function updatePassword($login, $password)
    {
        try
        {
            $passwordHash = hash('whirlpool', $password);
            $db = new Database();
            $stmt = $db->getPDO()->prepare('UPDATE users SET password = ? WHERE login = ?');
            $stmt->execute([$passwordHash, $login]);
        }
        catch (\Exception $error)
        {
            die('Erreur : ' . $error->getMessage());
        }

    }

    /**
     * @param $login
     * @param $token
     * @return array
     */
    protected function activeUser($login, $token)
    {
        if (isset($login, $token))
        {
            if ($this->getUserByToken($login, $token) === true)
            {
                $db = new Database();
                $stmt = $db->getPDO()->prepare('UPDATE users SET verified = 1 WHERE login = ? and verified = ?');
                $stmt->execute([$login, false]);
                if ($stmt->rowCount() == 0)
                {
                    return ['bool' => true, 'statement' => "Erreur : ton compte est déjà activé, si ce n'est pas le cas contactes l'administrateur"];
                }
                else
                {
                    return ['bool' => true, 'statement' => "ton compte est activé"];
                }
            }
        }

        return ['bool' => false, 'statement' => "lien d'activation non reconnu"];
    }

    /**
     * @param $login
     * @param $token
     * @return bool
     */
    protected function getUserByToken($login, $token)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare('SELECT login, token FROM users WHERE login = ? and token = ?');
        $stmt->execute([$login, $token]);
        if ($stmt->fetchColumn() === $login)
        {
            return true;
        }
        return false;
    }

    /**
     * @param $login
     * @param $mail
     * @return string
     */
    protected function getTokenByUserAndMail($login, $mail)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare('SELECT login, email, token FROM users WHERE login = ? and email = ?');
        $stmt->execute([$login, $mail]);
        return $stmt->fetchColumn(2);
    }

    /**
     * @param $login
     * @return bool
     */
    protected function isUser($login)
    {
        if (isset($_SESSION['user'], $_SESSION['connect']) && !empty($_SESSION['user']) && $login == $_SESSION['user'])
        {
            return true;
        }
        return false;
    }

    /**
     * @param string $mail
     * @param string $login
     * @param string $token
     * @param string $type
     *
     */
    protected function sendMailWithToken($mail, $login, $token, $type)
    {
        $subject = null;
        $message = null;
        $headers = null;
        $queryString = 'log='.urlencode($login).'&key='.urlencode($token);
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
			<a href="http://localhost:8080/reset?$queryString">Cliques ici pour changer ton mot de passe.</a>
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

    protected function secureInput($str)
    {
        if (preg_match("#[<>/'\\\"]#", $str) === 1)
        {
            return false;
        }
        return true;
    }
}
