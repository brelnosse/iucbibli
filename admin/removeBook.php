<?php
    include("config.php");
    if(isset($_GET['isbn']) AND !empty($_GET['isbn'])){
        $delBook = $bdd->prepare("DELETE FROM livres WHERE ISBN_livres = ?");
        $delBook->execute(array(htmlspecialchars($_GET['isbn'])));

        echo "ok";
    }