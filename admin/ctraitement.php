<?php
    session_start();
    include("config.php");

    if(isset($_POST['email']) AND !empty($_POST['email']) AND isset($_POST['ucode']) AND !empty($_POST['ucode'])){
        $email = htmlspecialchars($_POST['email']);
        $ucode = htmlspecialchars($_POST['ucode']);//on va hacher le mdp pour le maintenir confidentiel

        $getAdmin = $bdd->prepare("SELECT * FROM admin WHERE email_admin = ? AND ucode_admin = ?");
        $getAdmin->execute(array($email, $ucode));

        if($getAdmin->rowCount() == 1){
            echo "found";
            $_SESSION['email'] = $email;
        }
    }