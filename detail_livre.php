<?php
require_once 'inc/init.php';

if (!isLogged()) {
    header('Location: connexion.php');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id'])) {
    $data = $db->prepare('SELECT * FROM livre WHERE id_livre = :id');
    $data->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    $data->execute();
}

if ($data->rowCount() <= 0) {
    header('Location: index.php');
    exit();
}

$livre = $data->fetch(PDO::FETCH_ASSOC);

$content = '';
$content .= '<div class="card mb-3 p-2">';
$content .= '<img src="' . URL . $livre['image'] . '" class="card-img-top" alt="...">';
$content .= '<div class="card-body">';
$content .= '<h5 class="card-title">' . $livre['titre'] . '</h5>';
$content .= '<h5 class="card-title">' . $livre['auteur'] . '</h5>';
$content .= '<h6 class="card-subtitle mb-2 text-muted">' . $livre['genre'] . '</h6>';
$content .= '<p class="card-text">' . $livre['resume'] . '</p>';
$content .= '<p class="card-text">' . 'Genre : ' . $livre['genre'] . '</p>';
$content .= '<p class="card-text">' . 'Prix : ' . $livre['prix'] . '€' . '</p>';


$showMessage = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // var_dump($_POST['commentaire']);
    if (isset($_POST['commentaire']) && !empty($_POST['commentaire'])) {
        $commentaire = isset($_POST['commentaire']) ? $_POST['commentaire'] : '';
        $erreurs = [];

        if (empty($commentaire)) {
            $erreurs['commentaire'] = "Le commentaire est obligatoire";
        } elseif (strlen($commentaire) < 20) {
            $erreurs['commentaire'] = "Le commentaire doit faire au moins 20 caractères";
        }


        if (empty($erreurs)) {
            $id_user = $_SESSION['user']['id'];
            $query = $db->prepare('INSERT INTO commentaire (contenu, id_user, id_livre) VALUES (:commentaire, :id_user, :id_livre)');

            $query->bindValue(':commentaire', $commentaire, PDO::PARAM_STR);
            $query->bindValue(':id_user', $id_user, PDO::PARAM_INT);
            $query->bindValue(':id_livre', $livre['id_livre'], PDO::PARAM_STR);
            if ($query->execute()) {
                $showMessage .= '<div class="alert alert-success">Le commentaire a été ajouté</div>';
            } else {
                $showMessage .= '<div class="alert alert-danger">Une erreur est survenue</div>';
            }
        }
    }
    if (isset($_POST['reservation_livre'])) {
        $reservation_livre = $_POST['reservation_livre'];
        $query = $db->prepare("UPDATE livre SET reservation_livre = :reservation_livre WHERE id_livre = $_GET[id]");
        $query->bindValue(':reservation_livre', $reservation_livre, PDO::PARAM_STR);
        if ($query->execute()) {
            $showMessage .= '<div class="alert alert-success">Vous avez reservé le livre</div>';
            header('Location: detail_livre.php?id=' . $livre['id_livre']);
        } else {
            $showMessage .= '<div class="alert alert-danger">Une erreur est survenue lors de la reservation du livre</div>';
        }
    }
}



if (isset($_GET['id'])) {
    $data2 = $db->prepare('SELECT * FROM commentaire WHERE id_livre = :id');
    $data2->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    $data2->execute();

    if ($data->rowCount() <= 0) {
        header('Location: index.php');
        exit();
    }

    $contentCommentaire = '';


    while ($listeCommentaire = $data2->fetch(PDO::FETCH_ASSOC)) {

        $sql = $db->prepare('SELECT * FROM users WHERE id_user = :id');
        $sql->bindValue(':id', $listeCommentaire['id_user'], PDO::PARAM_INT);
        $sql->execute();
        $full_name = $sql->fetch(PDO::FETCH_ASSOC);
        $user_name = $full_name['nom'] . ' ' . $full_name['prenom'];

        $contentCommentaire .= '<div class="card mb-3 p-2">';
        $contentCommentaire .= '<div class="card-body">';
        $contentCommentaire .= '<h5 class="card-title">' .  $user_name . '</h5>';
        $contentCommentaire .= '<p class="card-text">' . $listeCommentaire['contenu'] . '</p>';
        $contentCommentaire .= '<p class="card-text">' . $listeCommentaire['created_at'] . '</p>';
        $contentCommentaire .= '</div>';
    }
}


$allComments = $db->prepare('SELECT * FROM commentaire WHERE id_livre = :id');
$allComments->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$allComments->execute();



?>


<?php require_once 'Common/header.php'; ?>

<div class="container">
    <h1 class="text-center">
        Détail du livre : <?= $livre['titre'] ?>
    </h1>
    <div class="row">
        <div class="col-md-10 m-auto ">
            <?php echo $content; ?>
            <?php if (!empty($livre['reservation_livre'])) : ?>
                <input type="submit" name="reservation_livre" value="Ce livre n'est plus disponible" class="btn btn-lg btn-danger" data-bs-toggle="popover" data-bs-content=" Je ne suis vraiment plus disponible">

            <?php else : ?>
                <form action="" method="post" name="reservation">
                    <input type="submit" name="reservation_livre" value="Emprunte moi" class="btn btn-light">
                </form>
            <?php endif ?>
        </div>
    </div>
</div>
</div>
</div>
<div class="comments">
    <h1 class="text-center">
        Ajouter un commentaire
    </h1>
    <?= $showMessage ?>
    <div class="col-md-10 m-auto ">
        <form action="" method="post" name="comment">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Laissez un commentaire" id="floatingTextarea2" style="height: 100px" name="commentaire"></textarea>
                <?php if (isset($erreurs['commentaire'])) : ?>
                    <div class="text-danger">
                        <?= $erreurs['commentaire'] ?>
                    </div>
                <?php endif; ?>
                <label for="floatingTextarea2">Laissez un commentaire</label>
            </div>
            <button type="submit" class="btn btn-light mt-2 mb-2">Envoyez</button>
        </form>
        <?php
        echo "<h2 class='text-center'>" . $allComments->rowCount() . " commentaire(s)</h2>";
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-10 m-auto">
                    <?php echo $contentCommentaire; ?>
                </div>
            </div>
        </div>


        <?php require_once 'Common/footer.php'; ?>