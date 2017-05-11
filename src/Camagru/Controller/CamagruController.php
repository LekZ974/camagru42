<?php

namespace Camagru\Controller;

use Camagru\Database;
use Camagru\Response;

class CamagruController extends Base\AbstractController
{
    /**
     * @param array $request
     *
     * @return Response
     */
    public function AppCamagruAction($request)
    {
        $username = $_SESSION['user'];
        $db = new Database();
        $query = $db->getPDO()->prepare("SELECT id, base64, owner, likes, comments FROM pictures WHERE owner = ? ORDER BY 1 ASC");
        $query->execute(array($username));
        $gallery = null;
        $modal = null;
        $row = $query->fetchAll();
        foreach ($row as $picture)
        {
            file_put_contents($picture['id'].".png", base64_decode($picture['base64']));
        }
        return $this->render('camagru/appCamagru.html.php', ['_request' => $request, 'gallery' => $row]);
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function galleryAction($request)
    {
        $db = new Database();
        $sql = "SELECT id, base64, likes, comments, created_at FROM pictures ORDER BY created_at ASC ";
        $query = $db->getPDO()->prepare($sql);
        $query->execute();
        $gallery = null;
        $modal = null;
        $row = $query->fetchAll();
        foreach ($row as $picture)
        {
            file_put_contents($picture['id'].".png", base64_decode($picture['base64']));
        }
        return $this->render('camagru/gallery.html.php', ['_request' => $request, 'gallery' => $row, "index" => 1]);
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function saveAction($request)
    {
        $username = $_SESSION['user'];
        $photo = $this->editingPhoto($request);
        if (isset($photo, $username))
        {

            $db = new Database();
            $stmt = $db->getPDO()->prepare("INSERT INTO pictures(owner, base64, likes, created_at) VALUES (:username, :photo, :likes, :times)");
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':photo', $photo);
            $stmt->bindValue(':likes', 0);
            $stmt->bindValue(':times', date('Y-m-d H:i:s'));
            $stmt->execute();
            $db = null;
            header('Refresh:0; url=/Camagru');
        }
        else
        {
            $message = "Tu n'as pas à être ici!";
            return $this->render('security/checkAccount.html.php', ['request' => $request, 'statement' => $message]);
        }

        return $this->render('camagru/appCamagru.html.php', ['request' => $request]);
    }

    public function editingPhoto($request)
    {
        $picture = $_POST['pic'];
        $filter = $_POST['filt'];
        $img = preg_replace("%^data:image\/(?P<imgType>png|jpeg|jpg);base64,%i", '', $picture);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = uniqid() . '.png';
        $sucess = file_put_contents($file, $data);
        if ($sucess !== false)
        {
            print_r($filter);
            print_r($_SERVER['HTTP_ORIGIN'].'/'.$file);
            var_dump(gd_info());
            exit();
            $src = imagecreatefromstring(file_get_contents($filter));
            $dest = imagecreatefromstring(file_get_contents($_SERVER['HTTP_ORIGIN'].'/'.$file));
            if (imagecopy($dest, $src, 0, 0, 0, 0, imagesx($src), imagesy($src)))
            {
                return $file;
            }
        }
        return false;
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    public function deleteAction($request)
    {
        $db = new Database();
        $photo = $_GET['id'];
        $username = $_SESSION['user'];
        $stmt = $db->getPDO()->prepare("SELECT id FROM pictures WHERE id = :photo AND owner = :username");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':photo', $photo);
        $stmt->execute();
        $count = $stmt->fetch();
        $message = "Tu n'as pas les droits sur cette photo : ".$photo."! Cliques <a href='/gallery'>ici</a> pour revenir à la gallerie!";
        $status = false;
        if ($count != null)
        {
            $message = "Es tu sûre de vouloir supprimer ta photo ".$photo."?";
            $status = true;
            if (isset($_POST['valid']))
            {
                $stmt = $db->getPDO()->prepare("DELETE FROM pictures WHERE id = :photo AND owner = :username");
                $stmt->bindValue(':username', $username);
                $stmt->bindValue(':photo', $photo);
                $stmt->execute();
                $db = null;
                $message = "ta photo ".$photo."a bien été supprimée tu seras redirigé vers Camagru";
                header('Refresh:2; url=/Camagru');
            }
            if (isset($_POST['cancel']))
            {
                header('Refresh:0; url=/Camagru');
            }
        }
        return $this->render('camagru/delete.html.php', ['request' => $request, 'statement' => [$message, $status]]);
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    public function commentsAction($request)
    {
        $img = $_GET['id'];
        $db = new Database();
        $stmt = $db->getPDO()->prepare("SELECT pic_id, user_id, login, comments, post_at FROM comments WHERE pic_id = :img");
        $stmt->bindValue(':img', $img);
        $stmt->execute();
        $comments = $stmt->fetchAll();
        $stmt->closeCursor();
        $author = $comments[0]['login'];
        $stmt = $db->getPDO()->prepare("SELECT id, owner, likes FROM pictures WHERE id = :img");
        $stmt->bindValue(':img', $img);
        $stmt->execute();
        $image = $stmt->fetchAll();
        $stmt->closeCursor();
        if (isset($_POST['comment']))
        {
            $username = $_SESSION['user'];
            $comment = $_POST['comment'];
            $stmt = $db->getPDO()->prepare("INSERT INTO comments(pic_id, login, comments, post_at) VALUES (:img, :username, :comment, :time)");
            $stmt->bindValue(':img', $img);
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':comment', $comment);
            $stmt->bindValue(':time', date('Y-m-d H:i:s'));
            $stmt->execute();
            $stmt->closeCursor();
            if ($username !== $author)
            {
                $this->sendMail($username, $img, $comment, $author);
            }
        }
        $likes = $this->likeAction($request);
        return $this->render('camagru/comments.html.php', ['_request' => $request, 'data' => $comments, 'image' => $image, 'likes' => $likes]);
    }

    public function likeAction($request)
    {
        $db = new Database();
        $id = $_GET['id'];
        if (isset($id, $_GET['t']) && !empty($id) && !empty($_GET['t']) && $_SESSION['user'])
        {
            $username = $_SESSION['user'];
            $stmt = $db->getPDO()->prepare("SELECT COUNT(id) FROM pictures WHERE id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $count = $stmt->fetch();
            if ($count[0] == 1)
            {
                $checkLike = $db->getPDO()->prepare('SELECT COUNT(pic_id) FROM likes WHERE pic_id = ? AND login = ?');
                $checkLike->execute(array($id, $username));
                $count = $checkLike->fetch();
                if ($count[0] == 1)
                {
                    $del = $db->getPDO()->prepare('DELETE FROM likes WHERE pic_id = ? AND login = ?');
                    $del->execute(array($id, $username));
                }
                else
                {
                    $add = $db->getPDO()->prepare('INSERT INTO likes (pic_id, login) VALUES (?, ?)');
                    $add->execute(array($id, $username));
                }
            }
            else
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => 'erreur fatale redirection vers l\'acceuil']);
            }

        }
        $likes = $db->getPDO()->prepare("SELECT COUNT(pic_id) FROM likes WHERE pic_id = ?");
        $likes->execute(array($id));
        $likes = $likes->fetch();
        return $likes[0];
    }
    /**
     * @param string $destinataire
     * @param int $imageId
     * @param string $comment
     * @param string $author
     *
     */
    protected function sendMail($destinataire, $imageId, $comment, $author)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare("SELECT login, email FROM users WHERE login = :destinataire");
        $stmt->bindValue(':destinataire', $destinataire);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $stmt->closeCursor();
        $subject = $author.' t\'as laissé un commentaire';
        $message = '
		<html>
		<head>
		<title>Va voir vite</title>
		</head>
		<body>
			<p>Bonjour ' . $destinataire . ',</p>
			<br />
			<p>'.$author.' t\'as laissé un commentaire sur l\'une de tes photos Camagru.</p>
			<br />
			<p>'.$comment.'</p>
			<br />
			<p>Vas jeter un oeil <a href="http://localhost:8080/comments?id='.$imageId.'">ici</a>!</p>
			<br />
			<p>---------------</p>
			<p>C\'est un mail automatique, Merci de ne pas y répondre.</p>
		</body>
		</html>
		';
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ahoareau@student.42.fr' . "\r\n";
        mail($data[0]['email'], $subject, $message, $headers);
    }
}