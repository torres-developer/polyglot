<?php

require "../vendor/autoload.php";

use TorresDeveloper\HTTPMessage\URI;
use TorresDeveloper\Polyglot\Lang;
use TorresDeveloper\Polyglot\Translator;

$from = new Lang("pt");
$to = new Lang("en");

$t = new Translator(new URI("http://127.0.0.1:5000"));
$t->setNative($from);
var_dump($t->translate(
    "Boas pessoal vocês sabem quem fala, "
        . "daqui é o Tiagovski a rebentar a escala.",
    $to
));
