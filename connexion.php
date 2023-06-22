<?php
require_once 'inc/init.php';

if (isLogged()) {
    header('Location: profil.php');
}

$showMessage = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $errors = [];

    if (empty($email)) {
        $errors['email'] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'email n'est pas valide";
    }

    if (empty($password)) {
        $errors['password'] = "Le mot de passe est obligatoire";
    }

    if (empty($errors)) {
        // C - Vérification de l'existence de l'utilisateur en base de données
        $query = $db->prepare('SELECT * FROM users WHERE email = :email');
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->execute();

        // rowCount() permet de compter le nombre de lignes retournées par la requête. Si le nombre de lignes est supérieur à 0, cela signifie que l'utilisateur existe en base de données
        if ($query->rowCount() > 0) {

            //Je récupère les données de l'utilisateur dans une variable $user
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // je vérifie si le mot de passe saisi par l'utilisateur correspond au mot de passe en base de données($user['password'])
            // password_verify() permet de vérifier si le mot de passe saisi par l'utilisateur correspond au mot de passe haché en base de données

            if (password_verify($password, $user['motdepasse'])) {
                // je stocke les données de l'utilisateur dans la session.Ici je ne fais plus session_start() car je l'ai déjà fait dans init.php

                $_SESSION['user']['id'] = $user['id_user'];
                $_SESSION['user']['avatar'] = $user['avatar'];
                $_SESSION['user']['nom'] = $user['nom'];
                $_SESSION['user']['prenom'] = $user['prenom'];
                $_SESSION['user']['email'] = $user['email'];
                $_SESSION['user']['date_inscription'] = $user['date_inscription'];

                // je redirige l'utilisateur vers la page de profil

                header('Location: profil.php');
                exit; // je stoppe l'exécution du code


            } else {
                $showMessage = '<div class="alert alert-warning text-center"> 
                email ou mot de passe incorrect
                </div>';
            }
        } else {
            $showMessage = '<div class="alert alert-danger"> 
                email ou mot de passe incorrect
            </div>';
        }
    }
}

?>


<?php require_once 'Common/header.php'; ?>

<div class="container">
    <div class="row text-center">
        <h1 class="display-1 my-3">
            Connexion
        </h1>
    </div>

    <div class="row">
        <div class="col-md-6 m-auto shadow p-4">
            <?= $showMessage ?>
            <form action="" method="post">

                <input type="email" name="email" placeholder="Entrez votre email" class="form-control my-3">
                <input type="password" name="password" placeholder="Entrez votre mot de passe" class="form-control my-3">
                <button type="submit" class="btn btn-primary">Connexion</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'Common/footer.php'; ?>