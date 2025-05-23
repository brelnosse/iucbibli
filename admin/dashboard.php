<?php
    session_start();
    include("config.php");
    if(!isset($_SESSION['email'])){
        header("location:index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/fontawesome.css">
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/brands.css"/>
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/solid.css"/>
    <link rel="stylesheet" href="assets/aos-2/dist/aos.css">
    <link rel="stylesheet" href="css/dashboard.css"/>
    <title><?php 
        if(isset($_SESSION['email'])){
            echo htmlspecialchars($_SESSION['email']);
        }
    ?></title>
</head>
<body>
    <div class="toolsmenu">
        <div class="toolsmenu__header">
            <span class="toolsmenu__header--userpp">
                <i class="fa-solid fa-user"></i>
            </span>
            <span class="toolsmenu__header--useremail">
                <?php
                    echo htmlspecialchars($_SESSION['email']);
                ?>
            </span>
        </div>
        <div class="toolsmenu__body">
            <a href="addbook.php"><i class="fa fa-plus"></i> <span class="label">Ajouter un livre</span></a>
            <a href="dashboard.php" class="active"><i class="fa fa-eye"></i> <span class="label">Afficher les livres</span></a>
            <a href="gereremprunt.php"><i class="fa fa-cloud-download"></i> <span class="label">Gerer les emprunts</span> 
            <?php
                $getunreadEmprunt = $bdd->query("SELECT * FROM emprunt WHERE viewedbyhost = false");
                $getunread = $getunreadEmprunt->fetch();

                if($getunreadEmprunt->rowCount() > 0){ ?>
                 <b id="<?php echo $getunreadEmprunt->rowCount(); ?>" class="bbull" style="display:inline-flex; height:10px; width: 10px; border-radius: 50px; background-color: #b80000; margin-left: 8px"></b>
                <script>
                    const bbull = document.querySelector(".bbull");

                    if(Notification.permission === "granted"){
                        new Notification("Emprunt de livres ", {
                            body: "Vous avez " + bbull.id + (parseInt(bbull.id) > 1 ? " nouveaux emprunts de livres" : " nouvel emprunt de livre")
                        });          
                    }else{
                        Notification.requestPermission()
                            .then(permission =>{
                                if(permission === "granted"){
                                    new Notification("Emprunt de livres ", {
                                        body: "Vous avez " + bbull.id + (parseInt(bbull.id) > 1 ? " nouveaux emprunts de livres" : " nouvel emprunt de livre")
                                    });
                                }
                            })
                    }
                </script>
            <?php
                }
            ?>
             </a>
            <a href="borrowedBooks.php"><i class="fa fa-bullseye"></i> <span class="label">Afficher les livres emprunter</span></a>
            <a href="history.php"><i class="fa fa-history"></i> <span class="label">Historique des emprunts</span></a>
        </div>
        <div class="toolsmenu__footer">
            <a href="index.php?log"><i class="fa fa-sign-out-alt"></i></a>
        </div>
    </div>
    <div class="container">
        <div class="container__title">
            <span>Tableau de bord / </span>
            <a href="dashboard.php" style="margin-left: 2px"> Tous les lives</a>
        </div>
        <form method="post" class="container__option">
            <p>Rechercher un livre</p>
            <div class="searchContainer">
                <input type="text" name="search" placeholder="Entrez un titre ou un auteur" value="<?php 
                    if(isset($_POST['search'])){
                        echo htmlspecialchars($_POST['search']);
                    }
                ?>">
                <input type="submit" name="research" value="Rechercher">
            </div>
        </form>
        <div class="container__body">
            <!-- <h1 style="background-color: white; color: #850000; width: 98%; padding: 20px 20px;margin: 10px auto;border-radius: 15px; font-family: calibri">Tout les livres</h1> -->

            <table class="container__table">
                <tr class="container__body--titles-container">
                    <th class="container__body--title">Titre</th>
                    <th class="container__body--title">Auteur</th>
                    <th class="container__body--title">popularit&eacute;s</th>
                    <th class="container__body--title">Date d'ajout</th>
                    <th class="container__body--title">Exemplaires</th>
                    <th class="container__body--title">Action</th>
                </tr>
                <?php 
                    $count = 0;
                    $getBooks;
                    if(isset($_POST['research'])){
                        if(isset($_POST['search']) AND !empty($_POST['search'])){
                            $getBooks = $bdd->query("SELECT * FROM livres WHERE titre_livres LIKE '%".htmlspecialchars($_POST['search'])."%' OR auteur_livres LIKE '%".htmlspecialchars($_POST['search'])."%'");
                        }else{
                            $getBooks = $bdd->query("SELECT * FROM livres");
                        }
                    }else{
                        $getBooks = $bdd->query("SELECT * FROM livres");
                    }

                    if($getBooks->rowCount() > 0){
                        while($book = $getBooks->fetch()){ ?> 
                    <tr class="save">
                        <td class="book_title">
                            <span><?php echo $book["titre_livres"]; ?></span>
                        </td>
                        <td class="auth">
                           <span><?php echo $book["auteur_livres"]; ?></span>
                        </td>
                        <td>
                            <?php 
                                if($book["cote_livres"] >= 100){ ?>
                                <span class="badge tpopulaire"><i class="fa fa-face-grin-stars" style="margin-right: 3px"></i> Tr&egrave;s Populaire</span>
                            <?php
                                }elseif($book["cote_livres"] > 30 AND $book["cote_livres"] < 100){ ?>
                                <span class="badge populaire"><i class="fa fa-smile-beam" style="margin-right: 3px"></i> populaire</span>
                            <?php
                                }else{ ?>
                                <span class="badge npopulaire"><i class="fa fa-meh-rolling-eyes" style="margin-right: 3px"></i> pas populaire</span>
                            <?php
                                }
                            ?>
                        </td>
                        <td>
                            <?php echo $book["date_ajout_livres"]; ?>
                        </td>
                        <td class="num">
                            <?php echo $book["nbre_livres"]; ?>
                        </td>
                        <td class="container__table--button-container">
                            <a href="book.php?isbn=<?php echo $book['ISBN_livres']; ?>" class="see"><i class="fa fa-eye"></i></a>
                            <a href="update.php?isbn=<?php echo $book['ISBN_livres']; ?>"  class="set"><i class="fa fa-pen-to-square"></i></a>
                            <a href=""  class="delete" id="<?php echo $book["ISBN_livres"]; ?>"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php
                        $count++;

                }
                    }else{ ?>
                <tr>
                    <td colspan="6" class="nothing">
                        <img src="assets/img/undraw_No_data_re_kwbl.png" width="250" alt="">
                        <span style="color: #b60000; margin-top: 15px">aucun livre...</span>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </table>
            <div class="statitics">
                <h1 style="background-color: white; color: #850000;">Les plus emprunt&eacute;s</h1>
                <div class="chartContainer">
                    <canvas id="barContainer" aria-label="chart" role="img"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="msgbox">
        <p>Cette action est irr&eacute;versible</p>
        <span class="cancelBtn">
            Annuler 
            <span id="counter">5s</span>
        </span>
    </div>
    <script src="assets/aos-2/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="js/chart.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>