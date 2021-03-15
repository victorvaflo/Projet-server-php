<?php
/**
 * projet server - ceci est le point d'entre.
 */
session_start();

$index_loaded = true;

 //fichier requis
require_once 'global_defines.php';
require_once 'tools.php';
require_once 'webpage.php';
require_once 'users.php';

//debut du code
// $etuds = ['isabel', 'yannick', 'christian', 'victor', 'dmytro'];
// affichageVertical($etuds);

// echo'<p>test dossier serveur</p>';
// echo '<img src="'.WEB_SITE_ICON.'" alt="icon de la compagnie">';

//controller
if (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = 0;
}
//CODE OP ENTRE 0 ET 4 SEULMENT SI PAS CONNECTE
if (!isset($_SESSION['email']) and $op >= 5) {
    crash(401, 'vous devez etre connectez pour acceder a cette page');
}

switch ($op) {
    case 0:
        HomePage();
    break;

    case 1:
        $users = new users();
        $user_info = ['email' => '', 'pw' => ''];
        $users->loginFormAffiche($user_info);

    break;
    case 2:
        $users = new users();
        $users->loginFormVerification();
    break;
    case 3:
        $users = new users();
        $user_info = ['fullname' => '', 'adresse' => '', 'ville' => '', 'province' => '', 'pays' => '', 'code_postal' => '', 'langue' => '', 'email' => '', 'pw' => '', 'pw2' => ''];
        $users->incriptionFormAffiche($user_info);
    break;
    case 4:
        $users = new users();
        $users->inscriptionVerification();
    break;
    //TOUS LES OP SUIVANTES L'USAGE DOIS ETRE CONNECTE
    case 5:
        //deconnection
        $_SESSION['email'] = null;
        HomePage();
    break;
    case 98:

        $page = new WebPage();
        $page->title = 'log du serveur';
        $page->content = logAffiche();
        $page->display();

    break;
    case 99:
        affichageVertical($_SERVER);
    break;
    //ORDERS -----------------------------------------------------------------------------------------------------------
    case 400:
        $order = new order();
        if (isset($_POST['orderNumber'])) {
            $order->list($_POST['orderNumber']);
        } else {
            $order->list();
        }

    break;
    case 401:
        $order = new order();
        $order->updateVerification();
    break;
    case 405:
        $order = new order();
        $id = $_GET['id'];
        $order->afficherOrder($_GET['id']);
    break;
    case 406:
        $userInfo = ['customerNumber' => '', 'requiredDate' => '', 'comments' => ''];
        $order = new order();
        $order->addOrder($userInfo);
    break;
    case 407:
        $order = new order();
        $order->addOrderVerification();
    break;
    case 408:
        $order = new order();
        $orderNumber = $_GET['id'];
        $order->removeOrder($orderNumber);
    break;
    case 409:
        $userInfo = ['customerNumber' => '', 'requiredDate' => '', 'comments' => '', 'status' => '', 'shippedDate' => '', 'orderDate' => '', 'customerNumber' => ''];
        $order = new order();
        $order->updateOrder($userInfo, $_GET['id']);
    break;
    case 410:
        //SERVICE (API) RETOURNE LA LISTE DES ORDER
        //EN FORMAT JSON
        $order = new order();
        $order->ListJson();
    break;
    case 420:
        $users = new users();
        if (isset($_POST['id'])) {
            $users->list($_POST['id']);
        } else {
            $users->list();
        }
    break;
    default:
    crash(400, 'operation invalide');
}

function HomePage()
{
    $page = new WebPage();
    $page->title = 'Bienvenue';
    $page->description = 'bienvenue a veloelectrique.com';
    $page->content = <<<HTML
<h1>bonjour le monde</h1>
<p>hi</p>
<button type="button" class="btn btn-danger">Danger</button>
HTML;

    $page->display();
}
