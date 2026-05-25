<?php
// PATH TRAVERSAL VULNERABIL
if (isset($_GET['file'])) {
    $file = $_GET['file']; // fara sanitizare
    $path = 'uploads/' . $file;
    
    if (file_exists($path)) {
        echo "<pre>" . file_get_contents($path) . "</pre>";
    } else {
        echo "Fisierul nu exista: " . $path;
    }
}

// // COD SECURIZAT :
// if (isset($_GET['file'])) {
//     $file = basename($_GET['file']);
//     $path = realpath('uploads/' . $file);
//     $allowed_dir = realpath('uploads/');

//     if (!$path || strpos($path, $allowed_dir) !== 0) {
//         die("Acces nepermis!");
//     }

//     echo file_get_contents($path);
// }
?>

<!DOCTYPE html>
<html>
<head><title>View File</title></head>
<body>
    <h2>Vizualizare fisier</h2>
    <form method="GET">
        <input type="text" name="file" placeholder="Nume fișier..." size="40">
        <button type="submit">Vizualizeaza</button>
    </form>
</body>
</html>