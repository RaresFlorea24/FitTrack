<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Planner</title>
    <link rel="stylesheet" href="styles3.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
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

<h1><span title="info">Workout Planner</span></h1>

<form id="plannerForm">
    <fieldset>
        <legend>Date personale</legend>

        <p>Nume: <input type="text" id="name" name="nume" maxlength="20" size="20"></p>

        <p>Vârstă: <input type="number" id="age" name="varsta" max="100" step="1"></p>

        <p>Greutate: <input type="number" id="weight" name="greutate" value="70"></p>

        <p>Înălțime (cm): <input type="number" id="height" name="inaltime" value="170"></p>

        <p>Email: <input type="text" name="email" readonly value="exemplu@gmail.com"></p>

        <p>Adițional: <input type="text" value="blocat" readonly></p>
    </fieldset>

    <br>

    <fieldset>
        <legend>Detalii antrenament</legend>

        <p>Obiectiv:</p>
        <input type="radio" name="obiectiv" value="slabit" checked> Slăbit<br>
        <input type="radio" name="obiectiv" value="masa"> Masă musculară<br>
        <input type="radio" name="obiectiv" value="mentenanta"> Mentenanță<br>

        <br>

        <p>Tip de antrenament:</p>
        <input type="checkbox" name="tip1" checked> Cardio<br>
        <input type="checkbox" name="tip2"> Forță<br>
        <input type="checkbox" name="tip3" disabled> HIIT<br>

        <br>

        <p>Ziua antrenamentului:</p>
        <select name="zi" multiple size="5">
            <option selected>Luni</option>
            <option>Marți</option>
            <option>Miercuri</option>
            <option>Joi</option>
            <option>Vineri</option>
            <option>Sâmbătă</option>
            <option>Duminică</option>
        </select>

        <br><br>

        <p>Comentarii:</p>
        <textarea name="comentarii" rows="4" cols="30">
Introduceți obiectivele și progresul.
        </textarea>
    </fieldset>

    <br>

    <h2>Tabel orizontal</h2>

    <table id="exerciseTable">
        <tr>
            <th onclick="sortTable(0)">Exercițiu</th>
            <th onclick="sortTable(1)">Seturi</th>
            <th onclick="sortTable(2)">Repetări</th>
        </tr>
        <tr>
            <td>Genuflexiuni</td>
            <td>4</td>
            <td>10</td>
        </tr>
        <tr>
            <td>Împins la piept</td>
            <td>3</td>
            <td>12</td>
        </tr>
        <tr>
            <td>Fandari</td>
            <td>5</td>
            <td>8</td>
        </tr>
    </table>

    <h2>Tabel vertical</h2>

    <table id="verticalTable">
        <tr>
            <th onclick="sortVerticalTable(0)">Exercițiu</th>
            <td>Genuflexiuni</td>
            <td>Împins</td>
            <td>Fandări</td>
        </tr>
        <tr>
            <th onclick="sortVerticalTable(1)">Seturi</th>
            <td>4</td>
            <td>3</td>
            <td>5</td>
        </tr>
        <tr>
            <th onclick="sortVerticalTable(2)">Repetări</th>
            <td>10</td>
            <td>12</td>
            <td>8</td>
        </tr>
    </table>

    <br>

    <input type="submit" value="Trimite" name="submit">
    <input type="reset" value="Reset">
</form>

<br>

<h3>Alimentație</h3>

<ol type="A">
    <li>
        <h4>Mic dejun</h4>
        <ul>
            <li>Suplimente
                <ul>
                    <li>Proteine</li>
                    <li>Creatină</li>
                </ul>
            </li>
        </ul>
    </li>
    <li><h5>Prânz</h5></li>
    <li><h6>Cină</h6></li>
</ol>
</body>
</html>