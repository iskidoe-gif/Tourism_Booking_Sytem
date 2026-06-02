<?php
$pdo = new PDO('sqlite:database/database.sqlite');
foreach (['bookings','payments','tour_packages'] as $table) {
    echo "TABLE: $table\n";
    $stmt = $pdo->query("PRAGMA table_info($table)");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        printf("%s %s %s %s %s %s\n", $col['cid'], $col['name'], $col['type'], $col['notnull'], $col['dflt_value'], $col['pk']);
    }
    echo "\n";
}
