<?php
$db = new PDO('sqlite:database/database.sqlite');
$stmt = $db->query('select id, image from tour_packages');
if (! $stmt) {
    echo "Query failed\n";
    exit(1);
}
foreach ($stmt as $r) {
    echo $r['id'] . ' => ' . ($r['image'] ?? '[null]') . "\n";
}
