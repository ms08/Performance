<?php

$file = fopen("products.csv", "r");
if($file){
    $firstLine = fgets($file);
    $parser = new CSVParser();
    $parser->getColumns($firstLine);

    while(($line = fgets($file)) !== false){
        $parser->parseLine($line);
    }

    echo("<pre>");
    var_dump($parser->getQueries());
    echo("</pre>");
    fclose($file);
}

class CSVParser {
    private $columnsString;
    private $queries;

    public function getColumns($firstLine){
        $this->columnsString = str_replace(";",",",$firstLine);
    }
    public function parseLine($line){
        $values = explode(";",$line);
        for($i = 0; $i<count($values);$i++){
            $values[$i] = "'".$values[$i]."'";
        }

        $mysqlLine = implode(",",$values);

        $this->queries[] = "INSERT INTO table($this->columnsString) VALUES($mysqlLine);";

    }

    public function getQueries(){
        return $this->queries;
    }

}