<?php

if (!isset($index_loaded)) {
    http_response_code(403);
    die('acces direct a ce fichier est interdit');
}

function affichageVertical($tableau)
{
    echo '<table>';
    echo '<tr>';
    echo '<th>cle-indice</th><th>valeur</th>';
    echo'</tr>';
    foreach ($tableau as $cle => $valeur) {
        echo '<tr>';
        echo '<td>'.$cle.'</td><td>'.$valeur.'</td></tr>';
    }
}

function TableauSelectHTML($nom_du_select, $Tableau, $value = '')
{
    $html = '<select class="form-control"  name="'.$nom_du_select.'">';

    foreach ($Tableau as $initial => $nom) {
        if ($value != '' && $value == $initial) {
            $html .= '<option value="'.$initial.'"selected >'.$nom.'</option>';
        } else {
            $html .= '<option value="'.$initial.'">'.$nom.' </option>';
        }
    }
    $html .= '</select>';

    return $html;
}

/**
 * crash() affiche une erreur er enregistre
 * dans un fichier  .log.
 */
function crash($codeErreur, $messageErreur)
{
    $currentDirectory = getcwd();
    echo $currentDirectory.'<br>';
    //ecrire dans le fichier log
    // affichageVertical($_SERVER);
    $ip = $_SERVER['SERVER_ADDR'];
    $temps = date(DATE_RFC2822);
    $myFile = fopen($currentDirectory.'\log\serveur.log', 'a+');
    fwrite($myFile, $temps.'-'.$messageErreur.'-ip-'.$ip.PHP_EOL);
    fclose($myFile);

    //send email
    // mail('vvargasf@isi-mtl.com', 'erruer sur le serveur'.COMPANY_NAME, $messageErreur);

    //envoit reponse http
    http_response_code($codeErreur);
    die($messageErreur);
}

function logAffiche()
{
    $currentDirectory = getcwd();
    $file = file_get_contents($currentDirectory.'\log\serveur.log');
    if ($file === false) {
        return 'fichier no cree';
    } else {
        return $file;
    }
}
