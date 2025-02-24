<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

class bäcker extends Page
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
        $sql = "SELECT article.name AS name, ordered_article.* FROM ordered_article INNER JOIN article ON ordered_article.article_id =  article.article_id ";

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
        $this->generatePageHeader('Bäcker', '', true); //to do: set optional parameters
        // to do: output view of this page
        echo "<body>";

        include_once("parts/navigation.php");
        echo <<< EOT
         <section>
         <form method="post" action="baecker.php" id="formid">
EOT;


        foreach ($data as $item) {
            $nr = $item["ordered_article_id"];
            $name = $item["name"];
            $status = $item["status"];
            if($status == 0) {
                echo <<< EOT
                    <h3>Bestellnummer $nr Pizza: $name</h3>
                    <input type='radio' id='ordered{$nr}' name={$nr} checked>
                    <label for="ordered{$nr}">Bestellt</label><br>
                    <input type='radio' id='inOven{$nr}' name={$nr} onclick="document.forms['formid'].submit()" value='1'>
                    <label for="inOven{$nr}">Im Ofen</label><br>
                    <input type='radio' id='ready{$nr}' name={$nr} onclick="document.forms['formid'].submit()" value='2'>
                    <label for="ready{$nr}">Fertig</label><br>
EOT;
            }
            else if($status == 1){
                echo <<< EOT
                    <h3>Bestellnummer $nr Pizza: $name</h3>
                    <input type='radio' id='ordered{$nr}' name={$nr}>
                    <label for="ordered{$nr}">Bestellt</label><br>
                    <input type='radio' id='inOven{$nr}' name={$nr} onclick="document.forms['formid'].submit()" value='1' checked>
                    <label for="inOven{$nr}">Im Ofen</label><br>
                    <input type='radio' id='ready{$nr}' name={$nr} onclick="document.forms['formid'].submit()" value='2'>
                    <label for="ready{$nr}">Fertig</label><br>
EOT;
            }
        }
        echo "</form>";
        echo "</section>";
        echo "</body>";
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
        if(count($_POST)){
            foreach($_POST as $key => $value)
            {
//            echo "$key"." "."$value";
                if($value == 1 || $value == 2) {
                    $sqlUpdateOrdArt = "UPDATE ordered_article SET status = '$value' WHERE ordered_article_id = '$key'";

                    $sqlUpdateCheck = $this->_database->query($sqlUpdateOrdArt);

                    if (!$sqlUpdateCheck) {
                        throw new Exception("Abfrage fehlgeschlagen: " . $this->_database->error);
                    }
                }
            }
            header('Location: baecker.php');
            die();
        }
//        exit();
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
            $page = new Bäcker();
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
Bäcker::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >