<?php 
    session_start();
    include("admin/config.php");
    if(!isset($_SESSION['stu_mat'])){
        header("location: index.php");
    }
    $getUser  = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ?");
    $getUser->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
    $fetchUser = $getUser->fetch();
    
    if($getUser->rowCount() > 0){
        $dateToday = date("Y/m/d");
        $datetime = new DateTime($dateToday);
        $end = new DateTime($fetchUser["date_fin"]); 
        $lefts = $end->diff($datetime);

        if($datetime > $end){
            $getUserCaution = $bdd->prepare("SELECT * FROM caution WHERE mat_etu = ?");
            $getUserCaution->execute(array($_SESSION['stu_mat']));

            if($getUserCaution->rowCount() == 0){
                $AddCaution = $bdd->prepare("INSERT INTO caution(mat_etu, nom_etu, caution, date_dernier_ajout) VALUES(?, ?, ?, CURDATE())");
                $AddCaution->execute(array($_SESSION['stu_mat'], $_SESSION['stu_name'], 500*$lefts->days));
            }else{
                $updateCaution = $bdd->prepare("UPDATE caution SET caution =  caution* ".$lefts->days.", date_dernier_ajout = CURDATE() WHERE mat_etu = ? AND date_dernier_ajout != CURDATE()");
                $updateCaution->execute(array($_SESSION['stu_mat']));
            }
        }
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
            }
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
        }
    }

    $checkIfUserRequestedBorrow = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND date_fin < CURDATE() AND isok='false'");
    $checkIfUserRequestedBorrow->execute(array($_SESSION['stu_mat']));
    if($checkIfUserRequestedBorrow->rowCount() == 1){
        $deleteRequestedBorrow = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
        $deleteRequestedBorrow->execute(array($_SESSION['stu_mat']));
    }

    $checkIfUserGaveBackBook = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND isok='false' AND date_fin < CURDATE() AND matricule_etudiant IN (SELECT matricule_etudiant FROM etudiant WHERE nbre_emprunt_etudiant > 0)");
    $checkIfUserGaveBackBook->execute(array($_SESSION['stu_mat']));

    if($checkIfUserGaveBackBook->rowCount() > 0){
        //on ajoute dans la table de l'historique
        // $deleteRequestedBorrow = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
        // $deleteRequestedBorrow->execute(array($_SESSION['stu_mat']));
        //on retire de la table emprunt
        $deleteRequestedBack = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
        $deleteRequestedBack->execute(array($_SESSION['stu_mat']));
        echo "Vous pouvez de nouveaux effectuer des emprunts";
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

    <link rel="stylesheet" href="css/home.css">
    <title>IUCBibli</title>
</head>
<body>
    <!-- <div class="loaderContainer">
            <i class="fa fa-book-open-reader fa-fade" style="font-size: 3em; color: rgba(150,0,0)"></i>
            <b>Bibliotheque</b>
    </div>
    <script>
        // const loaderContainer = document.querySelector(".loaderContainer");

        // setTimeout(() => {
        //         loaderContainer.style.display = "none";
        // }, 1500);
    </script> -->
    <form method="post" class="main-menu">
        <a href="home.php?action=showCautionInfo&cote=all" class="walletItem">
            <i class="fa fa-piggy-bank"></i>
            <span class="amount">
                <?php 
                    $getAmount = $bdd->prepare("SELECT * FROM caution WHERE mat_etu = ?");
                    $getAmount->execute(array(htmlspecialchars($_SESSION['stu_mat'])));

                    if($getAmount->rowCount() == 0){
                        echo "0 FCFA";
                    }else{
                        $getAmountfetched = $getAmount->fetch();
                        echo $getAmountfetched['caution']." FCFA";
                    }
                ?>
            </span>
        </a>
        <div class="searchContainer">
            <input type="search" class="search" placeholder="Entrer le titre, ou le nom de l'auteur.." name="q" value="<?php 
                if(isset($_POST['q']) AND !empty($_POST['q'])){
                    echo htmlspecialchars($_POST['q']);
                }
            ?>">
            <button type="submit" class="searchBtn"><i class="fa fa-search"></i></button>
        </div>
        <a href="notification.php" class="main-menu__item" style="position: relative"><i class="fa fa-bell"></i>
        <?php
            $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'false'");
            $getnotif->execute(array($_SESSION['stu_mat']));
            $getnotif2 = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND viewedbystudent = 'false' AND repliedDate IS NOT NULL");
            $getnotif2->execute(array($_SESSION['stu_mat']));

            if($getnotif->rowCount() == 1 || $getnotif2->rowCount() == 1){ ?>
                <b class="bbull" style="background-color: red; height: 8px; width: 8px; border-radius: 50%; position: absolute; top: 20px; left: 5px"></b>         
                <script>
                    if(Notification.permission === "granted"){
                        new Notification("Nouvelle notification");          
                    }else{
                        Notification.requestPermission()
                        .then(permission =>{
                            if(permission === "granted"){
                                new Notification("Nouvelle notification");
                            }
                        })
                    }
                </script>
            <?php
            }
        ?>
        </a>
        <a href="panier.php" class="main-menu__item"><i class="fa fa-shopping-cart"></i></a>
        <div class="menu">
            <span class="smenu">
                <i class="fa fa-user-circle"></i>
                <span style="font-size: 0.8em"><i class="fa fa-angle-down"></i></span>
            </span>
            <div class="s-menu">
                <a href="home.php?cote=all&disconnect" class="item"><i class="fa fa-sign-out-alt"></i> Deconnexion</a>
            </div>
        </div>
</form>

    <div class="onglets">
        <?php
            if(isset($_GET['cote']) AND !empty($_GET['cote'])){
                switch($_GET['cote']){
                    case 'trespopulaire': ?>
                    <a href="home.php?cote=all" class="onglet">Tout</a>
                    <a href="home.php?cote=populaire" class="onglet">Populaire</a>
                    <a href="home.php?cote=trespopulaire" class="onglet activeTab">Tr&eacute;s populaire</a>                  
        <?php
                    break;
                    case 'populaire': ?>
                        <a href="home.php?cote=all" class="onglet">Tout</a>
                        <a href="home.php?cote=populaire" class="onglet activeTab">Populaire</a>
                        <a href="home.php?cote=trespopulaire" class="onglet">Tr&eacute;s populaire</a>                  
            <?php
                        break;
                    case 'all': ?>
                        <a href="home.php?cote=all" class="onglet activeTab">Tout</a>
                        <a href="home.php?cote=populaire" class="onglet">Populaire</a>
                        <a href="home.php?cote=trespopulaire" class="onglet">Tr&eacute;s populaire</a>                  
            <?php
                    break;
                    default: ?>
                        <a href="home.php?cote=all" class="onglet activeTab">Tout</a>
                        <a href="home.php?cote=populaire" class="onglet">Populaire</a>
                        <a href="home.php?cote=trespopulaire" class="onglet">Tr&eacute;s populaire</a>                  
            <?php
                    break;
                }
            }else{ ?>
                <a href="home.php?cote=all" class="onglet activeTab">Tout</a>
                <a href="home.php?cote=populaire" class="onglet">Populaire</a>
                <a href="home.php?cote=trespopulaire" class="onglet">Tr&eacute;s populaire</a> 
        <?php
            }
        ?>
    </div>
    <?php
        if(isset($_GET['cote']) AND ($_GET['cote'] != 'populaire' AND $_GET['cote'] != 'trespopulaire') AND !isset($_POST['q'])){
            $getPopu = $bdd->query("SELECT * FROM livres WHERE cote_livres > (SELECT AVG(cote_livres) FROM livres) AND nbre_livres > 0 ORDER BY cote_livres DESC LIMIT 5");
            if($getPopu->rowCount() > 0){ ?>
                <h2 class="title">Meilleurs Livre(s)</h2>
                <div class="popuContainer">
                <?php
                    while($data = $getPopu->fetch()){ ?>
                    <a href="book.php?isbn=<?php echo $data["ISBN_livres"]; ?>" class="popubook">
                        <img src="admin/<?php echo $data["couverture_livres"]?>" alt="livre populaire">
                        <span class="poptitle"><?php echo $data['titre_livres']; ?></span>
                        <span class="popauteur"><?php echo $data['auteur_livres']; ?></span>
                            <button class="">
                                <i class="fa fa-eye" style="margin-right:5px"></i>
                                Voir
                            </button>
                            <?php 
                                // $getViews = $bdd->prepare("SELECT COUNT(*) FROM vue WHERE book_isbn = ?");
                                // $getViews->execute(array($data['ISBN_livres']));
                                // $get = $getViews->fetch();
                                // echo $get[0]; 
                             ?> 
                    </a>
                <?php
                    }
            }
        }else{
            if(!isset($_GET['cote'])){
                $getPopu = $bdd->query("SELECT * FROM livres WHERE cote_livres > (SELECT AVG(cote_livres) FROM livres) AND nbre_livres > 0 ORDER BY cote_livres DESC LIMIT 3");
                if($getPopu->rowCount() > 0){ ?>
                    <h2 class="title">Meilleurs Livre(s)</h2>
                    <div class="popuContainer">
                    <?php
                        while($data = $getPopu->fetch()){ ?>
                        <a href="book.php?isbn=<?php echo $data["ISBN_livres"]; ?>" class="popubook">
                            <img src="admin/<?php echo $data["couverture_livres"]?>" alt="livre populaire">
                            <span class="poptitle"><?php echo $data['titre_livres']; ?></span>
                            <span class="popauteur"><?php echo $data['auteur_livres']; ?></span>
                                <button class="">
                                    <i class="fa fa-eye" style="margin-right:5px"></i>
                                    Voir
                                </button>
                                <?php 
                                    // $getViews = $bdd->prepare("SELECT COUNT(*) FROM vue WHERE book_isbn = ?");
                                    // $getViews->execute(array($data['ISBN_livres']));
                                    // $get = $getViews->fetch();
                                    // echo $get[0]; 
                                ?> 
                        </a>
                    <?php
                        }
                }
            }
        }
            ?>
            </div>
        <h2 class="title">Tous les livres</h2>
        <div class="booksContainer">
            <?php
                if(isset($_GET['cote']) AND !empty($_GET['cote'])){
                $getBooks;
                switch($_GET['cote']){
                    case "all":
                        $getBooks = $bdd->query("SELECT * FROM livres WHERE nbre_livres > 0");
                    break;
                    case "populaire":
                        $getBooks = $bdd->query("SELECT * FROM livres WHERE cote_livres > 30 AND cote_livres < 100 AND nbre_livres > 0");
                    break; 
                    case "trespopulaire":
                        $getBooks = $bdd->query("SELECT * FROM livres WHERE cote_livres > 100 AND nbre_livres > 0");
                    break; 
                    default:
                        $getBooks = $bdd->query("SELECT * FROM livres WHERE nbre_livres > 0");
                    break;
                }
                if(isset($_POST['q']) AND !empty($_POST['q'])){
                    $getBooks = $bdd->query("SELECT * FROM livres WHERE titre_livres LIKE '%".htmlspecialchars($_POST['q'])."%' OR auteur_livres LIKE '%".htmlspecialchars($_POST['q'])."%' AND nbre_livres > 0 ");
                }
                if($getBooks->rowCount() > 0){ 
                    while($data = $getBooks->fetch()){ ?>
                    <div class="book">
                        <img src="admin/<?php echo $data["couverture_livres"]?>" class="couv">
                        <div class="book_body" id="<?php echo $data["auteur_livres"]; ?>">
                            <h3><?php echo $data['titre_livres']; ?></h4>
                            <h4><?php echo $data["auteur_livres"]; ?></h6>
                            <span class="explNum"><b style="background-color: #2d2d2d; padding: 2px 5px;  border-radius: 1px; color: white; margin-right: 5px"><?php echo $data["nbre_livres"]; ?></b> Exemplaire(s)</span>
                        </div>
                        <div class="buttonCommand" id="<?php echo $data['titre_livres']; ?>">
                            <a href="book.php?isbn=<?php echo $data["ISBN_livres"]; ?>" class="" style="text-decoration:none"><i class="fa fa-eye" style="margin-right: 5px"></i> 
                            <?php 
                                    $getViews = $bdd->prepare("SELECT COUNT(*) FROM vue WHERE book_isbn = ?");
                                    $getViews->execute(array($data['ISBN_livres']));
                                    $get = $getViews->fetch();
                                    echo $get[0]; 
                                ?>
                            </a>
                            <?php
                            $d = $bdd->prepare("SELECT COUNT(matricule_etudiant) FROM emprunt WHERE matricule_etudiant = ?");
                            $d->execute(array($_SESSION['stu_mat']));
                            $dfetch = $d->fetch();
                            if($dfetch[0] == 0){ ?>
                                <button class="reservbtnshow" id="<?php echo $data["ISBN_livres"]; ?>"><i class="fa fa-cart-plus" style="margin: 5px"></i> Emprunter</button>
                        <?php 
                            }else{ ?>
                                <button class="reservbtnshows" style="background-color: rgba(100,100,100)" id="<?php echo $data["ISBN_livres"]; ?>"><i class="fa fa-cancel" style="margin: 5px"></i> Faire une reservation</button>
                        <?php
                            }
                        ?>   
                        </div>
                    </div>
            <?php
                    }
                }else{ ?>
                <div class="nothing" style="background-color: transparent; margin: auto; display: flex; flex-direction: column; align-items:center">
                    <img src="assets/undraw_bibliophile_re_xarc.svg" alt="pas de livres">
                    <span>Pas de livres</span>
                    <a href="home.php?cote=all">Rafraichir la page</a>
                </div>   
            <?php
                }
            }else{
            $getBooks = $bdd->query("SELECT * FROM livres WHERE nbre_livres > 0");
            if($getBooks->rowCount() > 0){ 
                while($data = $getBooks->fetch()){ ?>
                <div class="book">
                    <img src="admin/<?php echo $data["couverture_livres"]?>" class="couv">
                    <div class="book_body" id="<?php echo $data["auteur_livres"]; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 740 320" class="wave1">
                            <path fill="#b80000" fill-opacity="1" d="M0,256L80,261.3C160,267,320,277,480,272C640,267,800,245,960,229.3C1120,213,1280,203,1360,197.3L1440,192L1440,0L1360,0C1280,0,1120,0,960,0C800,0,640,0,480,0C320,0,160,0,80,0L0,0Z"></path>
                        </svg>
                        <h3><?php echo $data['titre_livres']; ?></h4>
                        <h4><?php echo $data["auteur_livres"]; ?></h6>
                        <span class="explNum"><b style="background-color: #900000; padding: 2px 5px;  border-radius: 10px; color: white; margin-right: 5px"><?php echo $data["nbre_livres"]; ?></b> Exemplaire(s) disponible</span>
                    </div>
                    <div class="buttonCommand" id="<?php echo $data['titre_livres']; ?>">
                        <a href="book.php?isbn=<?php echo $data["ISBN_livres"]; ?>" class=""><i class="fa fa-eye"></i></a>
                        <?php
                            $d = $bdd->prepare("SELECT COUNT(matricule_etudiant) FROM emprunt WHERE matricule_etudiant = ?");
                            $d->execute(array($_SESSION['stu_mat']));
                            $dfetch = $d->fetch();
                            if($dfetch[0] == 0){ ?>
                                <button class="reservbtnshow" id="<?php echo $data["ISBN_livres"]; ?>"><i class="fa fa-cart-plus" style="margin: 5px"></i> Faire une reservation</button>
                        <?php 
                            }else{ ?>
                                <button class="reservbtnshows" style="background-color: rgba(100,100,100)" id="<?php echo $data["ISBN_livres"]; ?>"><i class="fa fa-cancel" style="margin: 5px"></i> Faire une reservation</button>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            <?php
                }
            }   
            }
            ?>
        </div>
        <div class="reservationFen">
            <span class="closeReservationPopup">x</span>
            <div class="modeemprunt">
               <input type="radio" name="mode" id="emporter" checked>
               <label for="emporter">A emporter</label>
               <input type="radio" name="mode" id="lire">
               <label for="lire">Lire sur place</label>
            </div>
            <p class="infop">
                <i class="fa fa-triangle-exclamation" style="margin: 5px"></i>Une fois sur place, vous allez fournir votre carte d'identite comme quaranti
            </p>
            <p class="infop">
                <i class="fa fa-triangle-exclamation" style="margin: 5px"></i>La duree maximal d'un emprunt est de 72H soit 3 jours
            </p>

            <div class="booksInfo">
                <img src="" alt="" id="book-img">
                <span class="bookTitle"></span>
                <span class="bookAuteur"></span>
            </div>
            <div class="userInfosContainer">
                <label for="u_name">Nom</label>
                <input type="text" value="<?php echo $_SESSION['stu_name']; ?>" id="student_name" class="<?php echo htmlspecialchars($_SESSION['stu_mat']); ?>" disabled>
                <label for="u_phone">Num&eacute;ro de t&eacute;l&eacute;phone</label>
                <input type="text" value="<?php echo $_SESSION['stu_numero']; ?>" id="student_phone" disabled>
                <label for="date_debut">Date de debut</label>
                <input type="date" id="date_debut">
                <label for="date_fin" class="emp">Date de retour</label>
                <input type="date" id="date_fin" class="emp">
            </div>
            <div class="buttonPanel">
                <button class="d_cancel" style="background-color: white; color: #b80000">Annuler</button>
                <?php
                            $dd = $bdd->prepare("SELECT COUNT(matricule_etudiant) FROM emprunt WHERE matricule_etudiant = ?");
                            $dd->execute(array($_SESSION['stu_mat']));
                            $ddfetch = $dd->fetch();
                            if($dfetch[0] == 0){ ?>
                                <button class="d_add">Valider l'emprunt</button>
                        <?php 
                            }else{ ?>
                                <button class="d_addd" style="background-color: rgba(100,100,100)">Valider l'emprunt</button>
                        <?php
                            }
                        ?> 
            </div>
        </div>
        <?php
            if(isset($_GET['disconnect'])){ ?>
            <form method="post" class="confirmBorrow">
                <div class="c1">
                    <p style="background-color: transparent; display: flex;align-items: center">Etes-vous sur de vouloir vous deconnecter ?</p>
                    <div class="sc1">
                        <input type="submit" value="Oui" name="confirmDisconnect">
                        <a href="home.php?cote=all">annuler</a>
                    </div>
                </div>               
            </form>
        <?php
            }
            if(isset($_GET['action']) AND $_GET['action'] == "showCautionInfo"){ ?>
            <form method="post" class="confirmBorrow">
                <div class="c1" style="height: auto">
                    <p style="background-color: transparent; display: flex;align-items: center; flex-direction: column">
                        <b style="background-color: #850000; color: white; display:inline-flex; width: 100%; text-indent: 10px; padding: 15px 0px; font-size: 1.3em; font-family: calibri light; margin-bottom: 15px">Dette</b>
                        Ceci est votre caisse de penalit&eacute;, plus vous dur&eacute;e avant de remettre un livre une fois la date limite de l'emprunt d&eacute;pass&eacute;, plus votre dette augmente de 500FCFA par jours.
                        En plus de remettre le livre, vous devrez donner la somme mentionne a la biblioth&eacute;caire comme p&eacute;nalit&eacute;.
                    </p>
                    <div class="sc1">
                        <!-- <input type="submit" value="Oui" name="confirmDisconnect"> -->
                        <a href="home.php?cote=all">Fermer</a>
                    </div>
                </div>               
            </form>
        <?php
            }
        ?>
    <script src="js/home.js"></script>
</body>
</html>