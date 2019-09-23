<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=okasha;charset=UTF8', 'admin', 'ssssaa');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);