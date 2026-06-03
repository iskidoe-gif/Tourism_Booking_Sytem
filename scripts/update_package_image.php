<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "DB not found: $dbPath\n";
    exit(1);
}
$db = new SQLite3($dbPath);
$updated = $db->exec("UPDATE tour_packages SET image='images/bolinao-church.jpg' WHERE id=13;");
$result = $db->querySingle("SELECT image FROM tour_packages WHERE id=13;");
echo $result ?: 'NULL';
