<?php

require_once 'inc/config.php';
require_once 'inc/functions.php';

session_start();

// constante qui contient le chemin vers le dossier upload

define('BASE', $_SERVER['DOCUMENT_ROOT'] . '/Cours_altrh/PHP/evaluation_bibliotheque_adeline_marina/UPLOADS/');

define('URL', 'http://localhost:8888/COURS_ALTRH/PHP/evaluation_bibliotheque_adeline_marina/UPLOADS/');
