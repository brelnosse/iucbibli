<?php
    session_start();
    include("admin/config.php");
    if(!isset($_SESSION['stu_mat'])){
        header("location: home.php");
    }
    $checkIfUserRequestedBorrow = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND date_fin < CURDATE() AND isok='false'");
    $checkIfUserRequestedBorrow->execute(array($_SESSION['stu_mat']));
    if($checkIfUserRequestedBorrow->rowCount() == 1){
        $deleteRequestedBorrow = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
        $deleteRequestedBorrow->execute(array($_SESSION['stu_mat']));
    }
    if(isset($_GET["del"], $_GET['stu']) AND !empty($_GET['del']) AND !empty($_GET['stu'])){
        $del = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isbn_livres = ?");
        $del->execute(array(htmlspecialchars($_GET["stu"]), htmlspecialchars($_GET["del"])));
        $set = $bdd->prepare("UPDATE livres SET nbre_livres = nbre_livres + 1 WHERE isbn_livres = ?");
        $set->execute(array(htmlspecialchars($_GET["del"])));      
        header("location: panier.php");
    }
    if(isset($_POST['confirmDisconnect'])){
        session_destroy();
        header("location: index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin/assets/fontawesome-free-6.5.2-web/css/fontawesome.css">
    <link rel="stylesheet" href="admin/assets/fontawesome-free-6.5.2-web/css/brands.css"/>
    <link rel="stylesheet" href="admin/assets/fontawesome-free-6.5.2-web/css/solid.css"/>
    <link rel="stylesheet" href="css/panier.css">
    <title>Panier - <?php echo htmlspecialchars($_SESSION['stu_name']); ?></title>
</head>
<body>
    <div class="main-menu">
        <div class="back">
            <a href="home.php?cote=all"><i class="fa fa-arrow-left"></i></a>
        </div>
        <a href="notification.php" class="main-menu__item" style="position: relative"><i class="fa fa-bell"></i>
        <?php
            $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'false'");
            $getnotif->execute(array($_SESSION['stu_mat']));

            if($getnotif->rowCount() == 1){ ?>
                <b style="background-color: red; height: 8px; width: 8px; border-radius: 50%; position: absolute; top: 20px; left: 5px"></b>         
            <?php
            }
        ?>
        </a>
        <a href="panier.php" class="main-menu__item active"><i class="fa fa-shopping-cart"></i></a>
        <div class="menu">
            <span class="smenu">
                <i class="fa fa-user-circle"></i>
                <span style="font-size: 0.8em"><i class="fa fa-angle-down"></i></span>
            </span>
            <div class="s-menu">
                <a href="panier.php?disconnect" class="item"><i class="fa fa-sign-out-alt"></i> Deconnexion</a>
            </div>
        </div>
    </div>
    <div class="itemContainer">
        <?php
            $getUser  = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ?");
            $getUser->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
            $fetchUser = $getUser->fetch();

            if($getUser->rowCount() == 1){ 
                $getbookdetails = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
                $getbookdetails->execute(array($fetchUser['isbn_livres']));
                $getBook = $getbookdetails->fetch();
                
                if($fetchUser["isok"] == "true"){ ?>
                    <a  id="none" class="removefrompanier" style="background-color: #6c6c6c; color: white"><i class="fa fa-cancel" style="margin: 0px 5px"></i> Vider le panier</a>
            <?php
                }else{ ?>
                    <a href="panier.php" id="<?php echo $_SESSION['stu_mat']; ?>" class="removefrompanier"><i class="fa fa-trash" style="margin: 0px 5px"></i> Vider le panier</a>
            <?php
                }
        ?>

            <div class="book" id="<?php echo $getBook["ISBN_livres"]; ?>">
                <img src="admin/<?php echo $getBook["couverture_livres"]; ?>" alt="image livre" class="couverture">
                <?php 
                    if($fetchUser["isok"] == "false"){
                        echo "<span class='etat notyet'><i class='fa fa-stopwatch'></i> En attente d'approbation</span>";
                    }elseif($fetchUser["isok"] == "true"){
                        echo "<span class='etat accorded'><i class='fa fa-check-circle'></i> approuvee</span>";
                    }
                ?>
                <h4 class="bookTitle"><?php echo $getBook["titre_livres"]; ?></h4>
                <span class="bookAuth"><?php echo $getBook["auteur_livres"]; ?></span>
                <?php 
                    if($fetchUser["isok"] == "true"){
                        $today = new DateTime($fetchUser["date_debut"]); 
                        $end = new DateTime($fetchUser["date_fin"]); 
                        $diff = $today->diff($end);
                        $diffday = $diff->days;
    
                        if($diffday == 0){
                            echo "<b style='background-color: #b80000; padding: 5px 20px; margin-top:5px;color: white;font-family: calibri'>L'emprunt prend fin dans Aujourd'hui.</b>";
                        }else{
                            $dateToday = date("Y/m/d");
                            $datetime = new DateTime($dateToday);
                            $lefts = $end->diff($datetime);

                            if($end < $datetime){
                                echo "<b style='background-color: rgba(130,0,0,0.5); padding: 5px 20px; margin-top:5px;color: white;font-family: calibri'><i class='fa fa-triangle-exclamation' style='margin: 5px 8px'></i> L'emprunt est terminer depuis ".$lefts->days." Jours.</b>";
                            }else{
                            if($lefts->days == 1){
                                $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'true'");
                                $getnotif->execute(array($_SESSION['stu_mat']));

                                if($getnotif->rowCount() == 0){
                                    $addnotif = $bdd->prepare("INSERT INTO notification (student_mat, date_fin, day_left) VALUES(:student_mat, :date_fin, :day_left)");
                                    $addnotif->execute(array(
                                        'student_mat' => $_SESSION['stu_mat'],
                                        'date_fin' => $fetchUser['date_fin'],
                                        'day_left' => $lefts->days
                                    ));
                                }else{
                                    $getnotif = $bdd->prepare("UPDATE notification SET viewed = 'false' WHERE student_mat = ?");
                                    $getnotif->execute(array($_SESSION['stu_mat']));
                                }
                                echo "<b style='background-color: rgba(130,0,0,0.5); padding: 5px 20px; margin-top:5px;color: white;font-family: calibri'><i class='fa fa-triangle-exclamation' style='margin: 5px 8px'></i> L'emprunt est presque arriver a son terme.</b>";
                            }
                            if($lefts->days == 0){
                                $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'true'");
                                $getnotif->execute(array($_SESSION['stu_mat']));

                                if($getnotif->rowCount() == 0){
                                    $addnotif = $bdd->prepare("INSERT INTO notification (student_mat, date_fin, day_left) VALUES(:student_mat, :date_fin, :day_left)");
                                    $addnotif->execute(array(
                                        'student_mat' => $_SESSION['stu_mat'],
                                        'date_fin' => $fetchUser['date_fin'],
                                        'day_left' => $lefts->days
                                    ));
                                }
                                echo "<b style='background-color: rgba(130,0,0,0.8); padding: 5px 20px; margin-top:5px;color: white;font-family: calibri'><i class='fa fa-triangle-exclamation' style='margin: 5px 8px'></i> Vous devez rendre le livre aujourd'hui.</b>";
                            }else{
                                if($today == $datetime){
                                    echo "<b style='background-color: rgba(0,150,0); padding: 5px 20px; margin-top:5px;color: white;font-family: calibri'><i class='fa fa-check-circle' style='margin: 5px 8px'></i> La duree de votre emprunt est de ".$diffday." jours et a commencer aujourd'hui</b>";
                                }elseif($today < $datetime){
                                    echo "<b style='background-color: rgba(0,150,0); padding: 5px 20px; margin-top:5px;color: white;font-family: calibri'><i class='fa fa-check-circle' style='margin: 5px 8px'></i> Il vous reste ".$lefts->days." Jours</b>";
                                }else{
                                    echo "<b style='background-color: rgba(0,150,0); padding: 5px 20px; margin-top:5px;color: white;font-family: calibri'><i class='fa fa-check-circle' style='margin: 5px 8px'></i> La duree de votre emprunt est de ".$diffday." jours et commence partir du ".$fetchUser["date_debut"]."</b>";
                                }
                            }
                        }
                        }
                    }
                ?>
            </div>
        <?php
            }else{
                if($getUser->rowCount() == 0){ ?>
                    <div class="nothing" style="background-color: transparent; margin: auto; display: flex; flex-direction: column; align-items:center;height: calc(100vh - 180px)">
                        <img src="assets/undraw_bibliophile_re_xarc.svg" alt="pas de livres" style="flex:1">
                        <span style="font-size: 2em; font-weight: 100; margin: 20px 0px">Votre panier est vide.</span>
                    </div>
            <?php
                    }
            }
        ?>
    </div>
    <?php
        if(isset($_GET['disconnect'])){ ?>
        <form method="post" class="confirmBorrow">
            <div class="c1">
                <p style="background-color: transparent; display: flex;align-items: center">Etes-vous sur de vouloir vous deconnecter ?</p>
                <div class="sc1">
                    <input type="submit" value="Oui" name="confirmDisconnect">
                    <a href="panier.php">annuler</a>
                </div>
            </div>               
        </form>
    <?php
        }
    ?>
    <script>
        const viderPanier = document.querySelector(".removefrompanier");
        const book = document.querySelector(".book");
        let isbnLivre = null;
        viderPanier.addEventListener("click", function(e){
            if(e.target.id != "none"){
                isbnLivre = book.id;
                e.preventDefault();
                if(confirm("Cette action est irreversible !")){
                    window.location = "panier.php?del="+isbnLivre+"&stu="+e.target.id;
                }
            }else{
                alert("veuillez attendre la date de fin de l'emprunt. seul l'administrateur pourra valider le retour du livre");
            }
        })
    </script>
</body>
</html>