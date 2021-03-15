<?php

if (!isset($index_loaded)) {
    http_response_code(403);
    die('acces direct a ce fichier est interdit');
}

require_once 'db_pdo.php';
require_once 'tools.php';

class users
{
    public function __construct()
    {
    }

    public function List($filter = '', $message = '')
    {
        $DB = new DB();
        if ($filter == '') {
            $users = $DB->table('users');
        } else {
            $sql_str = 'SELECT * FROM users WHERE id=:id';
            $params = ['id' => $filter];
            $users = $DB->querySelectParam($sql_str, $params);
        }

        $page = new WebPage();
        $page->title = 'users';
        $page->description = 'tableau de list de order';
        $page->content = '';
        if (!empty($users)) {
            if ($message != '') {
                $page->content .= '<p>'.$message.'</p>';
            }
            $page->content .= '<style>';
            $page->content .= 'th,td{border:1px solid black}';
            $page->content .= '</style>';

            $page->content .= '<form action="/index.php?op=420" method="POST">';
            $page->content .= '<label>Chercher user par ID </label>';
            $page->content .= '<input type="number" name="id">';
            $page->content .= '<input type="submit" value="recherche">';
            $page->content .= '</form><br>';
            if (isset($users)) {
                $page->content .= '<table class="table table-striped table-dark">';
                $page->content .= '<tr>';
                $page->content .= '<th scope="col">id</th>';
                $page->content .= '<th scope="col">fullname</th>';
                $page->content .= '<th scope="col">adresse</th>';
                $page->content .= '<th scope="col">ville</th>';
                $page->content .= '<th scope="col">province</th>';
                $page->content .= '<th scope="col">pays</th>';
                $page->content .= '<th scope="col">code_postal</th>';
                $page->content .= '<th scope="col">langue</th>';
                $page->content .= '<th scope="col">autre_langue</th>';
                $page->content .= '<th scope="col">email</th>';
                $page->content .= '<th scope="col">pasword</th>';
                $page->content .= '<th scope="col">spam_ok</th>';
                $page->content .= '</tr>';

                foreach ($users as $user) {
                    $page->content .= '<tr>';
                    $page->content .= '<td scope="row">'.$user['id'].'</td>';
                    $page->content .= '<td scope="row">'.$user['fullname'].'</td>';
                    $page->content .= '<td scope="row">'.$user['adresse'].'</td>';
                    $page->content .= '<td scope="row">'.$user['ville'].'</td>';
                    $page->content .= '<td scope="row">'.$user['province'].'</td>';
                    $page->content .= '<td scope="row">'.$user['pays'].'</td>';
                    $page->content .= '<td scope="row">'.$user['code_postal'].'</td>';
                    $page->content .= '<td scope="row">'.$user['langue'].'</td>';
                    $page->content .= '<td scope="row">'.$user['autre_langue'].'</td>';
                    $page->content .= '<td scope="row">'.$user['email'].'</td>';
                    $page->content .= '<td scope="row">'.$user['pw'].'</td>';
                    $page->content .= '<td scope="row">'.$user['spam_ok'].'</td>';
                    $page->content .= '</tr>';
                }
                $page->content .= '</table>';
            }
        } else {
            $page->content .= '<h3>ce numero de ID n\'existe pas</h3>';
        }

        $page->display();
    }

