<?php
    session_start();
    include("admin/config.php");

    if(isset($_GET["nom"], $_GET["numero"], $_GET["date_debut"], $_GET["date_fin"], $_GET["mode"], $_GET["matricule"], $_GET["isbn"])){
        $nom = htmlspecialchars($_GET["nom"]);
        $numero = htmlspecialchars($_GET["numero"]);
        $dateDebut = htmlspecialchars($_GET["date_debut"]);
        $dateFin = htmlspecialchars($_GET["date_fin"]);
        $mode = htmlspecialchars($_GET["mode"]);
        $matricule = htmlspecialchars($_GET["matricule"]);
        $isbn = htmlspecialchars($_GET["isbn"]);

        $getemprunt = $bdd->prepare("SELECT * FROM emprunt where matricule_etudiant  = ?");
        $getemprunt->execute(array($matricule));

        if($getemprunt->rowCount() == 0){
            $addEmprunt = $bdd->prepare("INSERT INTO emprunt(matricule_etudiant, isbn_livres, nom_etudiant, numero_etudiant, mode, date_debut, date_fin) VALUES(?, ?, ?, ?, ?, ?, ?)");
            $addEmprunt->execute(array($matricule, $isbn, $nom, $numero, $mode, $dateDebut, $dateFin));

            $updateCote = $bdd->query("UPDATE livres SET cote_livres = cote_livres + 1 WHERE ISBN_livres = ".$isbn);
            $updateNbre = $bdd->query("UPDATE livres SET nbre_livres = nbre_livres - 1 WHERE ISBN_livres = ".$isbn." AND nbre_livres > 0");
            echo "ok";
        }else{
            echo "already";
        }
    }