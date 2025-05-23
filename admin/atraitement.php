<?php
    session_start();
    include("config.php");

    if(isset($_GET['titre']) AND !empty($_GET['titre']) AND isset($_GET['auteur']) AND !empty($_GET['auteur']) AND isset($_GET['isbn']) AND !empty($_GET['isbn']) AND isset($_SESSION['email'])){
        $isbn = htmlspecialchars($_GET['isbn']);
        $titre = htmlspecialchars($_GET['titre']);
        $auteur = htmlspecialchars($_GET['auteur']);

        if(isset($_GET['nbre']) AND isset($_FILES['couverture']['name'])){

            $nbre = htmlspecialchars($_GET['nbre']);

            $getBook = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
            $getBook->execute(array($isbn));

            if($getBook->rowCount() == 0){
                move_uploaded_file($_FILES['couverture']['tmp_name'], 'assets/uploads/'.basename($_FILES['couverture']['name']));
                $addbook = $bdd->prepare("INSERT INTO livres VALUES(?, ?, ?, ?, CURDATE(), ?, ?, ?)");
                $addbook->execute(array($isbn, $titre, $auteur, 0, $nbre, htmlspecialchars($_SESSION['email']), 'assets/uploads/'.basename($_FILES['couverture']['name'])));
                echo "ok";
            }else{
                echo "ex";
            }
        }
    }else{
        echo "no";
    }