    public function loginFormAffiche($user_info, $message = '')
    {
        if (isset($_SESSION['email'])) {
            $page = new webPage();
            $page->title = 'deja connecte';
            $page->description = 'page de connection';
            $page->content = 'vous etes deja connecte !!!<a href="index.php?op=5"> deconnectez-vous</a>';
            $page->display();
        } else {
            $page = new webPage();
            $page->title = 'connectez vous';
            $page->description = 'page de connection';
            $page->content = '';
            $page->content .= '<h1>connectez vous</h1>';

            if (isset($_COOKIE['email'])) {
                //usager a deja visite le site web il y a moins de 2 ans
                $page->content .= 'Re-bienvenue '.$_COOKIE['email'];
                $page->content .= '<br> votre derniere connection '.date('d-M-Y', $_COOKIE['derniere_connection']);
            }

            $page->content .= '<div class="alert alert-danger" role="alert">'.$message.'</div>';
            $page->content .= '<form action="/index.php?op=2" method="post">';
            $page->content .= 'Email<input type="email" name="email" maxlength=126 value="'.$user_info['email'].'" >';
            $page->content .= 'mot de passe <input type="password" maxlength="8" name="pw" value="'.$user_info['pw'].'" ><br>';
            $page->content .= '<input type="submit" value="continuer">';
            $page->content .= '</form>';

            $page->display();
        }
    }

    public function incriptionFormAffiche($user_info, $message = '')
    {
        $DB = new DB();
        $strQuery = 'SELECT * FROM provinces';
        $province = $DB->querySelect($strQuery);
        $strQuery = 'SELECT * FROM pays';
        $pay = $DB->querySelect($strQuery);
        $Provinces = [];
        $Pays = [];
        foreach ($province as $table => $code) {
            $Provinces[$code['code']] = $code['nom'];
        }
        foreach ($pay as $table => $code) {
            $Pays[$code['code']] = $code['nom'];
        }
        $prov = TableauSelectHTML('province', $Provinces, $user_info['province']);
        $pay = TableauSelectHTML('pays', $Pays, $user_info['pays']);

        $page = new webPage();
        $page->title = 'connectez vous';
        $page->description = 'page de connection';
        $page->content = <<<HTML
    <p>{$message}</p>
        <form action="/index.php?op=4" method="post" style="width:350px;">       
         fullname<br><input type="text" value="{$user_info['fullname']}" placeholder='nom et prenom' name="fullname" maxlength="50" required><br>
         adress<br><textarea type="text"  name="adresse" maxlength="255" >{$user_info['adresse']}</textarea><br>
         ville<br><input  name="ville" maxlength="50" value="{$user_info['ville']}" ><br>
         province{$prov}<br>
         pays{$pay}<br>
         code postal<br><input type="text" value="{$user_info['code_postal']}" name="code_postal" maxlength="7"><br>
         langue : <br>
         francais<input type="radio"  name="langue" value="fr" checked><br>
         anglais<input type="radio"  name="langue" value="en"><br>
         autre<input type="radio"  name="langue" value="autre"><br>
         Vos intérêts (optionel, vous pouvez sélectionner plusieurs)<br>
        <select class="form-control" name="interets[]" multiple size="3">
            <option value="se">scooter électrique</option>
            <option value="sg">scooter à essence</option>
            <option value="velo_el">vélo électrique</option>
            <option value="velo">velo régulier</option>
            <option value="moto">moto</option>
        </select>
         Email<br><input type="email" value="{$user_info['email']}" name="email" maxlength='126' required><br>
         mot de passe <br><input type="password" maxlength="8" value="{$user_info['pw']}" name="pw" required><br>
         <input type="password" maxlength="8" value="{$user_info['pw2']}" name="pw2" required><br>
         <input type="checkbox" name="spamOk" value="1" checked>spam ok <br>
         <input type="submit" value="continuer">
        
        </form>
    HTML;
        $page->display();
    }

