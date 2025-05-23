<?php 
    session_start();
    include("admin/config.php");
    $isbn;
    if(!isset($_GET['isbn']) || empty($_GET['isbn']) || !isset($_SESSION['stu_mat']) || empty($_SESSION['stu_mat'])){
        header("location:home.php");
    }
    $getUser  = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ?");
    $getUser->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
    $fetchUser = $getUser->fetch();
    
    if($getUser->rowCount() > 0){
    $dateToday = date("Y/m/d");
    $datetime = new DateTime($dateToday);
    $end = new DateTime($fetchUser["date_fin"]); 
    $lefts = $end->diff($datetime);

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
    if(isset($_GET['isbn']) AND !empty($_GET['isbn']) AND isset($_SESSION['stu_mat']) AND !empty($_SESSION['stu_mat'])){
        $getViewer = $bdd->prepare("SELECT COUNT(*) FROM vue WHERE book_isbn = ? AND user_matricule = ?");
        $getViewer->execute(array(htmlspecialchars($_GET['isbn']), htmlspecialchars($_SESSION['stu_mat'])));

        $vue = $getViewer->fetch();
        if($vue[0] == 0){
            $getViewer = $bdd->prepare("INSERT INTO vue(book_isbn, user_matricule) VALUES(?, ?)");
            $getViewer->execute(array(htmlspecialchars($_GET['isbn']), htmlspecialchars($_SESSION['stu_mat'])));            
        }
        $isbn = htmlspecialchars($_GET['isbn']);
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
    <link rel="stylesheet" href="css/book.css">
    <title>Document</title>
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
        <a href="" class="main-menu__item"><i class="fa fa-shopping-cart"></i></a>
        <div class="menu">
            <span class="smenu">
                <i class="fa fa-user-circle"></i>
                <span style="font-size: 0.8em"><i class="fa fa-angle-down"></i></span>
            </span>
            <div class="s-menu">
                <a href="book.php?isbn=<?php echo $isbn; ?>&disconnect" class="item"><i class="fa fa-sign-out-alt"></i> Deconnexion</a>
            </div>
        </div>
</div>
    <div class="container">
        <div class="container__body">
            <div class="redBall"></div>
            <div class="redBallp"></div>
            <?php 
                $getBook = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
                $getBook->execute(array(htmlspecialchars($_GET['isbn'])));

                if($getBook->rowCount() == 1){ 
                    while($book = $getBook->fetch()){ ?>
                        <div class="book">
                            <div class="book__couverture">
                                <img src="admin/<?php echo $book['couverture_livres']; ?>" alt="" >
                            </div>
                            <div class="rightSide">
                                <h1 class="book__title"><?php echo $book['titre_livres']; ?></h1>
                                <div class="auteurContainer item">
                                    <p id="auteur"><?php echo $book['auteur_livres']; ?></p>
                                </div>
                                <div class="isbnContainer item">
                                    <label for="isbn">ISBN</label>
                                    <p id="isbn"><?php echo $book['ISBN_livres']; ?></p>
                                </div>
                                <div class="explContainer item" style="display: flex">
                                    <label for="expl" style="margin-right: 5px"><i class="fa fa-book" style="margin: 0px 5px"></i></label>
                                    <p id="expl" style="font-size: 0.8em; color: #b80000; font-weight: bold"><?php echo $book['nbre_livres']; ?></p>
                                </div>
                                <div class="vueContainer item" style="display: flex">
                                    <label for="vue"><i class='fa fa-eye' style="margin: 0px 5px"></i></label>
                                    <p id="vue" style="font-size: 0.8em; color: #b80000; font-weight: bold">
                                    <?php 
                                        $getViews = $bdd->prepare("SELECT * FROM vue WHERE book_isbn = ?");
                                        $getViews->execute(array($book['ISBN_livres']));

                                        echo $getViews->rowCount();
                                    ?>
                                    </p>
                                </div>
                                <div class="buttonContainer">
                                    <a href="home.php?cote=all" class="retour">
                                        <i class="fa fa-arrow-left" style="font-size: 0.8em"></i>
                                        <span>retour</span>
                                    </a>
                                    <?php
                                        $d = $bdd->prepare("SELECT COUNT(matricule_etudiant) FROM emprunt WHERE matricule_etudiant = ?");
                                        $d->execute(array($_SESSION['stu_mat']));
                                        $dfetch = $d->fetch();
                                        if($dfetch[0] == 0){ ?>
                                        <button class="reserv" style="background-color: #b80000; border: none" id="<?php echo htmlspecialchars($book['ISBN_livres']); ?>"><i class="fa fa-cart-plus"></i> Faire une reservation</button>
                                    <?php 
                                        }else{ ?>
                                        <button class="reser" style="background-color: rgba(100,100,100); border: none"><i class="fa fa-cancel"></i> Faire une reservation</button>
                                    <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }else{
                    if($getBook->rowCount() == 0){ ?>
                    <div class="nothing" style="background-color: transparent; margin: auto; display: flex; flex-direction: column; align-items:center">
                        <img src="assets/undraw_bibliophile_re_xarc.svg" alt="pas de livres">
                        <span>Pas de livres</span>
                        <a href="home.php?cote=all">Rafraichir la page</a>
                    </div>
            <?php
                    }
                }
            ?>
        </div>
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
                    <a href="book.php?isbn=<?php echo $isbn; ?>">annuler</a>
                </div>
            </div>               
        </form>
    <?php
        }
    ?>
    <script src="js/book.js"></script>
</body>
</html>