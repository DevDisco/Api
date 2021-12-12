<?php

$config = new Config();
$error = new SimpleError();
$request = new Request();
$database = new Database($config, $error);

//the request must include the name of the table you want to read from
//and the id of the record you want to retrieve, which must be named 'id'
$id = $request->getID();
$table = $request->getTable();

if ($table === false) {

    $error->setError("No or not a valid table.", 406);
} else {

    if ( is_file( "../Custom.php" ) ){
        
        require_once("../Custom.php" );
        $custom = new Custom($database);
        $result = $custom->execute($table, $id);
    }
    else{
    
        //run query, will report on invalid parameters
        $result = $database->run($table, $id);
    }

    Logger::toLog($result, "result");

    if (is_array($result)) {

        if (!DEBUG) {

            header('Content-type: application/json');
            print json_encode($result);
            exit;
        }
    } 
}

//any errors are returned as a json object
if (!DEBUG) {

    header('Content-type: application/json');
    print json_encode($error->getErrorArray());
    exit;
}

//this is just for debugging
$error->printError();
Logger::printLog();
