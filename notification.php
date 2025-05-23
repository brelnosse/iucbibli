<?php
    session_start();
    include("admin/config.php");
    if(!isset($_SESSION['stu_mat'])){
        header("location: home.php");
    }
    // $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'true'");
    // $getnotif->execute(array($_SESSION['stu_mat']));

    // if($getnotif->rowCount() == 1){
    //     echo "done";
    // }else{
    //     echo "none";
    // }
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
    <link rel="stylesheet" href="admin/assets/fontawesome-free-6.5.2-web/css/regular.css"/>
    <link rel="stylesheet" href="admin/assets/fontawesome-free-6.5.2-web/css/solid.css"/>
    <link rel="stylesheet" href="css/notification.css">
    <title>Panier - <?php echo htmlspecialchars($_SESSION['stu_name']); ?></title>
</head>
<body>
    <div class="loaderContainer">
        <i class="fa-regular fa-bell fa-shake" style="font-size: 3em; color: rgba(150,0,0)"></i>
        <b>notifications</b>
    </div>
    <script>
        const loaderContainer = document.querySelector(".loaderContainer");

        setTimeout(() => {
                loaderContainer.style.display = "none";
        }, 1000);
    </script>
    <div class="main-menu">
        <div class="back">
            <a href="home.php?cote=all"><i class="fa fa-arrow-left"></i></a>
        </div>
        <a href="notification.php" class="main-menu__item active"><i class="fa fa-bell"></i></a>
        <a href="panier.php" class="main-menu__item"><i class="fa fa-shopping-cart"></i></a>
        <div class="menu">
            <span class="smenu">
                <i class="fa fa-user-circle"></i>
                <span style="font-size: 0.8em"><i class="fa fa-angle-down"></i></span>
            </span>
            <div class="s-menu">
                <a href="notification.php?disconnect" class="item"><i class="fa fa-sign-out-alt"></i> Deconnexion</a>
            </div>
        </div>
    </div>
    <div class="itemContainer">
        <h2 class="title">Notification(s)</h2>

        <h3>Message important</h3>
        <?php 
             $get  = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'false'");
             $get->execute(array(htmlspecialchars($_SESSION['stu_mat'])));  

             if($get->rowCount() == 1){
                $getd = $get->fetch();
                ?>
                <div class="notif warning">
                    <i class="fa-solid fa-bell" style="color:#b80000; margin: 5px; margin-left: 0px; font-size: 0.8em"></i>
                    <p><i class="fa fa-triangle-exclamation" style="color: rgb(130,150,0)"></i> Votre emprunt arrive a terme dans <?php echo $getd["day_left"]; ?> Jours</p>
                </div>
            <?php
             }else{
                echo "<p class='rien'>Aucun message important pour le moment .</p>";
             }
        ?>
        <h3>Message de l'administrateur</h3>
        <?php
            $getUser  = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND isok != 'false'");
            $getUser->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
            
            if($getUser->rowCount() > 0){
                while($fetchUser = $getUser->fetch()){
                    $getBook = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
                    $getBook->execute(array(htmlspecialchars($fetchUser['isbn_livres'])));
    
                    if($getBook->rowCount() == 1){ 
                        $book = $getBook->fetch();
                            if($fetchUser['isok'] == "refus"){ ?>
                            <div class="notif denied">
                            <span class="headerInfoContainer"><span class="dateReceived"><?php echo $fetchUser["repliedDate"]; ?></span><i class="fa-solid fa-bell" style="color:#b80000; margin: 5px; margin-left: 0px; font-size: 0.8em"></i></span>
                                <p><i class="fa fa-times-circle" style="color: #b80000"></i> Votre demande d'emprunt pour le livre <?php echo $book["titre_livres"]; ?> a ete accepter refuser<?php echo $fetchUser['date_debut'];?></p>
                            </div>
                        <?php
                            }else{ 
                                    if($fetchUser['viewedbystudent'] == 'true'){ ?>
                                    <div class="notif" style="background-color: #f1ffe7; box-shadow: 0px 0px 16px 8px rgba(200,200,200,0.05)">
                                        <span class="headerInfoContainer"><span class="dateReceived"><?php echo $fetchUser["repliedDate"]; ?></span><i class="fa-solid fa-bell" style="color:#b80000; margin: 5px; margin-left: 0px; font-size: 0.8em"></i></span>
                                        <p><i class="fa fa-check-circle" style="color: rgba(0,130,0)"></i> Votre demande d'emprunt pour le livre <b>"<?php echo $book["titre_livres"]; ?>"</b> a ete accepter et debute le <?php echo $fetchUser['date_debut'];?></p>
                                    </div>
                                <?php
                                    }else{ ?>
                                    <div class="notif">
                                        <span class="headerInfoContainer"><span class="dateReceived"><?php echo $fetchUser["repliedDate"]; ?></span><i class="fa-solid fa-bell" style="color:#b80000; margin: 5px; margin-left: 0px; font-size: 0.8em"></i></span>
                                        <p><i class="fa fa-check-circle" style="color: rgb(0,130,0)"></i> Votre demande d'emprunt pour le livre <b>"<?php echo $book["titre_livres"]; ?>"</b> a ete accepter et debute le <?php echo $fetchUser['date_debut'];?></p>
                                    </div>
                                <?php    
                                }
                                ?>
                        <?php
                            }
                }
            }
            }else{
                if($getUser->rowCount() == 0){ ?>
                    <div class="nothing" style="background-color: transparent; margin: auto; display: flex; flex-direction: column; align-items:center;height: calc(100vh - 180px)">
                        <img src="assets/undraw_bibliophile_re_xarc.svg" alt="pas de livres" style="flex:1">
                        <span style="font-size: 2em; font-weight: 100; margin: 20px 0px">Vous n'avez pas encore de notifications.</span>
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
                    <a href="notification.php">annuler</a>
                </div>
            </div>               
        </form>
    <?php
        }
    ?>
</body>
</html>
<?php
    $getnotif = $bdd->prepare("UPDATE notification SET viewed = 'true' WHERE student_mat = ?");
    $getnotif->execute(array($_SESSION['stu_mat']));

    $setView = $bdd->prepare("UPDATE emprunt SET viewedbystudent = 'true' WHERE matricule_etudiant = ?");
    $setView->execute(array($_SESSION['stu_mat']));