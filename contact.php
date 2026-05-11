<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="styles1.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="data.js"></script>
</head>
<body>

<nav class="menu">
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="planner.php">Planner</a></li>
        <li><a href="sprites.php">Sprites</a></li>
        <li><a href="widgets.php">Widgets</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
</nav>

<form id="contactForm">
    <fieldset>
        <legend>Contacteaza-ne</legend>

        <p>Subiect: <input type="text" id="subject" name="subiect" maxlength="200" size="20"></p>

        <p>Mesaj: <textarea id="message" name="mesaj" rows="4" cols="30"></textarea></p>

    </fieldset>
    <br>
    <input type="submit" value="Trimite">
    <input type="reset" value="Reset">
</form>

</body>
</html>