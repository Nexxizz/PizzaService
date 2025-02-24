<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€


// to do: change name 'Bestellung' throughout this file
require_once './Page.php';

class Bestellung extends Page
{
    // to do: declare reference variables for members
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }


    function printPizzas(string $id, string $name, string $picture, string $price):void {
        echo <<< ARTICLE
<article onclick="insert(this)" class="card" id=$id data-name=$name data-price="$price">
        <a href="#" class="mainA">
        <p>Hinzufügen<br>Pizza $name <strong>$price €</strong></p>
        <img src=$picture alt="Pizza $name">
        </a>
    </article>
ARTICLE;
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data.
     * This may be a normal array, an empty array or an associative array.
     */



    protected function getViewData():array
    {
        // to do: fetch data for this view from the database
        // to do: return array containing data
        $sql = "SELECT * FROM article";

        $recordset = $this->_database->query($sql);
        if (!$recordset) {
            throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
        }

        $result = array();
        $record = $recordset->fetch_assoc();
        while ($record) {
            $result[] = $record;
            $record = $recordset->fetch_assoc();
        }

        $recordset->free();
        return $result;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    protected function generateView():void
    {
        $data = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('Bestellung', 'js/main.js'); //to do: set optional parameters
        echo "<body onload='initialisieren()'>";
        include_once("parts/navigation.php");
        echo "<section class=\"grid-container font-changer\">";

        foreach ($data as $item) {
            $this->printPizzas($item["article_id"], $item["name"], $item["picture"], $item["price"]);
        }

        echo <<< PRINTOTHER
        </section>
        
 <form method="post" action="bestellung.php" accept-charset="utf-8">
 <section class="grid-container-Wk font-changer">
        <article class="cardWk">
        <h2>Warenkorb</h2>

            <select name="selection[]" size="8" id="warenkorbSelection" multiple title="Auswahl" required>
                
            </select>

            <p id="endPreis" data-endPrice="0">Ihr Preis: </p>

            <br />
            <input type="button" name="delAll" value="Alles Löschen" id="delAll"/>
            <br>
            <input type="button" name="delSel" value="Auswahl Löschen" id="delSel"/>

    </article>
    <article class="cardWk">
        <h2>Ihre Daten</h2>
            <input id="vname" type="text" name="vorname" placeholder="Vorname" required>
            <input id="nname" type="text" name="nachname" placeholder="Nachname" required>
            <input id="plz" type="number" name="plz" placeholder="PLZ" required>
            <input id="ort"  type="text" name="ort" placeholder="Ort" required>
            <input id="str"  type="text" name="strasse" placeholder="Straße" required>
            <br>
            
            <input id="bestellen"  type="submit" name="send" value="Bestellen" onclick="selectAll();">
                </article>
                </section>
        </form>
    </body>
PRINTOTHER;
        // to do: output view of this page
        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     */
    protected function processReceivedData():void
    {
        session_start();
        parent::processReceivedData();

        if(isset($_POST["selection"]) && isset($_POST["vorname"])  && isset($_POST["nachname"]) && isset($_POST["plz"]) && isset($_POST["ort"]) && isset($_POST["strasse"])) {
            $pizzas = $_POST["selection"];

            $address = $this->_database->real_escape_string($_POST["strasse"]) . ", " . $this->_database->real_escape_string($_POST["ort"]) .
                ", " . $this->_database->real_escape_string($_POST["plz"]) . ", " . $this->_database->real_escape_string($_POST["vorname"]) . ", " .
                $this->_database->real_escape_string($_POST["nachname"]);

            $sqlEntriesCheck = "SELECT ordering_id FROM ordering WHERE address = '$address'";

            $sqlEntriesCheckResult = $this->_database->query($sqlEntriesCheck);

            if (!$sqlEntriesCheckResult) {
                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
            }



            if ($sqlEntriesCheckResult->num_rows == 0) {

                $sqlInsert = "INSERT INTO ordering(address) VALUES ('$address')";


                $sqlCheck = $this->_database->query($sqlInsert);


                if (!$sqlCheck) {
                    throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
                }

                $_SESSION["customerID"]  = $this->_database->insert_id;


                $sqlGetID = "SELECT ordering_id FROM ordering WHERE address = '$address'";

                $getIdQuery = $this->_database->query($sqlGetID);

                if (!$getIdQuery) {
                    throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
                }

                $getId = $getIdQuery->fetch_assoc();

                $ordering_id = $getId["ordering_id"];

                $pizzaId = [];

                foreach ($pizzas as $item) {
                    //id 1 Salami, 2 Chicken-Barbecue, 3 Hawaii, 4 Margherita, 5 Tonno
                    $pizzaId[] = intval($item[0]);
//                    if ($item === "Salami") {
//                        $pizzaId[] = 1;
//                    }
//                    if ($item === "Chicken-Barbecue") {
//                        $pizzaId[] = 2;
//                    }
//                    if ($item === "Hawaii") {
//                        $pizzaId[] = 3;
//                    }
//                    if ($item === "Margherita") {
//                        $pizzaId[] = 4;
//                    }
//                    if ($item === "Tonno") {
//                        $pizzaId[] = 5;
//                    }
                }

                foreach ($pizzaId as $id) {
                    $sqlInsertOrdered = "INSERT INTO ordered_article(ordering_id, article_id, status) VALUES ('$ordering_id', '$id', 0)";
                    $orderedQuery = $this->_database->query($sqlInsertOrdered);
                    if (!$orderedQuery) {
                        throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
                    }
                }
            }
            header("HTTP/1.1 303 See Other");
            header('Location: bestellung.php');
            die();
        }
        // to do: call processReceivedData() for all members
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     * @return void
     */
    public static function main():void
    {
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page.
// That is input is processed and output is created.
Bestellung::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >
