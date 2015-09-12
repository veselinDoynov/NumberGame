<?php

require_once 'NumberGameClass.php';

$numberGame = new NumberGame('123.908','0.0344');

//$numberGame->printMultiplicationResult();


//example usage:
//we will multiple this numbers ...  
//
//
//123823*929323*12365634*939.234343*1239283834

$try = new NumberGame('123823', '929323');
$result = $try->getResult();
$try = new NumberGame($result, '12365634');
$result = $try->getResult();
$try = new NumberGame($result, '939.234343');
$result = $try->getResult();
$try = new NumberGame($result, '1239283834');
$try->printMultiplicationResult();

//outputs 1656262406148980030757226985404.090332 ... normal calculator can not calculate it :) 




 