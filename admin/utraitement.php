<?php
    session_start();
    include("config.php");

    if(isset($_GET['isbn']) AND !empty($_GET['isbn'])){
        $isbn = htmlspecialchars($_GET['isbn']);
        $getBook = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
        $getBook->execute(array($isbn));

        if($getBook->rowCount() == 1){
            $data = $getBook->fetch();
            if(isset($_GET['auteur']) AND !empty($_GET['auteur'])){
                $auteur = htmlspecialchars($_GET['auteur']);
                if($auteur != $data['auteur_livres']){
                    $setAuteur = $bdd->prepare("UPDATE livres SET auteur_livres = ? WHERE ISBN_livres = ?");
                    $setAuteur->execute(array($auteur, $isbn));
                    echo "ok";
                }
            }elseif(isset($_GET['exemplaire']) AND !empty($_GET['exemplaire'])){
                $nbre = htmlspecialchars($_GET['exemplaire']);
                if($nbre != $data['nbre_livres']){
                    $setExpl = $bdd->prepare("UPDATE livres SET nbre_livres = ? WHERE ISBN_livres = ?");
                    $setExpl->execute(array($nbre, $isbn));
                    echo "ok";  
                }             
            }elseif(isset($_FILES['couverture']['name'])){
                move_uploaded_file($_FILES['couverture']['tmp_name'], 'assets/uploads/'.basename($_FILES['couverture']['name']));
                $updatePp = $bdd->prepare("UPDATE livres SET couverture_livres = ? WHERE ISBN_livres = ?");
                $updatePp->execute(array('assets/uploads/'.basename($_FILES['couverture']['name']), $isbn));
                echo "ok";
            }else{
                $titre = htmlspecialchars($_GET['titre']);
                if($titre != $data['titre_livres']){
                    $setTitle = $bdd->prepare("UPDATE livres SET titre_livres = ? WHERE ISBN_livres = ?");
                    $setTitle->execute(array($titre, $isbn));
                    echo "ok"; 
                } 
            }
        }else{
            echo "no";
        }
    }else{
        echo "no";
    }