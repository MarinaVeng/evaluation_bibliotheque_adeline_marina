<?php
require_once 'inc/init.php';
require_once 'common/header.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['genre']) && isset($_GET['prix'])) {
        $genre = $_GET['genre'];
        $prix = $_GET['prix'];

        if ($prix == 'Inférieur à 10') {
            $data2 = $db->prepare('SELECT * FROM livre WHERE prix < :prix AND genre = :genre');
            $prix_req = 10;
            $data2->bindParam(':genre', $genre, PDO::PARAM_STR);
            $data2->bindParam(':prix', $prix_req, PDO::PARAM_INT);
            $data2->execute();
        } elseif ($prix == 'Compris entre 10 et 20') {
            $data2 = $db->prepare('SELECT * FROM livre WHERE prix BETWEEN :minPrice AND :maxPrice AND genre = :genre');
            $minPrice = 10;
            $maxPrice = 20;
            $data2->bindParam(':minPrice', $minPrice, PDO::PARAM_INT);
            $data2->bindParam(':maxPrice', $maxPrice, PDO::PARAM_INT);
            $data2->bindParam(':genre', $genre, PDO::PARAM_STR);
            $data2->execute();
        } elseif ($prix == 'Supérieur à 20') {
            $data2 = $db->prepare('SELECT * FROM livre WHERE prix >= :prix AND genre = :genre');
            $prix_req = 20;
            $data2->bindParam(':genre', $genre, PDO::PARAM_STR);
            $data2->bindParam(':prix', $prix_req, PDO::PARAM_INT);
            $data2->execute();

            //die;
        }
    }
}
?>

<div class="container">
    <div class="row text-center">
        <h1>
            Bienvenue dans la bibliothèque NiedbalVeng !
        </h1>
        <h6>Veuillez sélectionner un genre</h6>
        <form method="get">
            <select class="form-select mb-4 mt-4" aria-label="Default select example" name="genre" value="<?= isset($genre) ? $genre : '' ?>">
                <option <?php if (isset($genre) && $genre == 'Policier') echo 'selected'; ?>>Policier</option>
                <option <?php if (isset($genre) && $genre == 'Romance') echo 'selected'; ?>>Romance</option>
                <option <?php if (isset($genre) && $genre == 'Manga') echo 'selected'; ?>>Manga</option>
                <option <?php if (isset($genre) && $genre == 'BD') echo 'selected'; ?>>BD</option>
                <option <?php if (isset($genre) && $genre == 'Horreur') echo 'selected'; ?>>Horreur</option>
                <option <?php if (isset($genre) && $genre == 'Science Fiction') echo 'selected'; ?>>Science Fiction</option>
            </select>
            <h6>Veuillez sélectionner un ordre de prix</h6>
            <select class="form-select mb-4 mt-4" aria-label="Default select example" name="prix" value="<?= isset($prix) ? $prix : '' ?>">
                <option <?php if (isset($prix) && $prix == 'Inférieur à 10') echo 'selected'; ?>>Inférieur à 10</option>
                <option <?php if (isset($prix) && $prix == 'Compris entre 10 et 20') echo 'selected'; ?>>Compris entre 10 et 20</option>
                <option <?php if (isset($prix) && $prix == 'Supérieur à 20') echo 'selected'; ?>>Supérieur à 20</option>
            </select>
            <button type="submit" class="btn btn-light mb-4">Valider</button>
        </form>
    </div>
    <div class="row d-flex justify-content-between">
        <?php
        if (isset($_GET['genre']) && isset($_GET['prix'])) {
            //$livres = $data->fetch(PDO::FETCH_ASSOC);


            while ($livres = $data2->fetch(PDO::FETCH_ASSOC)) {
                $card = '';
                $card .= '<div class="card my-2" style="width: 18rem;">';
                $card .= '<img src="' . URL . $livres['image'] . '" class="card-img-top img-thumbnail" alt="...">';
                $card .= '<div class="card-body">';
                $card .= '<h5 class="card-title">' . $livres['titre'] . '</h5>';
                $card .= '<h5 class="card-title">' . $livres['auteur'] . '</h5>';
                $card .= '<h6 class="card-subtitle mb-2 text-muted">' . $livres['annee'] . '</h6>';
                $card .= '<p class="card-text">' . substr($livres['resume'], 0, 150) . '...</p>';
                $card .= '<p class="card-text">' . 'Genre : ' . $livres['genre'] . '</p>';
                $card .= '<p class="card-text">' . 'Prix : ' . $livres['prix'] . '€' . '</p>';
                $card .= '<a href="detail_livre.php?id=' . $livres['id_livre'] .
                    '" class="btn btn-light">Lire la suite</a>';
                $card .= '</div>';
                $card .= '</div>';
                echo $card;
            }
            echo '<div class="d-grid mt-4"><a href="consult_livre.php" class="btn btn-light">Retour à la liste</a></div>';
        } else {
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
                $card .= '<h5 class="card-title">' . $livres['auteur'] . '</h5>';
                $card .= '<h6 class="card-subtitle mb-2 text-muted">' . $livres['annee'] . '</h6>';
                $card .= '<p class="card-text">' . substr($livres['resume'], 0, 150) . '...</p>';
                $card .= '<p class="card-text">' . 'Genre : ' . $livres['genre'] . '</p>';
                $card .= '<p class="card-text">' . 'Prix : ' . $livres['prix'] . '€' . '</p>';
                $card .= '<a href="detail_livre.php?id=' . $livres['id_livre'] .
                    '" class="btn btn-light">Lire la suite</a>';
                $card .= '</div>';
                $card .= '</div>';
                echo $card;
            }
        }
        ?>
    </div>
</div>

<?php require_once 'common/footer.php'; ?>