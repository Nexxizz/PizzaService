<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

class fahrer extends Page
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
        $sql = "SELECT ordered_article.ordering_id AS customerID, ordered_article.status AS status, ordering.address AS address, 
                ordered_article.ordered_article_id AS articleID
                FROM ordered_article INNER JOIN ordering ON ordered_article.ordering_id = ordering.ordering_id";

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
        $this->generatePageHeader('Fahrer', '', true); //to do: set optional parameters
        // to do: output view of this page
        echo "<body>";
        include_once("parts/navigation.php");

        echo "<section>";
        echo "<form method=\"post\" action=\"fahrer.php\" accept-charset=\"utf-8\" id=\"formid\">";
        foreach($data as $item) {
            // Status 2 fertig gebacken
            $customerID = $item["customerID"];
            $articleID = $item["articleID"];
            $address = htmlspecialchars($item["address"]);
            if(2 == $item["status"]){
                echo <<< EOT
                <h3>Kundenummer:$customerID</h3>
                <h4>$address</h4>       
                    <input type='radio' id='fertig{$articleID}' name={$articleID} checked>
                    <label for="fertig{$articleID}">Fertig gebacken</label><br>
                    <input type='radio' id='unterwegs{$articleID}' name={$articleID} onclick="document.forms['formid'].submit()" value='3'>
                    <label for="unterwegs{$articleID}">Unterwegs</label><br>
                    <input type='radio' id='geliefert{$articleID}' name={$articleID} onclick="document.forms['formid'].submit()" value='4'>
                    <label for="geliefert{$articleID}">Geliefert</label><br>
EOT;
            }
            if(3 == $item["status"]){
                echo <<< EOT
            <h3>Kundenummer:$customerID</h3>
                <h4>$address</h4>       
                    <input type='radio' id='fertig{$articleID}' name={$articleID}>
                    <label for="fertig{$articleID}">Fertig gebacken</label><br>
                    <input type='radio' id='unterwegs{$articleID}' name={$articleID} onclick="document.forms['formid'].submit()" value='3' checked>
                    <label for="unterwegs{$articleID}">Unterwegs</label><br>
                    <input type='radio' id='geliefert{$articleID}' name={$articleID} onclick="document.forms['formid'].submit()" value='4'>
                    <label for="geliefert{$articleID}">Geliefert</label><br>
EOT;

            }
        }
        echo "</form>";
        echo "</section>";
        echo "</body>";

//        echo <<< PRINT
//<section>
//    <h3>Bestellnummer 1</h3>
//    <p>Vorname Name, Straße Nr, PLZ Stadt</p>
//    <form method="get" action="https://echo.fbi.h-da.de/" accept-charset="utf-8">
//        <p>Status:</p>
//        <select name="selection[]" size="1" multiple title="Auswahl">
//            <option selected>Gebacken</option>
//            <option>Unterwegs</option>
//            <option>Geliefert</option>
//        </select>
//        <input type="submit" name="send" value="Weiterleiten">
//    </form>
//</section>
//
//PRINT;

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
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
        if(count($_POST)){
            foreach($_POST as $key => $value)
            {
                if($value == 3 || $value == 4) {
                    $sqlUpdateOrdArt = "UPDATE ordered_article SET status = '$value' WHERE ordered_article_id = '$key'";

                    $sqlUpdateCheck = $this->_database->query($sqlUpdateOrdArt);

                    if (!$sqlUpdateCheck) {
                        throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
                    }


            }
        }
//            $sqlCheckIfFertig = "SELECT ordering_id FROM ordered_article WHERE status = 4";
//
//            $sqlCheckFertigResult = $this->_database->query($sqlCheckIfFertig);
//
//            if (!$sqlCheckFertigResult) {
//                throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
//            }
//
//            $ordering_id = array();
//
//            if($sqlCheckFertigResult->num_rows > 0) {
//
//                $sqlCheckFertigRecord = $sqlCheckFertigResult->fetch_assoc();
//                while ($sqlCheckFertigRecord) {
//                    $ordering_id[] = $sqlCheckFertigRecord["ordering_id"];
//                    $sqlCheckFertigRecord = $sqlCheckFertigResult->fetch_assoc();
//                }
//            }
//
//            $sqlCheckFertigResult->free();
//
//            foreach($ordering_id as $orderingItem){
//                $sqlChangeStatus = "UPDATE ordered_article SET status = '4' WHERE ordering_id = '$orderingItem'";
//
//                $sqlUpdateCheck = $this->_database->query($sqlChangeStatus);
//
//                if (!$sqlUpdateCheck) {
//                    throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
//                }
//            }

            header('Location: fahrer.php');
            die();
        }
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
            $page = new Fahrer();
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
Fahrer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >