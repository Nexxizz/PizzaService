// request als globale Variable anlegen (haesslich, aber bequem)
let request = new XMLHttpRequest();

function requestData() { // fordert die Daten asynchron an
    "use strict";
    request.open("GET", "KundenStatus.php"); // URL für HTTP-GET
    request.onreadystatechange = processData; //Callback-Handler zuordnen
    request.send(null); // Request abschicken
}

function processData() {
    if(request.readyState == 4) { // Uebertragung = DONE
        if (request.status == 200) {   // HTTP-Status = OK
            if(request.responseText != null)
                process(request.responseText);// Daten verarbeiten
            else console.error ("Dokument ist leer");
        }
        else console.error ("Uebertragung fehlgeschlagen");
    } else ;          // Uebertragung laeuft noch
}

function process($data) {

    let obj = JSON.parse($data);

    let sectionOutput = document.getElementById("output");

    while (sectionOutput.firstChild) {
        sectionOutput.removeChild(sectionOutput.lastChild);
    }

    // let Ueberschrift = document.createElement("h2");
    // Ueberschrift.innerText = "Der Status ihrer Bestellungen:";
    // sectionOutput.appendChild(Ueberschrift);

        for (const item of obj) {
            if(item.status != 4) {
                let pizzaname = document.createElement("h3");
                pizzaname.innerText = "Pizza Name " + item.name;
                sectionOutput.appendChild(pizzaname);
            }

            if(item.status == 0) {
                let status = document.createElement("p");
                status.innerText = "Bestellt";
                sectionOutput.appendChild(status);
            }

            if(item.status == 1) {
                let status = document.createElement("p");
                status.innerText = "Im Ofen";
                sectionOutput.appendChild(status);
            }

            if(item.status == 2) {
                let status = document.createElement("p");
                status.innerText = "Fertig gebacken";
                sectionOutput.appendChild(status);
            }

            if(item.status == 3) {
                let status = document.createElement("p");
                status.innerText = "Unterwegs";
                sectionOutput.appendChild(status);
            }

        }
}

window.setInterval(requestData, 2000);