<?php

if (!isset($index_loaded)) {
    http_response_code(403);
    die('acces direct a ce fichier est interdit');
}
 require_once 'global_defines.php';
 require_once 'order.php';

 class WebPage
 {
     public $lang = 'fr-CA';
     public $title = 'ScooterElectrique.com - Acceuil';
     public $description = 'Le plus vaste de choix de scooters électrique à Montréal - Vente - Service - Pièces';
     public $author = 'Votre nom ici';
     public $icon = WEB_SITE_ICON;
     public $content;

     public function __construct()
     {
     }

     public function display()
     {
         require_once 'head.php';
         if (!isset($this->content)) {
             crash(500, 'acces direct a ce fichier est interdit');
         } ?>

<body>

    <!-- PAGE HEADER -->
    <header>
        <h2 style="background-color:black;color:white;padding:10px">
            <?=$this->title; ?>
        </h2>
    </header>

    <!-- BARRE DE NAVIGATION -->
    <nav style="background-color:gray;color:white;padding:10px">
        <a href='/'>Acceuil |</a>
        <a href="/index.php?op=98">| Log du serveur | </a>
        <a href="/index.php?op=99">| $_SERVER |</a>
        <?php
        if (isset($_SESSION['email'])) {
            echo $_SESSION['email'];
            echo'<a href="/index.php?op=400">| afficher orders |</a>';
            echo'<a href="/index.php?op=420">| afficher users|</a>';
            echo '<a href="/index.php?op=5">| deconnection | </a>';
        } else {
            ?>
        <a href="/index.php?op=1">| connection | </a>
        <a href="/index.php?op=3">| inscription | </a>
        <?php
        } ?>
    </nav>

    <!-- CONTENT -->
    <?php echo $this->content; ?>

    <!-- FOOTER -->
    <footer style="background-color:black;color:white;padding:10px">
        Exercice par victor &copy;
        <p><?=COMPANY_STREET_ADDRESS.' '.COMPANY_CITY.' '.COMPANY_PROVINCE.' '.COMPANY_COUNTRY.' '.COMPANY_POSTAL_CODE; ?><br>
            <?='email: '.COMPAGNIE_EMAIL; ?><br>
            <?=' telephone: '.COMPAGNIE_TEL; ?>
        </p>
    </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>

</html>
<?php
    die();
     }

     //fin functgion display
 }//fin calsse
