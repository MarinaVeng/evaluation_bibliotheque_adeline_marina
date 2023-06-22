<?php require_once 'common/header.php';
require_once 'inc/init.php'; ?>

<?php
if (isLogged()) {
    header('Location: profil.php');
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    foreach ($_POST as $key => $value) {
        $_POST[$key] = htmlspecialchars(addslashes($value));
    }

    $errors = [];

    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $motdepasse = isset($_POST['motdepasse']) ? $_POST['motdepasse'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (isset($_GET['action']) && $_GET['action'] == 'update') {
        $nomAvatar = $_POST['oldAvatar'];
    }

    if (empty($nom)) {
        $errors['nom'] = "Le nom est obligatoire";
    }

    if (empty($prenom)) {
        $errors['prenom'] = "Le prénom est obligatoire";
    }

    if (empty($email)) {
        $errors['email'] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'email n'est pas valide";
    }

    if (empty($motdepasse)) {
        $errors['motdepasse'] = "Le mot de passe est obligatoire";
    }

    if (empty($confirm_password)) {
        $errors['confirm_password'] = "La confirmation du mot de passe est obligatoire";
    } elseif ($confirm_password != $motdepasse) {
        $errors['confirm_password'] = "La confirmation du mot de passe ne correspond pas";
    }

    if (!empty($_FILES['avatar']['name'])) {

        $tabExt = ['jpg', 'png', 'jpeg'];

        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        if (!in_array($extension, $tabExt)) {
            $errors['avatar'] = "L'extension n'est pas valide";
        }

        if ($_FILES['avatar']['size'] > 2000000) {
            $errors['avatar'] = "L'avatar ne doit pas dépasser 2Mo";
        }
        $nomAvatar = bin2hex(random_bytes(16)) . '.' . $extension;
        move_uploaded_file($_FILES['avatar']['tmp_name'], BASE . $nomAvatar);
    }

    if (empty($errors)) {

        $req = $db->prepare("INSERT INTO users (avatar, nom, prenom, email, motdepasse) VALUES (:avatar, :nom, :prenom, :email, :motdepasse)");
        $req->bindValue(':avatar', $nomAvatar, PDO::PARAM_STR);
        $req->bindValue(':nom', $nom, PDO::PARAM_STR);
        $req->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->bindValue(':motdepasse', password_hash($motdepasse, PASSWORD_DEFAULT), PDO::PARAM_STR);

        if ($req->execute()) {
            header('Location: connexion.php');
        }
    }
}




?>

<div class="container">
    <div class="row text-center">
        <h1 class="display-1 my-3">
            Inscription
        </h1>
    </div>

    <div class="row">
        <div class="col-md-6 m-auto shadow p-4">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="file" class="form-control" placeholder="Avatar" name="avatar" value="<?= $avatar ?>">

                <?php if (!empty($avatar)) : ?>
                    <img src="<?= URL  . $avatar ?>" alt="" width="200">
                <?php endif; ?>
                <input type="hidden" name="oldAvatar" value="<?= $avatar ?>">
                <input type="text" name="nom" placeholder="Entrez votre nom" class="form-control mt-2" value="<?= isset($nom) ? $nom : '' ?>">
                <?php if (isset($errors['nom'])) : ?>
                    <small class="text-danger"><?= $errors['nom']; ?></small>
                <?php endif; ?>
                <input type="text" name="prenom" placeholder="Entrez votre prénom" class="form-control mt-2" value="<?= isset($prenom) ? $prenom : '' ?>">
                <?php if (isset($errors['prenom'])) : ?>
                    <small class="text-danger"><?= $errors['prenom']; ?></small>
                <?php endif; ?>
                <input type="email" name="email" placeholder="Entrez votre email" class="form-control mt-2" value="<?= isset($email) ? $email : '' ?>">
                <?php if (isset($errors['email'])) : ?>
                    <small class="text-danger"><?= $errors['email']; ?></small>
                <?php endif; ?>
                <input type="password" name="motdepasse" placeholder="Entrez votre mot de passe" class="form-control mt-2" value="">
                <?php if (isset($errors['motdepasse'])) : ?>
                    <small class="text-danger"><?= $errors['motdepasse']; ?></small>
                <?php endif; ?>
                <input type="password" name="confirm_password" placeholder="Confirmez votre mot de passe" class="form-control mt-2" value="">
                <?php if (isset($errors['confirm_password'])) : ?>
                    <small class="text-danger"><?= $errors['confirm_password']; ?></small>
                <?php endif; ?>
                <div class="d-grid gap-2 col-6 mx-auto">
                    <input type="submit" class="btn btn-primary mt-2" value="S'inscrire">
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once 'common/footer.php'; ?>