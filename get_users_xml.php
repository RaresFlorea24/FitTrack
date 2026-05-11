<?php
// get_users_xml.php — același endpoint ca get_users.php, dar returnează XML

header('Content-Type: application/xml; charset=utf-8');

require_once 'db.php';

$k      = isset($_GET['k'])      ? (int)$_GET['k']      : 5;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if ($k < 1)      $k = 5;
if ($offset < 0) $offset = 0;

try {
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM users");
    $total = (int)$stmtTotal->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.username,
            u.created_at,
            COALESCE(p.phone,  '-') AS phone,
            COALESCE(p.age,    '-') AS age,
            COALESCE(p.gender, '-') AS gender,
            COALESCE(p.goal,   '-') AS goal
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        ORDER BY u.id
        LIMIT :k OFFSET :offset
    ");
    $stmt->bindValue(':k',      $k,      PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Construim XML manual
    $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<response>' . "\n";
    $xml .= '  <success>true</success>' . "\n";
    $xml .= '  <total>'  . $total  . '</total>' . "\n";
    $xml .= '  <offset>' . $offset . '</offset>' . "\n";
    $xml .= '  <k>'      . $k      . '</k>' . "\n";
    $xml .= '  <records>' . "\n";

    foreach ($records as $row) {
        $xml .= '    <user>' . "\n";
        foreach ($row as $key => $value) {
            $xml .= '      <' . $key . '>' . htmlspecialchars($value, ENT_XML1, 'UTF-8') . '</' . $key . '>' . "\n";
        }
        $xml .= '    </user>' . "\n";
    }

    $xml .= '  </records>' . "\n";
    $xml .= '</response>';

    echo $xml;

} catch (PDOException $e) {
    echo '<?xml version="1.0" encoding="UTF-8"?><response><success>false</success><error>' . htmlspecialchars($e->getMessage(), ENT_XML1, 'UTF-8') . '</error></response>';
}
?>