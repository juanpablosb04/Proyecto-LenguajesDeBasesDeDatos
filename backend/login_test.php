<?php
require 'login.php';

if(login('maria@gmail.com', '123456')){
    echo 'Login Correcto' . PHP_EOL;
}else{
    echo 'Login incorrecto' . PHP_EOL;
}

