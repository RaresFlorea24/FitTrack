<?php
session_start();
require_once 'db.php';

// parametri din URL
$k      = 2;  // inregistrari pe pagina
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if ($offset < 0) $offset = 0;

// interogare baza de date
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM users");
$total     = (int)$stmtTotal->fetchColumn();

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

// calcule pentru paginare
$currentPage = (int)floor($offset / $k) + 1;
$totalPages  = (int)ceil($total / $k);
$prevOffset  = $offset - $k;
$nextOffset  = $offset + $k;

$isFirst = ($offset === 0);
$isLast  = ($offset + $k >= $total);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Utilizatori — Server-side</title>
  <link rel="stylesheet" href="styles1.css">
  <link rel="stylesheet" href="pagination.css">
</head>
<body>

  <section id="paginated-section">
    <h2>Utilizatori inregistrati (server-side)</h2>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Utilizator</th>
          <th>Data inregistrarii</th>
          <th>Telefon</th>
          <th>Varsta</th>
          <th>Gen</th>
          <th>Obiectiv</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($records as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['id'])         ?></td>
          <td><?= htmlspecialchars($row['username'])   ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><?= htmlspecialchars($row['phone'])      ?></td>
          <td><?= htmlspecialchars($row['age'])        ?></td>
          <td><?= htmlspecialchars($row['gender'])     ?></td>
          <td><?= htmlspecialchars($row['goal'])       ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="pagination-controls">
      <?php if ($isFirst): ?>
        <span class="disabled-btn">&#8592; Previous <?= $k ?></span>
      <?php else: ?>
        <a href="?offset=<?= $prevOffset ?>">&#8592; Previous <?= $k ?></a>
      <?php endif; ?>

      <span class="page-info">
        Pagina <?= $currentPage ?> din <?= $totalPages ?>
        (<?= $total ?> utilizatori total)
      </span>

      <?php if ($isLast): ?>
        <span class="disabled-btn">Next <?= $k ?> &#8594;</span>
      <?php else: ?>
        <a href="?offset=<?= $nextOffset ?>">Next <?= $k ?> &#8594;</a>
      <?php endif; ?>
    </div>
  </section>
</body>
</html>