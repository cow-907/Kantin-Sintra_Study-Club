<?php
require 'includes/init.php';

$_SESSION = [];
session_destroy();

header('Location: /kantin-sintra-php/login.php');
exit;
