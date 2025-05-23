const waves = document.querySelectorAll("svg.wave1");
if(innerWidth <= 632){
    for(let wave of waves){
        wave.setAttribute("viewBox","0 0 540 320");
    }   
}else if(innerWidth <= 483){
    for(let wave of waves){
        wave.setAttribute("viewBox", "0 0 500 320");
    }
}else if(innerWidth <= 460){
    for(let wave of waves){
        wave.setAttribute("viewBox", "0 0 480 320");
    }
}else if(innerWidth <= 422){
    for(let wave of waves){
        wave.setAttribute("viewBox", "0 0 450 320");
    }
}else{
    for(let wave of waves){
        wave.setAttribute("viewBox", "0 0 740 320");
    } 
}
  onresize = function(){
    if(this.innerWidth <= 632){
        for(let wave of waves){
            wave.setAttribute("viewBox", "0 0 540 320");
        }
    }
    if(this.innerWidth <= 483){
        for(let wave of waves){
            wave.setAttribute("viewBox", "0 0 500 320");
        }
    }
    if(this.innerWidth <= 460){
        for(let wave of waves){
            wave.setAttribute("viewBox", "0 0 480 320");
        }
    }
    if(this.innerWidth <= 422){
        for(let wave of waves){
            wave.setAttribute("viewBox", "0 0 450 320");
        }
    }
    if(this.innerWidth > 632){
        for(let wave of waves){
            wave.setAttribute("viewBox", "0 0 740 320");
        }       
    }
  }

  const closereservfen = document.querySelector(".closeReservationPopup"),
        reservationFen = document.querySelector(".reservationFen"),
        showreservfenbtns = document.querySelectorAll(".reservbtnshow"),
        bookImg = document.querySelector("#book-img"),
        bookTitle = document.querySelector(".bookTitle"),
        bookAuteur = document.querySelector(".bookAuteur"),
        dadd = document.querySelector(".d_add"),
        dcancel = document.querySelector(".d_cancel"),
        aemporterbtn = document.getElementById("emporter"),
        alirebtn = document.getElementById("lire"),
        tohideifalire = document.querySelectorAll(".emp"),
        stuName = document.querySelector("#student_name"),
        studNumero = document.querySelector("#student_phone"),
        dateDebut = document.querySelector("#date_debut"),
        date_fin = document.querySelector("#date_fin"),
        booksTitle = document.querySelectorAll(".book h3"),
        booksAuth = document.querySelectorAll(".book h4");

function sliceText(maxLength, elemtoResize, elemParent){
    let finalLength = maxLength - 5;
    let emplText = elemtoResize.textContent.trim();
    let tabempltext = [];
        if(emplText.length >= maxLength){
            tabempltext = emplText.slice(0, finalLength);
            elemParent.title = emplText;
            elemtoResize.textContent =  tabempltext+'...';
        }
}
for(let bTitle of booksTitle){
    sliceText(20, bTitle, bookTitle.parentNode);
}
for(let bAuth of booksAuth){
    sliceText(33, bAuth, bAuth.parentNode);
}
closereservfen.addEventListener("click", ()=>{
    reservationFen.style.display = "none";
})
dcancel.addEventListener("click", ()=>{
    reservationFen.style.display = "none";
})
for(let showreservfenbtn of showreservfenbtns){
    showreservfenbtn.addEventListener("click", (e)=>{
        reservationFen.style.display = "flex";
        bookImg.setAttribute("src", e.target.parentNode.parentNode.firstElementChild.src);
        bookTitle.textContent = e.target.parentNode.id;
        bookAuteur.textContent = e.target.parentNode.previousElementSibling.id;
        dadd.id = e.target.id;
    })
}
alirebtn.addEventListener("click", function(e){
    if(e.target.checked){
        for(let bf of tohideifalire){
            bf.style.visibilty = "hidden";
            bf.style.opacity = "0";
        }
    }
})
aemporterbtn.addEventListener("click", function(e){
    if(e.target.checked){
        for(let bf of tohideifalire){
            bf.style.visibilty = "visible";
            bf.style.opacity = "1";
        }
    }
})
dateDebut.value = new Date().toLocaleDateString("en-CA");

