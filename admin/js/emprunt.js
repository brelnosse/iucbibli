const lines = document.querySelectorAll(".save .book_title a");
// const linesAuth = document.querySelectorAll(".save .auth span");

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

for(let text of lines){
    sliceText(25, text, text.parentNode)
}