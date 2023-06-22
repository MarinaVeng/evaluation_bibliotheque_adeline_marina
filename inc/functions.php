<?php

// fonction qui vérifie si l'utilisateur est connecté. Pour cela je regarde si j'ai une session user et si elle n'est pas vide
function isLogged()
{
    if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
        return true;
    } else {
        return false;
    }
}
