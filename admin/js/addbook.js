const titre = document.getElementById("titre");
const auteur = document.getElementById("auteur");
const isbn = document.getElementById("isbn");
const nbre = document.getElementById("expl");
const couverture = document.querySelector("#image");
const addbookbtn = document.querySelector(".addBookBtn");
const prev = document.querySelector(".imagereview");
const errmsg = document.querySelector(".updateErrorMsg");

const showErrmsg = (msg, color, time) =>{
    errmsg.innerHTML = msg;
    errmsg.style.backgroundColor = color;
    errmsg.classList.remove('hidden')
    setTimeout(()=>{
        hideErrmsg();
    }, time);
}
const hideErrmsg = () =>{
    errmsg.classList.add('hidden')
}


function createThumbnail(file) {
    var reader = new FileReader();
    reader.onload = function() {
        prev.style.backgroundImage = 'url('+this.result+')';
    };
    reader.readAsDataURL(file);
} 
let allowedTypes = ['png', 'jpg', 'jpeg', 'gif'];

couverture.onchange = function() {
    var files = this.files,
    imgType;
    imgType = files[0].name.split('.');
    imgType = imgType[imgType.length - 1];
        if(allowedTypes.indexOf(imgType.toLowerCase()) != -1) {
            if(files[0].name.split(' ').length == 1){
                createThumbnail(files[0]);
            }else{
                showErrmsg("<span><i class='fa fa-info-circle'></i></span> Le nom du fichier est incorrect .", "#5a0000", 7000);
            }
        }
};

addbookbtn.addEventListener("click", (e)=>{
    if(titre.value.trim() == ""){
        titre.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Le titre ne doit pas etre vide.", "#6d0000", 7000);        
    }else if(auteur.value.trim() == ""){
        auteur.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Vous devez renseigner le nom de l'auteur.", "#6d0000", 7000);
    }else if(isbn.value.trim() == "" || isbn.value.trim().split(" ").join("").length != 10 && isbn.value.trim().split(" ").join("").length != 13){
        isbn.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> ISBN Incorrect !", "#6d0000", 7000);
    }else if(nbre.value.trim() == "" || nbre.value.trim() < 0){
        nbre.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> La quantite renseigner n'est pas valide.", "#6d0000", 7000);
    }else if(couverture.value.trim() == ""){
        couverture.style.boxShadow = "0px 0px 16px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Vous devez selectionner une page de couverture", "#6d0000", 7000);
    }else{
        if(couverture.files[0].name.split(' ').length == 1){
            const form = new FormData();
            form.append("couverture", couverture.files[0]);
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "atraitement.php?titre="+titre.value.trim().split("&").join(" et ")+"&auteur="+auteur.value.trim().split("&").join(" ,")+"&isbn="+isbn.value.trim().split(" ").join("")+"&nbre="+nbre.value.trim());
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    if(xhr.responseText == "ok"){
                        isbn.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                        nbre.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                        couverture.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0)";
                        auteur.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                        titre.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";

                        showErrmsg("<i class='fa fa-check-circle' style='margin-right: 10px'></i> Ajout reussi <a href='dashboard.php' style='text-decoration: underline; color: white; font-weight: bold; margin-left: 3px'>voir les livres</a>", "rgba(0,120,0)", 60000);
                    }else if(xhr.responseText == "ex"){
                        isbn.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
                        showErrmsg("<i class='fa fa-exclamation-triangle' style='margin-right: 10px'></i> ISBN d&eacute;ja present", "#6d0000", 7000);
                    }else{
                        isbn.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                        showErrmsg("<i class='fa fa-exclamation-triangle' style='margin-right: 10px'></i> Une erreur est survenue !", "#6d0000", 7000);
                    }
                }
            }
            xhr.send(form);
        }else{
            showErrmsg("<span><i class='fa fa-info-circle'></i></span> Le nom du fichier est incorrect .", "#5a0000", "7000");
        }
    }
})