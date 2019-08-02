<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//формирование массива параметров
$arComponentParameters = array(
    "PARAMETERS" => array(
        "CHAR_CODE"    =>  array(
            "PARENT"    =>  "BASE",
            "NAME"      =>  "Код валюты",
            "TYPE"      =>  "STRING",
            "DEFAULT"   =>  "USD"
        ),
    )
);