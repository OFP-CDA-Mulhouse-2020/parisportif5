<?php

$servername = "mysql";
    $username = "root";
    $password = "test";
    $dbname = "mysql";
    $port = "3306";
    try {
        $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
        $pdo ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<br>Succès de la connexion<br>";
    } catch (PDOException $e) {
        echo "<br>Échec de la connexion : " . $e -> getMessage() . '<br>';
    }
    $result = $pdo->query('SHOW DATABASES');
    echo '<pre>';
    print_r($result->fetchAll(\PDO::FETCH_ASSOC));
    echo '</pre>';