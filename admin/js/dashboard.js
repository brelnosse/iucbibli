const deleteButtons = document.querySelectorAll(".container__table--button-container a:last-of-type");
const msgbox = document.querySelector(".msgbox");
const cancelBtn = document.querySelector(".cancelBtn");
const countDown = document.querySelector("#counter");
const lines = document.querySelectorAll(".save .book_title span");
const linesAuth = document.querySelectorAll(".save .auth span");
let bookISBN = null;
let countdown = 5;
let countdownId;

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
function simpleSlice(text){
    let tabempltext = []; 
    if(text.length > 15){
        tabempltext = text.slice(0, 15);
        return tabempltext+'...';
    }else{
        return text;
    }
}

for(let text of lines){
    sliceText(25, text, text.parentNode)
}
for(let auth of linesAuth){
    sliceText(40, auth, auth.parentNode)
}
const showmsgbox = () =>{
    msgbox.style.top = "35px";
    countdownId = setInterval(function(){
        countdown--;
        if(countdown >= 0){
            countDown.innerHTML = countdown + "s";
            if(countdown == 0){
                const xhr = new XMLHttpRequest();
                xhr.open("GET", "removeBook.php?isbn="+bookISBN);
                xhr.onreadystatechange = function(){
                    if(xhr.readyState == 4 && xhr.status == 200){
                        if(xhr.responseText == 'ok'){
                            window.location = "dashboard.php";
                        }else{
                            alert("Une erreur est survenue, veuillez re-actualiser la page.")
                        }
                    }
                }
                xhr.send(null);
            }
        }
    }, 1000);
    setTimeout(()=>{
        hideErrmsg();
    }, 6000);
}
const hideErrmsg = () =>{
    msgbox.style.top = "-410px";
    countdown = 5;
    bookISBN = null;
    countDown.innerHTML = countdown;
    clearInterval(countdownId);
}

cancelBtn.addEventListener("click", (e)=>{
    hideErrmsg();
})
for(let deleteButton of deleteButtons){
    deleteButton.addEventListener("click", (e)=>{
        e.preventDefault();
        bookISBN = e.target.id;
        showmsgbox()
    })
}
let valuesContainer = [];
let labelsContainer = [];

const xhr = new XMLHttpRequest();
xhr.open("GET", "getChartinfo.php");
xhr.onreadystatechange = function(){
    if(xhr.readyState == 4 && xhr.status == 200){
        let arr = xhr.responseText.split("(*.)");
        arr = arr.map(function(elem){
            return elem.split("(.*)");
        })
        for(let i = 0; i < arr.length; i++){
            labelsContainer.push(simpleSlice(arr[i][0]));
        }
        for(let i = 0; i < arr.length; i++){
            valuesContainer.push(arr[i][1]);
        }
        const barContainer = document.querySelector("#barContainer");
        const barChart = new Chart(barContainer, {
            type: "bar",
            data: {
                labels: labelsContainer,
                datasets:[{
                    label: "Popularite",
                    data: valuesContainer,
                    backgroundColor: [
                        "#850000",
                        "#C70039",
                        "#F1C40F", 
                        "yellow",
                        "#2ECC71"
                    ]
                }]
            },
            options:{
                scales:{
                    y:{
                        suggestedMax: Math.max(valuesContainer)+100
                    }
                }
            }
        });
    }
}
xhr.send(null);