<?php
require_once 'inc/init.php';


if (!isLogged()) {
    header('Location: connexion.php');
}

$errors = [];

$showMessage = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $titre = isset($_POST['titre']) ? $_POST['titre'] : '';
    $auteur = isset($_POST['auteur']) ? $_POST['auteur'] : '';
    $genre = isset($_POST['genre']) ? $_POST['genre'] : '';
    $resume = isset($_POST['resume']) ? $_POST['resume'] : '';
    $annee = isset($_POST['annee']) ? $_POST['annee'] : '';
    $prix = isset($_POST['prix']) ? $_POST['prix'] : '';

    if (isset($_GET['action']) && $_GET['action'] == 'update') {
        $nomImage = $_POST['oldImage'];
    }

    if (empty($titre)) {
        $errors['titre'] = "Le titre est obligatoire";
    }

    if (empty($auteur)) {
        $errors['auteur'] = "L'auteur est obligatoire";
    }

    if (empty($resume)) {
        $errors['resume'] = "Le résumé est obligatoire";
    } elseif (strlen($resume) < 20) {
        $errors['resume'] = "Le résumé doit faire au moins 20 caractères";
    }

    if (empty($annee)) {
        $errors['annee'] = "L'année de publication est obligatoire";
    }

    if (empty($prix)) {
        $errors['prix'] = "Le prix est obligatoire";
    }

    if (!empty($_FILES['image']['name'])) {

        $tabExt = ['jpg', 'png', 'jpeg'];

        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        if (!in_array($extension, $tabExt)) {
            $errors['image'] = "L'extension n'est pas valide";
        }

        if ($_FILES['image']['size'] > 2000000) {
            $errors['image'] = "L'image ne doit pas dépasser 2Mo";
        }
        $nomImage = bin2hex(random_bytes(16)) . '.' . $extension;
        move_uploaded_file($_FILES['image']['tmp_name'], BASE . $nomImage);
    }

    if (empty($errors)) {

        if (isset($_GET['action']) && $_GET['action'] == 'update') {
            $id_livre = $_POST['id'];
            $query = $db->prepare('UPDATE livre SET titre = :titre, auteur = :auteur, genre = :genre, resume = :resume, annee = :annee, prix = :prix, image = :image WHERE id_livre = :id_livre');
            $query->bindValue(':titre', $titre, PDO::PARAM_STR);
            $query->bindValue(':auteur', $auteur, PDO::PARAM_STR);
            $query->bindValue(':genre', $genre, PDO::PARAM_STR);
            $query->bindValue(':resume', $resume, PDO::PARAM_STR);
            $query->bindValue(':image', $nomImage, PDO::PARAM_STR);
            $query->bindValue(':annee', $annee, PDO::PARAM_INT);
            $query->bindValue(':prix', $prix, PDO::PARAM_INT);
            $query->bindValue(':id_livre', $id_livre, PDO::PARAM_INT);
            if ($query->execute()) {
                $showMessage .= '<div class="alert alert-success">Le livre a été modifié</div>';
            } else {
                $showMessage .= '<div class="alert alert-danger">Une erreur est survenue</div>';
            }
        } else {

            $id_user = $_SESSION['user']['id'];
            $query = $db->prepare('INSERT INTO livre (titre,auteur,genre,resume,image,annee,prix,id_user) VALUES (:titre,:auteur,:genre,:resume,:image,:annee,:prix,:id_user)');

            $query->bindValue(':titre', $titre, PDO::PARAM_STR);
            $query->bindValue(':auteur', $auteur, PDO::PARAM_STR);
            $query->bindValue(':genre', $genre, PDO::PARAM_STR);
            $query->bindValue(':resume', $resume, PDO::PARAM_STR);
            $query->bindValue(':annee', $annee, PDO::PARAM_INT);
            $query->bindValue(':prix', $prix, PDO::PARAM_INT);
            $query->bindValue(':image', $nomImage, PDO::PARAM_STR);
            $query->bindValue(':id_user', $id_user, PDO::PARAM_INT);
            if ($query->execute()) {
                $showMessage .= '<div class="alert alert-success">Le livre a été ajouté</div>';
            } else {
                $showMessage .= '<div class="alert alert-danger">Une erreur est survenue</div>';
            }
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id_livre = $_GET['id_livre'];
    $query = $db->prepare('DELETE FROM livre WHERE id_livre= :id_livre');
    $query->bindValue(':id_livre', $id_livre, PDO::PARAM_INT);
    if ($query->execute()) {
        $showMessage .= '<div class="alert alert-success">Le livre a été supprimé</div>';
        header('Location: profil.php');
    } else {
        $showMessage .= '<div class="alert alert-danger">Une erreur est survenue</div>';
    }
}


if (isset($_GET['action']) && $_GET['action'] == 'update') {
    $id_livre = $_GET['id_livre'];
    $query = $db->prepare('SELECT * FROM livre WHERE id_livre = :id_livre');
    $query->bindValue(':id_livre', $id_livre, PDO::PARAM_INT);
    if ($query->execute()) {
        $livre = $query->fetch(PDO::FETCH_ASSOC);
    }
}

$id_livre = isset($livre['id_livre']) ? $livre['id_livre'] : '';
$titre = isset($livre['titre']) ? $livre['titre'] : '';
$auteur = isset($livre['auteur']) ? $livre['auteur'] : '';
$genre = isset($livre['genre']) ? $livre['genre'] : '';
$resume = isset($livre['resume']) ? $livre['resume'] : '';
$annee = isset($livre['annee']) ? $livre['annee'] : '';
$prix = isset($livre['prix']) ? $livre['prix'] : '';
$image = isset($livre['image']) ? $livre['image'] : '';

?>


<?php require_once 'Common/header.php'; ?>

<div class="container">
    <?php foreach ($errors as $error) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endforeach; ?>

    <div class="row text-center">
        <h1 class="display-1 my-3">
            <?php if (isset($_GET['action']) && $_GET['action'] == 'update') : ?>
                Modification de livre
            <?php else : ?>
                Ajout de livre
            <?php endif; ?>
        </h1>
    </div>

    <div class="row">
        <div class="col-md-9 m-auto">
            <?= $showMessage ?>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="">
                <div class="input-group mb-3">
                    <input type="file" class="form-control" placeholder="Image" name="image" value="<?= $image ?>">
                </div>
                <?php if (!empty($image)) : ?>
                    <img src="<?= URL  . $image ?>" alt="" width="200">
                <?php endif; ?>
                <input type="hidden" name="oldImage" value="<?= $image ?>">

                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Titre" name="titre" value="<?= $titre ?>">
                </div>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Auteur" name="auteur" value="<?= $auteur ?>"">
                </div>

                <select class=" form-select mb-2" aria-label="Default select example" name="genre" value="<?= $genre ?>">
                    <option value=" Romance" selected>Romance</option>
                    <option value="Policier">Policier</option>
                    <option value="Manga">Manga</option>
                    <option value="BD">BD</option>
                    <option value="Horreur">Horreur</option>
                    <option value="Science Fiction">Science Fiction</option>
                    </select>

                    <div class="input-group mb-3">
                        <textarea name="resume" class="form-control" placeholder="Résumé" rows="10"><?= $resume ?></textarea>
                    </div>

                    <div class=" input-group mb-3">
                        <input type="text" class="form-control" placeholder="Année de publication" name="annee" value="<?= $annee ?>">
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Prix" name="prix" value="<?= $prix ?>">
                    </div>

                    <div class="d-grid gap-2 col-6 mx-auto">
                        <?php if (isset($_GET['action']) && $_GET['action'] == 'update') : ?>
                            <button type="submit" class="btn btn-warning">Modifier</button>
                        <?php else : ?>
                            <button type="submit" class="btn btn-light">Ajouter</button>
                        <?php endif; ?>
                    </div>


            </form>
        </div>
    </div>


</div>

<?php require_once 'Common/footer.php'; ?>