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
            $userGallery = $this->getPageUserPictures();
            $modalGallery = $this->getAllUserPictures();

            return $this->render('camagru/appCamagru.html.php', ['_request' => $request, 'gallery' => $userGallery['pictures'], 'modalGallery' => $modalGallery, 'anchor' => 'user-gallery']);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!", 'anchor' => null]);
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
            $page = $this->getPagePictures();
            if ($page['pictures'] == null)
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Pas de photo à afficher!", 'anchor' => 'Camagru']);
            }
            $modalGallery = $this->getAllPictures();

            return $this->render('camagru/gallery.html.php', ['_request' => $request, 'gallery' => $page['pictures'], 'pages' => $page['pages'], 'modalGallery' => $modalGallery, 'index' => $page['index'], 'anchor' => 'gallery']);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!", 'anchor' => null]);
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
            $row = $this->getPageUserPictures();
            if ($row['pictures'] == null)
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Pas de photo à afficher!", 'anchor' => 'Camagru']);
            }
            $modalGallery = $this->getAllUserPictures();

            return $this->render('camagru/gallery.html.php', ['_request' => $request, 'gallery' => $row['pictures'], 'pages' => $row['pages'], 'modalGallery' => $modalGallery, 'index' => $row['index'], 'anchor' => 'user-gallery']);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!", 'anchor' => null]);
        }
    }
    /**
     * @param array $request
     *
     * @return Response
     */
    public function miniGalleryAction($request)
    {
        if (isset($_SESSION['user'], $_SESSION['connect']) && !empty($_SESSION['user'])) {
            $row = $this->getPageUserPictures();
            if ($row['pictures'] == null)
            {
                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Pas de photo à afficher!", 'anchor' => 'Camagru']);
            }
            $modalGallery = $this->getAllUserPictures();

            return $this->render('camagru/fragment/_mini-gallery.html.php', ['_request' => $request, 'gallery' => $row['pictures'], 'pages' => $row['pages'], 'modalGallery' => $modalGallery, 'index' => $row['index'], 'anchor' => 'user-gallery']);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!", 'anchor' => null]);
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
        $photoStudio = $this->editingPhoto();
        $this->cleanEditingPhoto($photoStudio['file'], $photoStudio['filter']);
        if (isset($photoStudio['newPhoto'], $username))
        {
            $db = new Database();
            $stmt = $db->getPDO()->prepare("INSERT INTO pictures(owner, base64, likes, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $photoStudio['newPhoto'], 0, date('Y-m-d H:i:s')]);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!", 'anchor' => null]);
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
        $username = $_SESSION['user'];
        $photo = $this->getPictureByOwner($_GET['id'], $username);
        $state = ['message' => "Tu n'as pas les droits sur cette photo : ".$photo."! Cliques <a href='/gallery'>ici</a> pour revenir à la gallerie!", 'status' => false];
        if ($photo != null)
        {
            $state = ['message' => "Es tu sûre de vouloir supprimer ta photo ".$photo."?", 'status' => true];
            if (isset($_POST['valid']))
            {
                $state = $this->deletePictureOfOwner($photo, $username);

                return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => $state['message'], 'anchor' => "Camagru"]);
            }
            if (isset($_POST['cancel']))
            {
                header('Refresh:0; url=/Camagru');

                return $this->render('camagru/appCamagru.html.php', ['_request' => $request]);
            }
        }

        return $this->render('camagru/delete.html.php', ['_request' => $request, 'statement' => [$state['message'], $state['status']]]);
    }

    /**
     * @param array $request
     *
     * @return Response
     */
    public function commentsAction($request)
    {
        $imgId = $_GET['id'];
        $user = $_SESSION['user'];
        if (isset($user, $_SESSION['connect']) && !empty($user))
        {
            $comments = $this->selectCommentsByPicture($imgId);
            $imgOwner = $this->getPictureById($imgId)[0]['owner'];
            $image = $this->getPictureById($imgId);
            $db = new Database();
            $comment = $this->addCommentToPicture($imgId, $user, $_POST['comment']);
            if ($comment['author'] !== $imgOwner && !empty($comment['author']))
            {
                $this->sendMail($imgOwner, $imgId, $comment['comment'], $comment['author']);
            }
            $likes = $this->likeAction($imgId, $user);

            return $this->render('camagru/comments.html.php', ['_request' => $request, 'data' => $comments, 'image' => $image, 'likes' => $likes]);
        }
        else
        {
            return $this->render('security/checkAccount.html.php', ['_request' => $request, 'statement' => "Tu n'as pas à être ici!", 'anchor' => null]);
        }
    }

    /**
     * @param $imgId
     * @param $author
     * @param $comment
     * @return array|bool
     */
    protected function addCommentToPicture($imgId, $author, $comment)
    {
        if (isset($comment) && !empty($comment))
        {
            $db = new Database();
            $stmt = $db->getPDO()->prepare("INSERT INTO comments(pic_id, login, comments, post_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$imgId, $author, $comment, date('Y-m-d H:i:s')]);

            return ['imgId' => $imgId, 'comment' => $comment, 'author' => $author];
        }
        return false;
    }

    /**
     * @param $picture
     * @return array
     */
    protected function selectCommentsByPicture($picture)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare("SELECT * FROM comments WHERE pic_id = ?");
        $stmt->execute([$picture]);
        return $stmt->fetchAll();
    }

    /**
     * @param $imgId
     * @return array
     */
    protected function getPictureById($imgId)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare("SELECT * FROM pictures WHERE id = ?");
        $stmt->execute([$imgId]);
        return $stmt->fetchAll();
    }

    /**
     * @param null $user
     * @return array
     */
    protected function galleryPageManage($user = null)
    {
        $db = new Database();
        $nbPicturesPage = 8;
        if ($user)
        {
            $query = $db->getPDO()->query('SELECT COUNT(*) AS total_pictures FROM pictures WHERE owner = ?');
            $query->execute([$user]);
        }
        else
        {
            $query = $db->getPDO()->query('SELECT COUNT(*) AS total_pictures FROM pictures');
        }
        $count = $query->fetch();
        $nbPages = ceil($count['total_pictures'] / $nbPicturesPage);
        $page = $_GET['page'];
        if (isset($page) && !empty($page)) {
            if ($nbPages == 1) {
                $pageNext = 1;
                $pagePrev = 1;
            } elseif ($page == 0 || $page <= 1) {
                $pageNext = 2;
                $pagePrev = 1;
            } elseif ($page == $nbPages) {
                $pageNext = $nbPages;
                $pagePrev = $nbPages - 1;
            } else {
                $pageNext = $page + 1;
                $pagePrev = $page - 1;
            }
            return ['nbPages' => $nbPages, 'nbPicturesPages' => $nbPicturesPage, 'pageNext' => $pageNext, 'pagePrev' => $pagePrev];
        }
        $page = null;
        return ['nbPages' => $nbPages, 'nbPicturesPages' => $nbPicturesPage, 'pageNext' => null, 'pagePrev' => null];
    }

    /**
     * @return array
     */
    protected function getAllPictures()
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

    /**
     * @return array
     */
    protected function getPagePictures()
    {
        $db = new Database();
        $pages = $this->galleryPageManage();
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
     * @return array
     */
    protected function getAllUserPictures()
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
     * @return array
     */
    protected function getPageUserPictures()
    {
        $db = new Database();
        $pages = $this->galleryPageManage($_SESSION['user']);
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
     * @return array|bool
     */
    protected function editingPhoto()
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

            return ['newPhoto' => $this->createNewPhotoFromFile(__DIR__."/../../../web/image/temp/".$file, imagecreatefromstring($img), imagecreatefromstring($filter['img'])), 'file' => $file, 'filter' => $filter];
        }

        return false;
    }

    /**
     * @param $id
     * @param $username
     * @return string
     */
    protected function likeAction($id, $username)
    {
        $this->likesManager($id, $username);
        $db = new Database();
        $likes = $db->getPDO()->prepare("SELECT COUNT(pic_id) FROM likes WHERE pic_id = ?");
        $likes->execute([$id]);

        return $likes->fetchColumn();
    }

    /**
     * @param $imgId
     * @param $username
     */
    protected function likesManager($imgId, $username)
    {
        if (isset($imgId, $_GET['t']) && !empty($imgId) && !empty($_GET['t']) && $username)
        {
            $db = new Database();
            if ($this->checkIfUserAlreadyLike($imgId, $username) >= 1)
            {
                $del = $db->getPDO()->prepare('DELETE FROM likes WHERE pic_id = ? AND login = ?');
                $del->execute([$imgId, $username]);
            }
            else
            {
                $add = $db->getPDO()->prepare('INSERT INTO likes (pic_id, login) VALUES (?, ?)');
                $add->execute([$imgId, $username]);
            }
        }
    }

    /**
     * @param $imgId
     * @param $user
     * @return string
     */
    protected function checkIfUserAlreadyLike($imgId, $user)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare('SELECT COUNT(pic_id) FROM likes WHERE pic_id = ? AND login = ?');
        $stmt->execute([$imgId, $user]);
        return $stmt->fetchColumn();
    }

    /**
     * @param $path
     * @return bool
     */
    protected function getPngName($path)
    {
        $tab = explode('/', $path);
        foreach ($tab as $value)
        {
            if (preg_match("%.png%", $value))
            {
                return $value;
            }
        }
        return false;
    }

    /**
     * @param $pathFilter
     * @return array|bool
     */
    protected function getFilter($pathFilter)
    {
        $filter = $this->getPngName($pathFilter);
        $file = __DIR__."/../../../web/image/filter/".$filter;
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
        $name = $this->getPngName($file);
        list($width, $height) = getimagesize($file);
        $dest = imagecreatetruecolor($newWidth, $newHeight);
        imageAlphaBlending($dest, false);
        imageSaveAlpha($dest, true);
        $image = file_get_contents($file);
        $src = imagecreatefromstring($image);
        imagecopyresampled($dest, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $this->createDirectory(__DIR__.'/../../../web/image/', 'temp');
        $pathTmpFilter = __DIR__."/../../../web/image/temp/temp_".$name;
        imagepng($dest, $pathTmpFilter);
        $newImage = file_get_contents($pathTmpFilter);

        return $newImage;
    }

    /**
     * @param $photo
     * @param $owner
     * @return array
     */
    protected function deletePictureOfOwner($photo, $owner)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare("DELETE FROM pictures WHERE id = ? AND owner = ?");
        $stmt->execute([$photo, $owner]);
        unlink(__DIR__."/../../../web/".$photo.".png");

        return['message' => "ta photo ".$photo."a bien été supprimée tu seras redirigé vers Camagru", 'status' => true];
    }

    /**
     * @param $idPicture
     * @param $owner
     * @return string
     */
    protected function getPictureByOwner($idPicture, $owner)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare("SELECT * FROM pictures WHERE id = ? AND owner = ?");
        $stmt->execute([$idPicture, $owner]);
        return $stmt->fetchColumn();
    }

    /**
     * @param $parentDirectoryPath
     * @param $directoryName
     */
    protected function createDirectory($parentDirectoryPath, $directoryName)
    {
        $dir = scandir($parentDirectoryPath);
        if (array_search('temp', $dir) === false)
        {
            mkdir($parentDirectoryPath.$directoryName.'/');
        }
    }

    protected function getEmailByUser($user)
    {
        $db = new Database();
        $stmt = $db->getPDO()->prepare("SELECT login, email FROM users WHERE login = ?");
        $stmt->execute([$user]);

        return $stmt->fetchAll()[0]['email'];
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
        $mail = $this->getEmailByUser($destinataire);
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui présentent des bogues.
        {
            $passage_ligne = "\r\n";
        }
        else
        {
            $passage_ligne = "\n";
        }
        $subject = $author . ' t\'as laissé un commentaire';
        $message= "Content-Transfer-Encoding: 8bit".$passage_ligne;
        $message.= "<html><head><title>Va voir vite</title></head>";
        $message.= "<body><p>Bonjour ".$destinataire.",</p><br />";
        $message.="<p>".$author." t'as laissé un commentaire sur l'une de tes photos Camagru.</p><br />";
		$message.="<p>".$comment."</p><br />";
		$message.="<p>Vas jeter un oeil <a href='http://localhost:8080/comments?id=".$imageId."'>ici</a>!</p><br />";
        $message.="<p>--------</p><p>C'est un mail automatique, Merci de ne pas y répondre.</p></body></html>";
        $headers = 'MIME-Version: 1.0'.$passage_ligne;
        $headers .= 'Content-type: text/html; charset=iso-8859-1'.$passage_ligne;
        $headers .= 'To: ' . $mail . ''.$passage_ligne;
        $headers .= 'From: ahoareau@student.42.fr'.$passage_ligne;
        $headers .= 'Cc: comment_archive@example.com'.$passage_ligne;
        $headers .= 'Bcc: comment_verif@example.com'.$passage_ligne;
        mail($mail, $subject, $message, $headers);
    }
    /**
     * @param $pathFile
     * @param $dest
     * @param $src
     * @return bool|string
     */
    private function createNewPhotoFromFile($pathFile, $dest, $src)
    {
        if (imagecopy($dest, $src, 30, 40, 0, 0, imagesx($src), imagesy($src))) {
            imageAlphaBlending($dest, false);
            imageSaveAlpha($dest, true);
            imagepng($dest, $pathFile);
            imagedestroy($dest);
            imagedestroy($src);
            $photo = file_get_contents($pathFile);
            $photo = base64_encode($photo);

            return $photo;
        }
        return false;
    }

    /**
     * @param $file
     * @param $filter
     */
    private function cleanEditingPhoto($file, $filter)
    {
        unlink(__DIR__."/../../../web/image/temp/temp_".$file);
        unlink(__DIR__."/../../../web/image/temp/temp_".$filter['filterName']);
        unlink(__DIR__."/../../../web/image/temp/".$file);
        unlink(__DIR__."/../../../web/".$file);
    }
}