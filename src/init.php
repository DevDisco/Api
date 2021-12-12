<?php

const SRC_FOLDER = "../../../src/";
const CLASS_FOLDER = SRC_FOLDER."classes/";
const TEMPLATES_FOLDER = SRC_FOLDER."templates/";

//A simple autoloader that grabs everything in /classes/, no need for composer


if (is_dir(CLASS_FOLDER)) {

    $dir = new DirectoryIterator(CLASS_FOLDER);

    foreach ($dir as $fileinfo) {
        
        if (!$fileinfo->isDot() && $fileinfo->getExtension() === "php") {
            
            require_once($fileinfo->getPathname());
        }
    }
}
