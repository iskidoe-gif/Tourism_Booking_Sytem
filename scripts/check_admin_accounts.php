<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "No sqlite database found at $dbPath\n";
    exit(1);
}
$db = new SQLite3($dbPath);

function dumpRows(SQLite3 $db, string $query): void {
    $result = $db->query($query);
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo json_encode($row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    }
}

echo "--- users with admin role ---\n";
dumpRows($db, "SELECT id, email, password, role FROM users WHERE role = 'admin' LIMIT 10;");
echo "--- admins table ---\n";
dumpRows($db, "SELECT id, email, password, role FROM admins LIMIT 10;");
