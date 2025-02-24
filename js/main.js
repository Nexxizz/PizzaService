function initialisieren(){
    "use strict"
    // addToCart();
    delFromCart();
    delSelFromCart();
};

function insert(element) {
    let id = element.getAttribute("id");
    let name = element.getAttribute("data-name");
    let select = document.getElementById("warenkorbSelection");
    let neuesElement = document.createElement("option");
    let price = parseFloat(element.getAttribute("data-price"));
    // priceAttribute.value = element.getAttribute("data-price");
    let optionText = document.createTextNode(id + " " + name);
    neuesElement.appendChild(optionText);
    neuesElement.setAttribute("data-price", price);
    neuesElement.selected = true;
    select.appendChild(neuesElement);
    let endPreis = document.getElementById("endPreis");
    let endPriceData = parseFloat(endPreis.getAttribute("data-endPrice"));
    endPreis.setAttribute("data-endPrice", (endPriceData + price).toFixed(2));
    endPriceData = parseFloat(endPreis.getAttribute("data-endPrice"));
    endPreis.innerText = "Ihr Preis: " + endPriceData + " €";
}

function delFromCart(){
    let delEverything = document.getElementById("delAll");
    delEverything.addEventListener("click", function (e){
        let select = document.getElementById("warenkorbSelection");
        while(select.firstChild){
            select.removeChild(select.lastChild);
        }
        let endPreis = document.getElementById("endPreis");
        endPreis.setAttribute("data-endPrice", "0");
        endPreis.innerText = "Ihr Preis: 0 €";
    }, false);
}

function delSelFromCart(){
    let delSelected = document.getElementById("delSel");
    delSelected.addEventListener("click", function (e){
        let select = document.getElementById("warenkorbSelection");
        let minusPreis = 0;
        let children = select.childNodes;
        for (let i = 0; i < children.length; i++){
                if(children[i].selected == true){
                minusPreis += parseFloat(children[i].getAttribute("data-Price"));
                select.removeChild(children[i]);
                i--;
            }
        }
        let endPreis = document.getElementById("endPreis");
        endPreis.setAttribute("data-endPrice", (parseFloat(endPreis.getAttribute("data-endPrice")) - minusPreis).toFixed(2));
        endPriceData = parseFloat(endPreis.getAttribute("data-endPrice"));
        endPreis.innerText = "Ihr Preis: " + endPriceData + " €";
    },false);

}

function selectAll()
{
    selectBox = document.getElementById("warenkorbSelection");

    for (var i = 0; i < selectBox.options.length; i++)
    {
        selectBox.options[i].selected = true;
    }
}


