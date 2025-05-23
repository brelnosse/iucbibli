const nomauteurbtn = document.querySelector(".nomauteur");
const nomauteur = document.querySelector("#auteur");
const nbrelivrebtn = document.querySelector(".nbrelivre");
const nbrelivre = document.querySelector("#exemplaire");
const errmsg = document.querySelector(".updateErrorMsg");
const couverture = document.querySelector("#couverture");
const titrebtn = document.querySelector(".titre_btn");
const titre = document.querySelector("#book_title");
const prev = document.querySelector(".book__form--body-rightSide");

titre.disabled = false;
titre.focus();

const showErrmsg = (msg, color) =>{
    errmsg.innerHTML = msg;
    errmsg.style.bottom = "5%";
    errmsg.style.backgroundColor = color;
    setTimeout(()=>{
        hideErrmsg();
    }, 4000);
}
const hideErrmsg = () =>{
    errmsg.style.bottom = "-120%";
}

titrebtn.addEventListener("click", (e)=>{
    titre.disabled = false;
    titre.focus();
})
titre.onblur = function(){
    titre.disabled = true;
    if(titre.value.trim() != ""){
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "utraitement.php?isbn="+nomauteurbtn.id+"&titre="+titre.value.trim().split("&").join(" et "));
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                if(xhr.responseText == 'ok'){
                    showErrmsg("<span><i class='fa fa-check-circle'></i></span> Modification du titre reussi !", "rgba(0,180,0)");
                }else{
                    if(xhr.responseText == "no")
                        showErrmsg("<span><i class='fa fa-times-circle'></i></span> Une erreur inconnu est survenu !", "#5a0000");
                }
            }
        }
        xhr.send(null);
    }else{
        showErrmsg("<span><i class='fa fa-times-circle'></i></span> Ce champ ne peux etre vide !", "#5a0000");
        titre.disabled = false;
        titre.focus();
    }
}

nomauteurbtn.addEventListener("click", (e)=>{
    nomauteur.disabled = false;
    nomauteur.focus();
})
nomauteur.onblur = function(){
    nomauteur.disabled = true;
    if(nomauteur.value.trim() != ""){
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "utraitement.php?isbn="+nomauteurbtn.id+"&auteur="+nomauteur.value.trim().split("&").join(" ,"));
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                if(xhr.responseText == 'ok'){
                    showErrmsg("<span><i class='fa fa-check-circle'></i></span> Modification du nom de l'auteur reussi !", "rgba(0,180,0)");
                }else{
                    if(xhr.responseText == "no")
                        showErrmsg("<span><i class='fa fa-times-circle'></i></span> Une erreur inconnu est survenu !", "#5a0000");
                }
            }
        }
        xhr.send(null);
    }else{
        showErrmsg("<span><i class='fa fa-times-circle'></i></span> Ce champ ne peux etre vide !", "#5a0000");
        nomauteur.disabled = false;
        nomauteur.focus();
    }
}

nbrelivrebtn.addEventListener("click", (e)=>{
    nbrelivre.disabled = false;
    nbrelivre.focus();
})
nbrelivre.onblur = function(){
    nbrelivre.disabled = true;
    if(nbrelivre.value.trim() != "" && nbrelivre.value.trim() >= 0){
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "utraitement.php?isbn="+nbrelivrebtn.id+"&exemplaire="+nbrelivre.value.trim());
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                if(xhr.responseText == 'ok'){
                    showErrmsg("<span><i class='fa fa-check-circle'></i></span> Modification du nombre d'exemplaire reussi !", "rgba(0,180,0)");
                }else{
                    if(xhr.responseText == "no")
                        showErrmsg("<span><i class='fa fa-times-circle'></i></span> Une erreur inconnu est survenu.", "#5a0000");
                }
            }
        }
        xhr.send(null);
    }else{
        showErrmsg("<span><i class='fa fa-times-circle'></i></span> Ce champ ne peux etre vide et ne doit contenir que des entier positif !", "#5a0000");
        nbrelivre.disabled = false;
        nbrelivre.focus();
    }
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
            
                const img = new FormData();
                img.append("couverture", this.files[0]);
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "utraitement.php?isbn="+nbrelivrebtn.id);
                xhr.onreadystatechange = function(){
                    if(xhr.readyState == 4 && xhr.status == 200){
                        if(xhr.responseText == 'ok'){
                            showErrmsg("<span><i class='fa fa-check-circle'></i></span> Modification de la couverture reussi !", "rgba(0,180,0)");
                        }else{
                            if(xhr.responseText == "no")
                                showErrmsg("<span><i class='fa fa-times-circle'></i></span> Une erreur inconnu est survenu.", "#5a0000");
                        }
                    }
                }
                xhr.send(img);
            }else{
                showErrmsg("<span><i class='fa fa-info-circle'></i></span> Le nom du fichier est incorrect .", "#5a0000");
            }
        }else{
            showErrmsg("<span><i class='fa fa-info-circle'></i></span> Votre fichier n'est pas pris en charge.", "#5a0000");
        }
};
