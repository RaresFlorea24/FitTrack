<?php
// PATH TRAVERSAL VULNERABIL
if (isset($_GET['file'])) {
    $file = $_GET['file']; // fără sanitizare
    $path = 'uploads/' . $file;
    
    if (file_exists($path)) {
        echo "<pre>" . file_get_contents($path) . "</pre>";
    } else {
        echo "Fișierul nu există: " . $path;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>View File</title></head>
<body>
    <h2>Vizualizare fișier</h2>
    <form method="GET">
        <input type="text" name="file" placeholder="Nume fișier..." size="40">
        <button type="submit">Vizualizează</button>
    </form>
</body>
</html>