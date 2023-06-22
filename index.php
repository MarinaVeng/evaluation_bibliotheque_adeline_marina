<?php
require_once 'inc/init.php';

?>

<?php require_once 'common/header.php'; ?>

<div class="container">
    <div class="row text-center">
        <h1>
            Bienvenue dans la bibliothèque Niedbalveng !
        </h1>
        <h3>
            Nos derniers livres :
        </h3>
    </div>
    <div class="row d-flex justify-content-between">
        <?php
        $data = $db->prepare('SELECT * FROM livre ORDER BY created_at DESC');
        $data->execute();

        while ($livres = $data->fetch(PDO::FETCH_ASSOC)) {

            $author = $db->prepare('SELECT nom,prenom FROM users WHERE id_user = :id');
            $author->bindValue(':id', $livres['id_user'], PDO::PARAM_INT);
            $author->execute();
            $user = $author->fetch(PDO::FETCH_ASSOC);

            $userName = $user['prenom'] . ' ' . $user['nom'];

            $card = '';
            $card .= '<div class="card my-2" style="width: 18rem;">';
            $card .= '<img src="' . URL . $livres['image'] . '" class="card-img-top img-thumbnail" alt="...">';
            $card .= '<div class="card-body">';
            $card .= '<h5 class="card-title">' . $livres['titre'] . '</h5>';
            $card .= '<h6 class="card-subtitle mb-2 text-muted">' . $userName . '</h6>';
            $card .= '<h6 class="card-subtitle mb-2 text-muted">' . $livres['annee'] . '</h6>';
            $card .= '<p class="card-text">' . substr($livres['resume'], 0, 150) . '...</p>';
            $card .= '<p class="card-text">' . 'Genre : ' . $livres['genre'] . '</p>';
            $card .= '<p class="card-text">' . 'Prix : ' . $livres['prix'] . '€' . '</p>';
            $card .= '</div>';
            $card .= '</div>';
            echo $card;
        }
        ?>
    </div>
</div>


<?php require_once 'common/footer.php'; ?>