    public function inscriptionVerification()
    {
        $DB = new DB();
        $strQuery = 'SELECT id , email , pw from users';
        $users = $DB->querySelect($strQuery);

        $userInfo = $_POST;
        if (isset($_POST['fullname']) && $_POST != '') {
            $fullName = $_POST['fullname'];
        } else {
            $this->incriptionFormAffiche($userInfo, 'il manque le email');
        }
        if (isset($_POST['adresse']) && $_POST != '') {
            $adressInscription = $_POST['adresse'];
        }
        if (isset($_POST['ville']) && $_POST['ville'] != '') {
            $villeInscript = $_POST['ville'];
        }
        if (isset($_POST['province']) && $_POST['province'] != '') {
            $sql_str = 'SELECT * FROM provinces WHERE code=:province';
            $params = ['province' => $_POST['province']];
            $province = $DB->querySelectParam($sql_str, $params);
            if (count($province) == 0) {
                $this->incriptionFormAffiche($userInfo, 'province no existant');
            } else {
                $provinceInscrip = $_POST['province'];
            }
        }
        if (isset($_POST['pays']) && $_POST['pays'] != '') {
            $sql_str = 'SELECT * FROM pays WHERE code=:pays';
            $params = ['pays' => $_POST['pays']];
            $pays = $DB->querySelectParam($sql_str, $params);
            if (count($pays) == 0) {
                $this->incriptionFormAffiche($userInfo, 'pays no existant');
            } else {
                $paysInscrip = $_POST['pays'];
            }
        }
        if (isset($_POST['langue']) && $_POST['langue'] != '') {
            $langinscrip = $_POST['langue'];
        }
        if (isset($_POST['email']) && $_POST['email'] != '') {
            $emailInscrip = $_POST['email'];
        } else {
            $this->incriptionFormAffiche($userInfo, 'il manque un email');
        }
        if (isset($_POST['pw']) && $_POST['pw'] != '') {
            $pwInscrip = $_POST['pw'];
        } else {
            $this->incriptionFormAffiche($userInfo, 'il manque un passeword');
        }
        if (isset($_POST['pw2']) && $_POST['pw2'] != '') {
            $pw2Inscrip = $_POST['pw2'];
        } else {
            $this->incriptionFormAffiche($userInfo, 'il manque un passeword');
        }
        if ($pwInscrip != $pw2Inscrip) {
            $this->incriptionFormAffiche($userInfo, 'les mots de passe ne match pas');
        }
        if (!isset($_POST['spamOk'])) {
            $user_info['spamOk'] = 0;
        }

        $userFound = false;
        foreach ($users as $user => $info) {
            if ($info['email'] == $emailInscrip) {
                $userFound = true;
                break;
            }
        }
        if ($userFound) {
            $this->incriptionFormAffiche($userInfo, 'ce email est deja utilise');
        } else {
            setcookie('email', $_POST['email'], time() + 2 * (365 * 24 * 60 * 60));
            setcookie('derniere_connection', time(), time() + 2 * (365 * 24 * 60 * 60));
            $_SESSION['email'] = $_POST['email'];
            $this->bienvenueAffichage($emailInscrip);
        }
    }

    public function bienvenueAffichage($user)
    {
        $page = new webPage();
        $page->title = 'bienvenue';
        $page->description = 'page de bienvenue';
        $page->content = <<<HTML
    <h1>bonjour {$user}</h1>
    <div class="alert alert-success" role="alert">vous etes connecte</div>   
    HTML;
        $page->display();
    }

    public function loginFormVerification()
    {
        $DB = new DB();
        $strQuery = 'SELECT id , email , pw from users';
        $users = $DB->querySelect($strQuery);
        $user_info = $_POST;

        if (isset($_POST['email']) && $_POST['email'] != '') {
            $email_form = $_POST['email'];
        } else {
            $this->loginFormAffiche($user_info, 'il manque un email');
        }
        if (isset($_POST['pw']) && $_POST['pw'] != '') {
            $pw_form = $_POST['pw'];
        } else {
            $this->loginFormAffiche($user_info, 'il manque un passeword');
        }

        $userFound = false;
        foreach ($users as $user => $info) {
            if ($info['email'] == $email_form && $info['pw'] == $pw_form) {
                setcookie('email', $_POST['email'], time() + 2 * (365 * 24 * 60 * 60));
                setcookie('derniere_connection', time(), time() + 2 * (365 * 24 * 60 * 60));
                $_SESSION['email'] = $_POST['email'];
                $this->bienvenueAffichage($email_form);
                $userFound = true;
                break;
            }
        }
        if ($userFound == false) {
            $this->loginFormAffiche($user_info, 'email ou password sont incorrect');
        }
    }
}
