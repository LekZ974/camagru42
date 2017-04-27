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
        return $this->render('camagru/appCamagru.html.php', ['_request' => $request]);
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function galleryAction($request)
    {
        $db = new Database();
        $sql = "SELECT id, base64, likes, comments FROM pictures ORDER BY 1 ASC";
        $query = $db->getPDO()->prepare($sql);
        $query->execute();
        $gallery = null;
        $modal = null;
        $row = $query->fetchAll();
        foreach ($row as $picture)
        {
            file_put_contents($picture['id'].".png", base64_decode($picture['base64']));
        }
        return $this->render('camagru/gallery.html.php', ['_request' => $request, 'gallery' => $row]);
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    public function saveAction($request)
    {
        $username = $_SESSION['user'];
        $photo = $_POST['pic'];
        $img = preg_replace("%^data:image\/(?P<imgType>png|jpeg|jpg);base64,%i", '', $photo);
        var_dump($photo);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = uniqid() . '.png';
        $db = new Database();
        $stmt = $db->getPDO()->prepare("INSERT INTO pictures(owner, base64, likes, created_at) VALUES (:username, :photo, :likes, :times)");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':photo', $img);
        $stmt->bindValue(':likes', 0);
        $stmt->bindValue(':times', date('Y-m-d H:i:s'));
        $stmt->execute();
        $db = null;
        return $this->render('camagru/appCamagru.html.php', ['request' => $request]);
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
        if (isset($_GET['id'], $_GET['t']) && !empty($_GET['id']) && !empty($_GET['t']) && $_SESSION['user'])
        {
            $id = $_GET['id'];
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
                print_r($stmt->fetchAll());
//                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'message' => 'erreur fatale redirection vers l\'acceuil']);
            }

        }
        $likes = $db->getPDO()->prepare("SELECT COUNT(pic_id) FROM likes WHERE pic_id = ?");
        $likes->execute(array($id));
        $likes = $likes->fetch();
        print_r($likes);
        print_r($likes[0]);
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