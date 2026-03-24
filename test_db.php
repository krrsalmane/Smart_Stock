<?php
try {
    echo "Connecting...\n";
    $pdo = new PDO('mysql:host=db;dbname=smartstock;connect_timeout=3', 'root', 'root@123');
    echo "Connected successfully!\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
