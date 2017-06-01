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
        if (isset($username, $_SESSION['connect']) && !empty($username))
        {
            $userGallery = $this->getPageUserPictures($request);
            $modalGallery = $this->getAllUserPictures($request);

            return $this->render('camagru/appCamagru.html.php', ['_request' => $request, 'gallery' => $userGallery['pictures'], 'modalGallery' => $modalGallery]);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!"]);
        }
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function galleryAction($request)
    {
        if (isset($_SESSION['user'], $_SESSION['connect']) && !empty($_SESSION['user'])) {
            $row = $this->getPagePictures($request);
            if ($row['pictures'] == null)
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Pas de photo à afficher!"]);
            }
            $modalGallery = $this->getAllPictures($request);

            return $this->render('camagru/gallery.html.php', ['_request' => $request, 'gallery' => $row['pictures'], 'pages' => $row['pages'], 'modalGallery' => $modalGallery, 'index' => $row['index']]);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!"]);
        }
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function userGalleryAction($request)
    {
        if (isset($_SESSION['user'], $_SESSION['connect']) && !empty($_SESSION['user'])) {
            $row = $this->getPageUserPictures($request);
            if ($row['pictures'] == null)
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Pas de photo à afficher!"]);
            }
            $modalGallery = $this->getAllUserPictures($request);

            return $this->render('camagru/gallery.html.php', ['_request' => $request, 'gallery' => $row['pictures'], 'pages' => $row['pages'], 'modalGallery' => $modalGallery, 'index' => $row['index']]);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!"]);
        }
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
            $stmt = $db->getPDO()->prepare("INSERT INTO pictures(owner, base64, likes, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $photo, 0, date('Y-m-d H:i:s')]);
            $db = null;
            header('Refresh:0; url=/Camagru');
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!"]);
        }

        return $this->render('camagru/appCamagru.html.php', ['_request' => $request]);
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
        $stmt = $db->getPDO()->prepare("SELECT * FROM pictures WHERE id = ? AND owner = ?");
        $stmt->execute([$photo, $username]);
        $count = $stmt->fetch();
        $message = "Tu n'as pas les droits sur cette photo : ".$photo."! Cliques <a href='/gallery'>ici</a> pour revenir à la gallerie!";
        $status = false;
        if ($count != null)
        {
            $message = "Es tu sûre de vouloir supprimer ta photo ".$photo."?";
            $status = true;
            if (isset($_POST['valid']))
            {
                $stmt = $db->getPDO()->prepare("DELETE FROM pictures WHERE id = ? AND owner = ?");
                $stmt->execute([$photo, $username]);
                $db = null;
                $message = "ta photo ".$photo."a bien été supprimée tu seras redirigé vers Camagru";
                header('Refresh:2; url=/Camagru');
            }
            if (isset($_POST['cancel']))
            {
                header('Refresh:0; url=/Camagru');
            }
        }

        return $this->render('camagru/delete.html.php', ['_request' => $request, 'statement' => [$message, $status]]);
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    public function commentsAction($request)
    {
        if (isset($_SESSION['user'], $_SESSION['connect']) && !empty($_SESSION['user']))
        {
            $img = $_GET['id'];
            $db = new Database();
            $stmt = $db->getPDO()->prepare("SELECT pic_id, login, comments, post_at FROM comments WHERE pic_id = ?");
            $stmt->execute([$img]);
            $comments = $stmt->fetchAll();
            $stmt->closeCursor();
            $author = $comments[0]['login'];
            $stmt = $db->getPDO()->prepare("SELECT id, owner, likes FROM pictures WHERE id = ?");
            $stmt->execute([$img]);
            $image = $stmt->fetchAll();
            $stmt->closeCursor();
            if (isset($_POST['comment']))
            {
                $username = $_SESSION['user'];
                $comment = $_POST['comment'];
                $stmt = $db->getPDO()->prepare("INSERT INTO comments(pic_id, login, comments, post_at) VALUES (?, ?, ?, ?)");
                $stmt->execute([$img, $username, $comment, date('Y-m-d H:i:s')]);
                $stmt->closeCursor();
                if ($username !== $author)
                {
                    $this->sendMail($username, $img, $comment, $author);
                }
            }
            $likes = $this->likeAction($request);

            return $this->render('camagru/comments.html.php', ['_request' => $request, 'data' => $comments, 'image' => $image, 'likes' => $likes]);
        }
        else
        {
            $message = "Tu n'as pas à être ici!";

            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $message]);
        }

    }

    /**
     * @param $request
     * @param null $user
     * @return array
     */
    protected function galleryPageManage($request, $user = null)
    {
        $db = new Database();
        $nbPicturesPage = 8;
        if ($user)
        {
            $query = $db->getPDO()->query('SELECT COUNT(*) AS total_pictures FROM pictures WHERE owner = ?');
            $query->execute([$user]);
        }
        else
            $query = $db->getPDO()->query('SELECT COUNT(*) AS total_pictures FROM pictures');
        $count = $query->fetch();
        $nbPages = ceil($count['total_pictures'] / $nbPicturesPage);
        $page = $_GET['page'];
        switch ($page) {
            case $nbPages == 1:
                $pageNext = 1;
                $pagePrev = 1;
                break;
            case ($page == 0):
                $pageNext = 2;
                $pagePrev = 1;
                break;
            case ($page < 1):
                $pageNext = 2;
                $pagePrev = 1;
                break;
            case ($page == $nbPages):
                $pageNext = $nbPages;
                $pagePrev = $nbPages - 1;
                break;
            default:
                $pageNext = $page + 1;
                $pagePrev = $page - 1;
                break;
        }

        return ['nbPages' => $nbPages, 'nbPicturesPages' => $nbPicturesPage, 'pageNext' => $pageNext, 'pagePrev' => $pagePrev];
    }

    /**
     * @param $request
     * @return array
     */
    protected function getAllPictures($request)
    {
        $db = new Database();
        $query = $db->getPDO()->prepare("SELECT * FROM pictures ORDER BY created_at DESC");
        $query->execute();
        $row = $query->fetchAll();
        foreach ($row as $picture)
        {
            file_put_contents($picture['id'].".png", base64_decode($picture['base64']));
        }

        return $row;
    }

    protected function getPagePictures($request)
    {
        $db = new Database();
        $pages = $this->galleryPageManage($request);
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $pictureStart = ($page - 1) * $pages['nbPicturesPages'];
        $query = $db->getPDO()->prepare("SELECT * FROM pictures ORDER BY created_at DESC LIMIT ?,?");
        $query->execute([$pictureStart, $pages['nbPicturesPages']]);

        return ['pictures' => $query->fetchAll(), 'pages' => $pages, 'index' => $pictureStart];
    }

    /**
     * @param $request
     * @return array
     */
    protected function getAllUserPictures($request)
    {
        $db = new Database();
        $query = $db->getPDO()->prepare("SELECT id, base64, owner, likes, comments FROM pictures WHERE owner = ? ORDER BY 1 DESC");
        $query->execute([$_SESSION['user']]);
        $row = $query->fetchAll();
        foreach ($row as $picture)
        {
            file_put_contents($picture['id'].".png", base64_decode($picture['base64']));
        }

        return $row;
    }

    /**
     * @param $request
     * @return array
     */
    protected function getPageUserPictures($request)
    {
        $db = new Database();
        $pages = $this->galleryPageManage($request, $_SESSION['user']);
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $pictureStart = ($page - 1) * $pages['nbPicturesPages'];
        $query = $db->getPDO()->prepare("SELECT id, base64, owner, likes, comments FROM pictures WHERE owner = ? ORDER BY created_at DESC LIMIT ?,?");
        $query->execute([$_SESSION['user'], $pictureStart, $pages['nbPicturesPages']]);

        return ['pictures' => $query->fetchAll(), 'pages' => $pages, 'index' => $pictureStart];
    }

    /**
     * @param $request
     * @return bool|string
     */
    protected function editingPhoto($request)
    {
        $img = preg_replace("%^data:image\/(?P<imgType>png|jpeg|jpg);base64,%i", '', $_POST['pic']);
        $img = str_replace(' ', '+', $img);
        $img = base64_decode($img);
        $file = uniqid() . '.png';
        $success = file_put_contents($file, $img);
        if ($success !== false)
        {
            $img = $this->reSize($file, 720, 720);
            $filter = $this->getFilter($_POST['filt']);
            $src = imagecreatefromstring($filter['img']);
            $dest = imagecreatefromstring($img);
            if (imagecopy($dest, $src, 30, 40, 0, 0, imagesx($src), imagesy($src)))
            {
                imageAlphaBlending($dest, false);
                imageSaveAlpha($dest, true);
                imagepng($dest, __DIR__."/../../../web/image/".$file);
                imagedestroy($dest);
                imagedestroy($src);
                $photo = file_get_contents(__DIR__."/../../../web/image/".$file);
                $photo = base64_encode($photo);
                unlink(__DIR__."/../../../web/image/temp_".$file);
                unlink(__DIR__."/../../../web/image/temp_".$filter['filterName']);
                unlink(__DIR__."/../../../web/image/".$file);
                unlink(__DIR__."/../../../web/".$file);

                return $photo;
            }
        }

        return false;
    }

    /**
     * @param $request
     * @return Response
     */
    protected function likeAction($request)
    {
        $db = new Database();
        $id = $_GET['id'];
        if (isset($id, $_GET['t']) && !empty($id) && !empty($_GET['t']) && $_SESSION['user'])
        {
            $username = $_SESSION['user'];
            $stmt = $db->getPDO()->prepare("SELECT COUNT(id) FROM pictures WHERE id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetch();
            if ($count[0] == 1)
            {
                $checkLike = $db->getPDO()->prepare('SELECT COUNT(pic_id) FROM likes WHERE pic_id = ? AND login = ?');
                $checkLike->execute([$id, $username]);
                $count = $checkLike->fetch();
                if ($count[0] == 1)
                {
                    $del = $db->getPDO()->prepare('DELETE FROM likes WHERE pic_id = ? AND login = ?');
                    $del->execute([$id, $username]);
                }
                else
                {
                    $add = $db->getPDO()->prepare('INSERT INTO likes (pic_id, login) VALUES (?, ?)');
                    $add->execute([$id, $username]);
                }
            }
            else
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => 'erreur fatale redirection vers l\'acceuil']);
            }

        }
        $likes = $db->getPDO()->prepare("SELECT COUNT(pic_id) FROM likes WHERE pic_id = ?");
        $likes->execute([$id]);
        $likes = $likes->fetch();

        return $likes[0];
    }

    /**
     * @param $pathFilter
     * @return array|bool
     */
    protected function getFilter($pathFilter)
    {
        $tab = explode('/', $pathFilter);
        foreach ($tab as $value)
        {
            if (preg_match("%.png%", $value))
            {
                $filter = $value;
            }
        }
        $file = __DIR__."/../../../web/image/".$filter;
        list($width, $height) = getimagesize($file);
        switch ($filter)
        {
            case 'fuck-hand.png':
                print_r("got fuck");
                $newWidth = $width * 2;
                $newHeight = $height * 2;
                break;
            case 'Cat-baby.png':
                $newWidth = ($width / 8.54) * 2;
                $newHeight = ($height / 8.54) * 2;
                break;
            case 'Troll.png':
                print_r($width);
                $newWidth = ($width / 1.44) * 2;
                $newHeight = ($height / 1.44) * 2;
                break;
            case 'fantome.png':
                print_r($width);
                $newWidth = ($width / 1.44) * 2;
                $newHeight = ($height / 1.44) * 2;
                break;
            default:
                return false;
        }
        $filterReSize = $this->reSize($file, $newWidth, $newHeight);
        $img = base64_encode($filterReSize);
        $img = base64_decode($img);

        return ['img' => $img, 'filterName' => $filter];
    }

    /**
     * @param $file
     * @param $newWidth
     * @param $newHeight
     * @return string
     */
    protected function reSize($file, $newWidth, $newHeight)
    {
        $tab = explode('/', $file);
        foreach ($tab as $value)
        {
            if (preg_match("%.png%", $value))
            {
                $name = $value;
            }
        }
        list($width, $height) = getimagesize($file);
        $dest = imagecreatetruecolor($newWidth, $newHeight);
        imageAlphaBlending($dest, false);
        imageSaveAlpha($dest, true);
        $image = file_get_contents($file);
        $src = imagecreatefromstring($image);
        imagecopyresampled($dest, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $pathTmpFilter = __DIR__."/../../../web/image/temp_".$name;
        imagepng($dest, $pathTmpFilter);
        $newImage = file_get_contents($pathTmpFilter);

        return $newImage;
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
        $stmt = $db->getPDO()->prepare("SELECT login, email FROM users WHERE login = ?");
        $stmt->execute([$destinataire]);
        $data = $stmt->fetchAll();
        $stmt->closeCursor();
        $subject = $author.' t\'as laissé un commentaire';
        $message = <<<MAIL
		<html>
		<head>
		<title>Va voir vite</title>
		</head>
		<body>
			<p>Bonjour $destinataire,</p>
			<br />
			<p>$author t'as laissé un commentaire sur l'une de tes photos Camagru.</p>
			<br />
			<p>$comment</p>
			<br />
			<p>Vas jeter un oeil <a href="http://localhost:8080/comments?id=$imageId">ici</a>!</p>
			<br />
			<p>---------------</p>
			<p>C'est un mail automatique, Merci de ne pas y répondre.</p>
		</body>
		</html>
MAIL;
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ahoareau@student.42.fr' . "\r\n";
        mail($data[0]['email'], $subject, $message, $headers);
    }
}