<?php
    session_start();
    include("config.php");
    if(!isset($_SESSION['email'])){
        header("location:index.php");
    }
    // on modifie le statut des emprunts non lu
    $getunread = $bdd->query("UPDATE emprunt SET viewedbyhost = true");

    if(isset($_POST['confirmEmprunt'])){
        if(isset($_GET['msg_type'], $_GET['stu_mat']) AND !empty($_GET['msg_type']) AND !empty($_GET['stu_mat'])){
            $getEmpruntsIDs = $bdd->query("SELECT id FROM emprunt");

            $arr;
            $id = null;
            while($getEmpruntsIDSFetch = $getEmpruntsIDs->fetch()){
                $arr[] = $getEmpruntsIDSFetch['id'];
            }
            for($i = 0; $i < count($arr); $i++){
                if($_GET['msg_type'] == sha1($arr[$i])){
                    $id = $arr[$i];
                    break;
                }
            }
            if($id != null){
                $getBorrow = $bdd->prepare("SELECT * FROM emprunt WHERE isok='false' AND date_fin < CURDATE() AND matricule_etudiant = ?");
                $getBorrow->execute(array(htmlspecialchars($_GET['stu_mat'])));

                if($getBorrow->rowCount() > 0){
                    $deleteRequestedBorrow = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
                    $deleteRequestedBorrow->execute(array($_GET['stu_mat']));

                    echo "<script>alert('une erreur est survenue')</script>";
                    header("location: gereremprunt.php");
                }else{
                    $validate = $bdd->prepare("UPDATE emprunt SET isok = 'true', repliedDate = CURDATE() WHERE id = ?");
                    $validate->execute(array($id));
                    $updatenbre = $bdd->query("UPDATE etudiant SET nbre_emprunt_etudiant = nbre_emprunt_etudiant + 1 WHERE matricule_etudiant = '".htmlspecialchars($_GET['stu_mat'])."'");
                    header("location: gereremprunt.php");

                }                
            }
        }
    }
    if(isset($_POST['confirmReturn'])){
        if(isset($_GET['id'], $_GET['isbn']) AND !empty($_GET['id']) AND !empty($_GET['isbn'])){
            $getReturnsIDs = $bdd->query("SELECT id FROM emprunt");

            $arr;
            $id = null;
            while($getReturnsIDSFetch = $getReturnsIDs->fetch()){
                $arr[] = $getReturnsIDSFetch['id'];
            }
            for($i = 0; $i < count($arr); $i++){
                if($_GET['id'] == sha1($arr[$i])){
                    $id = $arr[$i];
                    break;
                }
            }
            if($id != null){
                $validate = $bdd->prepare("UPDATE livres SET nbre_livres = nbre_livres + 1 WHERE ISBN_livres = ?");
                $validate->execute(array(htmlspecialchars($_GET['isbn_livres'])));

                $updateValidate = $bdd->prepare("DELETE FROM emprunt WHERE id = ?");
                $updateValidate->execute(array($id));

                header("location: gereremprunt.php");
            }else{
                echo "<script>alert('bonjour bull')</script>";
            }
        }
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
    <link rel="stylesheet" href="css/allemprunts.css"/>
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
            <a href="dashboard.php"><i class="fa fa-eye"></i> <span class="label">Afficher les livres</span></a>
            <a href="gereremprunt.php"><i class="fa fa-cloud-download"></i> <span class="label">Gerer les emprunts</span></a>
            <a href="borrowedBooks.php" class="active"><i class="fa fa-bullseye"></i> <span class="label">Afficher les livres emprunter</span></a>
            <a href="history.php"><i class="fa fa-history"></i> <span class="label">Historique des emprunts</span></a>
        </div>
        <div class="toolsmenu__footer">
            <a href="index.php?log"><i class="fa fa-sign-out-alt"></i></a>
        </div>
    </div>
    <div class="container">
        <div class="container__title">
            <span>Tableau de bord / </span>
            <a href="borrowedBooks.php" style="margin-left: 2px"> Tous les emprunts</a>
        </div>
        <div class="container__option"></div>
        <div class="container__body">
            <?php
            $getPopu = $bdd->query("SELECT * FROM livres WHERE ISBN_livres IN (SELECT isbn_livres FROM emprunt WHERE isok='true')");
            if($getPopu->rowCount() > 0){
                while($data = $getPopu->fetch()){ ?>
                    <a href="book.php?isbn=<?php echo $data["ISBN_livres"]; ?>" class="popubook">
                        <img src="<?php echo $data["couverture_livres"]?>" alt="livre populaire">
                        <span class="poptitle"><?php echo $data['titre_livres']; ?></span>
                        <span class="popauteur"><?php echo $data['auteur_livres']; ?></span>
                            <button class="">
                                <i class="fa fa-eye" style="margin-right:5px"></i>
                                Voir
                            </button>
                    </a>
                <?php
            }                   
            }else{ ?>
                <div class="nothing" style="background-color: transparent; margin: auto; display: flex; flex-direction: column; align-items:center">
                <img src="assets/img/undraw_No_data_re_kwbl.png" alt="pas de livres">
                <span>Pas de livres</span>
                <a href="borrowedBooks.php">Rafraichir la page</a>
            </div>
            <?php
            }
            ?>
        </div>
    </div>
    <script src="js/emprunt.js"></script>
</body>
</html>