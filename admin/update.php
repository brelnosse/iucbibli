<?php 
    session_start();
    include("config.php");

    if(!isset($_SESSION['email'])){
        header("location:index.php");
    }
    if(!isset($_GET['isbn']) || empty($_GET['isbn'])){
        header("location:dashboard.php");
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
    <link rel="stylesheet" href="css/update.css">
    <title>Document</title>
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
            <a href="gereremprunt.php">
                <i class="fa fa-cloud-download"></i> <span class="label">Gerer les emprunts</span>
                <?php
                    $getunreadEmprunt = $bdd->query("SELECT * FROM emprunt WHERE viewedbyhost = false");
                    $getunread = $getunreadEmprunt->fetch();

                if($getunreadEmprunt->rowCount() > 0){ ?>
                 <b style="display:inline-flex; height:10px; width: 10px; border-radius: 50px; background-color: #b80000; margin-left: 8px"></b>
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
            <a href="dashboard.php" style="position: absolute; left: 10px">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div>
                <span>Tableau de bord / </span>
                <a href="update.php?isbn=<?php echo htmlspecialchars($_GET['isbn']);?>" style="margin-left: 2px"> Modifier un livre</a>
            </div>
        </div>
        <div class="container__body">
            <div class="redBall"></div>
            <div class="redBallp"></div>
            <?php 
                $getBook = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
                $getBook->execute(array(htmlspecialchars($_GET['isbn'])));

                if($getBook->rowCount() == 1){ 
                    while($book = $getBook->fetch()){ ?>
                        <div class="book__form">
                            <div class="book__form--header">
                                <label for="book_title" class="titre_btn"><i class="fa fa-pen-to-square"></i></label>
                                <input type="text" name="book_title" id="book_title" disabled="disabled" placeholder="Titre" value="<?php echo $book["titre_livres"]; ?>">
                            </div>
                            <div class="book__form--body">
                                <div class="book__form--body-leftSide">
                                    <label for="auteur">Auteur <span class="nomauteur" id="<?php echo $book["ISBN_livres"]; ?>"><i class="fa fa-pen-to-square"></i></span></label>
                                    <input type="text" id="auteur" placeholder="Auteur" value="<?php echo $book["auteur_livres"]; ?>" disabled="disabled">
                                    <label for="isbn">ISBN</label>
                                    <input type="text" id="isbn" placeholder="ISBN" value="<?php echo $book["ISBN_livres"]; ?>" disabled="disabled">
                                    <label for="exemplaire">Exemplaire <span class="nbrelivre" id="<?php echo $book["ISBN_livres"]; ?>"><i class="fa fa-pen-to-square"></i></span></label>
                                    <input type="number" id="exemplaire" placeholder="Nombre d'exemplaire" value="<?php echo $book["nbre_livres"]; ?>" disabled="disabled">
                                    <div class="updateErrorMsg">
                                        une erreur est survenue
                                    </div>
                                </div>
                                <div class="book__form--body-rightSide" style="background-image: url(<?php echo $book["couverture_livres"];?>)">
                                    <label for="couverture">
                                        <i class="fa fa-pen-to-square"></i>
                                    </label>
                                    <input type="file" id="couverture" style="display: none">
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }
            ?>
        </div>
    </div>  
    <script src="js/update.js"></script>
</body>
</html>