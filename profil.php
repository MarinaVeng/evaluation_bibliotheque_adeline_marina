<?php
require_once 'Common/header.php';
require_once 'inc/init.php';

if (!isLogged()) {
    header('Location: connexion.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

$data = $db->prepare('SELECT id_livre AS `ID Livre`, image AS `Image`, titre AS `Titre`, auteur AS `Auteur`, genre AS `Genre`, resume AS `Resume`, prix AS `Prix`, annee AS `Année`, created_at AS `Crée le`, id_user AS `ID Utilisateur`  FROM livre WHERE id_user = :user_id');
$data->bindValue(':user_id', $_SESSION['user']['id'], PDO::PARAM_INT);
$data->execute();

$livres = $data->fetchAll(PDO::FETCH_ASSOC);
?>



<div class="container">
    <div class="row text-center">
        <h1 class="display-1 my-3">
            Mon compte
        </h1>
    </div>

    <div class="row">
        <div class="card">
            <h5 class="card-header">Vos Informations</h5>
            <div class="card-body">
                <?= '<img class="img-thumbnail w-25 mb-3" src="' . URL . $_SESSION['user']['avatar'] . '" class="card-img-top" alt="...">'; ?>
                <h5 class="card-title">
                    <?= $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']; ?>
                </h5>
                <p class="card-text">
                    <?= $_SESSION['user']['email']; ?>
                </p>
                <p>
                    Membre depuis le <?= $_SESSION['user']['date_inscription']; ?>
                </p>
                <a href="profil.php?action=logout" class="btn btn-danger">Deconnexion</a>
                <a href="ajout_livre.php" class="btn btn-light">Ajout de livre</a>
            </div>
        </div>
    </div>

    <div class="row">
        <h4 class="text-center my-4">
            Vos livres
        </h4>
        <?php if (count($livres) <= -0) : ?>
            <div class="alert alert-info">
                Vous n'avez pas encore ajouter de livre
            </div>
        <?php else : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <?php
                        for ($i = 0; $i < $data->columnCount(); $i++) {
                            $colonne = $data->getColumnMeta($i);
                            echo "<th>$colonne[name]</th>";
                        }


                        echo '<th>Actions</th>';

                        foreach ($livres as $livre) {
                            echo '<tr>';
                            echo '<td>' . $livre['ID Livre'] . '</td>';
                            echo '<td> <img class="img-fluid" src="' . URL . $livre['Image'] . '"></td>';
                            echo '<td>' . $livre['Titre'] . '</td>';
                            echo '<td>' . $livre['Auteur'] . '</td>';
                            echo '<td>' . $livre['Genre'] . '</td>';
                            echo '<td>' . $livre['Resume'] . '</td>';
                            echo '<td>' . $livre['Prix'] . '</td>';
                            echo '<td>' . $livre['Année'] . '</td>';
                            echo '<td>' . $livre['Crée le'] . '</td>';
                            echo '<td>' . $livre['ID Utilisateur'] . '</td>';
                            echo '<td><a href="ajout_livre.php?action=update&id_livre=' . $livre['ID Livre'] . '" class="btn btn-warning mb-1">Modifier</a>
                            <a href="ajout_livre.php?action=delete&id_livre=' . $livre['ID Livre'] . '" class="btn btn-danger">Supprimer</a>
                            </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tr>
                </thead>

            </table>
        <?php endif; ?>


    </div>

</div>

<?php require_once 'Common/footer.php'; ?>