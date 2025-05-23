<?php
    session_start();
    include("admin/config.php");

    if(isset($_GET['fullname']) AND !empty($_GET['fullname']) AND isset($_GET['matricule']) AND !empty($_GET['matricule'])){
        $fullname = htmlspecialchars($_GET['fullname']);
        $matricule = htmlspecialchars($_GET['matricule']);

        $getStudent = $bdd->prepare("SELECT * FROM etudiant WHERE nom_etudiant = ? AND matricule_etudiant = ?");
        $getStudent->execute(array($fullname, $matricule));

        if($getStudent->rowCount() == 1){
            $stud = $getStudent->fetch();

            $_SESSION['stu_mat'] = $stud['matricule_etudiant'];
            $_SESSION['stu_name'] = $stud['nom_etudiant'];
            $_SESSION['stu_numero'] = $stud['numero_etudiant'];

            echo "ok";
        }else{
            echo "nex";
        }
    }else{
        echo "no";
    }