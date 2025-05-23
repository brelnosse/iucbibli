<?php
    session_start();
    include("admin/config.php");

    if(isset($_GET['fullname']) AND !empty($_GET['fullname']) AND isset($_GET['matricule']) AND !empty($_GET['matricule']) AND isset($_GET['ecole']) AND !empty($_GET['ecole']) AND isset($_GET['niveau']) AND !empty($_GET['niveau']) AND isset($_GET['email']) AND isset($_GET['phone']) AND !empty($_GET['phone'])){
        $fullname = htmlspecialchars($_GET['fullname']);
        $matricule = htmlspecialchars($_GET['matricule']);
        $ecole = htmlspecialchars($_GET['ecole']);
        $niveau = htmlspecialchars($_GET['niveau']);
        $email = htmlspecialchars($_GET['email']);
        $phone = htmlspecialchars($_GET['phone']);

        $getStudent = $bdd->prepare("SELECT * FROM etudiant WHERE matricule_etudiant = ?");
        $getStudent->execute(array($matricule));

        if($getStudent->rowCount() > 0){
            echo "ex";
        }else{
            $addStudent = $bdd->prepare("INSERT INTO etudiant VALUES(? , ? , ?, ?, ?, ?, ?, ?, ?)");
            $addStudent->execute(array($matricule, $fullname, $ecole, $niveau, 0, "pas d'info", $phone, $email, 0));
            echo "ok";
        }
    }else{
        echo "no";
    }