dateDebut.addEventListener("change", function(e){
    let today = new Date();
    today.setHours(0,0,0,0);
    let startDate = new Date(dateDebut.value.trim());
    startDate.setHours(0,0,0,0);
    if((startDate - today) < 0){
        e.target.value = new Date().toLocaleDateString("en-CA");
    }
});
date_fin.addEventListener("change", function(e){
    let today = new Date();
    let startDate = new Date(dateDebut.value.trim()),
    endDate = new Date(e.target.value.trim()); 
    startDate.setHours(0,0,0,0); 
    endDate.setHours(0,0,0,0);
    today.setHours(0,0,0,0);
    let diffMillis = endDate - startDate;
    let diffDays = diffMillis/(1000*60*60*24);

    if(diffMillis < 0 || diffDays > 3){
        e.target.value = today.toLocaleDateString("en-CA");
        e.target.style.boxShadow = "0px 0px 8px 3px rgba(150,0,0)";
    }else{
        e.target.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
    }
})
if(dadd != null){
dadd.addEventListener("click", function(e){
    let today = new Date();
    today.setHours(0,0,0,0);
    let startDate = new Date(dateDebut.value.trim()),
        endDate = new Date(date_fin.value.trim());
        startDate.setHours(0,0,0,0);
        endDate.setHours(0,0,0,0);
    let diffMillis = endDate - startDate;
    let diffDays = diffMillis/(1000*60*60*24);
    let todayAndDebut = startDate - today;
    let todayAndFin = endDate - today;

    if(aemporterbtn.checked){
        if(stuName.value.trim() == ""){
            stuName.style.boxShadow = "0px 0px 8px 3px rgba(150,0,0)";
            studNumero.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            dateDebut.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            date_fin.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";            
        }else if(studNumero.value.trim() == ""){
            studNumero.style.boxShadow = "0px 0px 8px 3px rgba(150,0,0)";
            stuName.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            dateDebut.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            date_fin.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";            
        }else if(dateDebut.value.trim() == "" || todayAndDebut < 0){
            dateDebut.style.boxShadow = "0px 0px 8px 3px rgba(150,0,0)";
            stuName.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            studNumero.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            date_fin.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";            
        }else if(date_fin.value.trim() == "" || diffDays > 3 || todayAndFin < 0){
            date_fin.style.boxShadow = "0px 0px 8px 3px rgba(150,0,0)";
            stuName.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            studNumero.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            dateDebut.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";   
        }else{
            stuName.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            studNumero.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            dateDebut.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            date_fin.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "htraitement.php?nom="+stuName.value.trim()+"&numero="+studNumero.value.trim()+"&date_debut="+dateDebut.value.trim()+"&date_fin="+date_fin.value+"&mode=emporter&matricule="+stuName.className+"&isbn="+e.target.id);
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    if(xhr.responseText == "ok"){
                            window.location = "panier.php";
                    }else{
                        if(xhr.responseText == "already"){
                            alert("Vous avez une demande d'emprunt en cours, veuillez patienter jusqu'a la fin de votre emprunt ou du refus eventuel de votre emprunt. ou vous pouvez annuler votre reservation dans le panier et effectuer une nouvelle")
                        }
                    }
                }
            }
            xhr.send(null);
        }
    }
    if(alirebtn.checked){
        if(stuName.value.trim() == ""){
            stuName.style.boxShadow = "0px 0px 8px 3px rgba(150,0,0)";
            studNumero.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            dateDebut.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
        }else if(studNumero.value.trim() == ""){
            studNumero.style.boxShadow = "0px 0px 8px 3px rgba(150,0,0)";
            stuName.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            dateDebut.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
        }else if(dateDebut.value.trim() == "" || todayAndDebut < 0){
            dateDebut.style.boxShadow = "0px 0px 8px 3px rgba(150,0,0)";
            stuName.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            studNumero.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
        }else{
            stuName.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            studNumero.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            dateDebut.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "htraitement.php?nom="+stuName.value.trim()+"&numero="+studNumero.value.trim()+"&date_debut="+dateDebut.value.trim()+"&date_fin="+dateDebut.value.trim()+"&mode=emporter&matricule="+stuName.className+"&isbn="+e.target.id);
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    if(xhr.responseText == "ok"){
                            window.location = "panier.php";
                    }else{
                        if(xhr.responseText == "already"){
                            alert("Ce livre est deja dans le panier .")
                        }
                    }
                }
            }
            xhr.send(null);
        }
    }
})
}
