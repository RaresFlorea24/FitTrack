<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Editare utilizator</title>
  <link rel="stylesheet" href="styles1.css">
  <style>
    #edit-section {
      margin: 2rem auto;
      max-width: 500px;
    }
    #edit-section h2 {
      margin-bottom: 1rem;
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.3rem;
      font-weight: bold;
      font-size: 0.95rem;
    }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 0.45rem 0.7rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 0.95rem;
      box-sizing: border-box;
    }
    #btn-save {
      padding: 0.5rem 1.4rem;
      background: #4caf50;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
    }
    #btn-save:disabled {
      opacity: 0.35;
      cursor: not-allowed;
    }
    #status-msg {
      margin-top: 0.8rem;
      font-size: 0.92rem;
      min-height: 1.2rem;
    }
    #status-msg.success { color: green; }
    #status-msg.error   { color: red;   }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <section id="edit-section">
    <h2>Editare utilizator</h2>

    <div class="form-group">
      <label for="user-select">Selectează utilizatorul:</label>
      <select id="user-select">
        <option value="">-- alege --</option>
      </select>
    </div>

    <!-- Câmpurile se populează după selectare -->
    <div id="user-fields" style="display:none;">
      <div class="form-group">
        <label for="field-username">Username</label>
        <input type="text" id="field-username" disabled>
        <!-- username e read-only, nu îl edităm -->
      </div>
      <div class="form-group">
        <label for="field-phone">Telefon</label>
        <input type="text" id="field-phone">
      </div>
      <div class="form-group">
        <label for="field-age">Vârstă</label>
        <input type="number" id="field-age" min="1" max="120">
      </div>
      <div class="form-group">
        <label for="field-gender">Gen</label>
        <select id="field-gender">
          <option value="masculin">Masculin</option>
          <option value="feminin">Feminin</option>
          <option value="altul">Altul</option>
        </select>
      </div>
      <div class="form-group">
        <label for="field-goal">Obiectiv</label>
        <select id="field-goal">
          <option value="slabit">Slăbit</option>
          <option value="mentenanta">Mentenanță</option>
          <option value="masa musculara">Masă musculară</option>
        </select>
      </div>

      <button id="btn-save" disabled>Salvează</button>
      <div id="status-msg"></div>
    </div>
  </section>

  <script>
  (function () {

    // ── Referințe DOM ─────────────────────────────────────
    var userSelect   = document.getElementById('user-select');
    var userFields   = document.getElementById('user-fields');
    var fieldPhone   = document.getElementById('field-phone');
    var fieldAge     = document.getElementById('field-age');
    var fieldGender  = document.getElementById('field-gender');
    var fieldGoal    = document.getElementById('field-goal');
    var fieldUsername= document.getElementById('field-username');
    var btnSave      = document.getElementById('btn-save');
    var statusMsg    = document.getElementById('status-msg');

    // ── Stare internă ─────────────────────────────────────
    var isDirty      = false;   // true dacă există modificări nesalvate
    var pendingId    = null;    // ID-ul pe care vrea să treacă utilizatorul

    // ── Funcție AJAX generică ─────────────────────────────
    function ajax(method, url, data, callback) {
      var xhr = new XMLHttpRequest();
      xhr.open(method, url, true);

      xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4) return;
        if (xhr.status === 200) {
          try {
            callback(null, JSON.parse(xhr.responseText));
          } catch (e) {
            callback('Eroare parsare JSON');
          }
        } else {
          callback('Eroare HTTP ' + xhr.status);
        }
      };

      if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(data));
      } else {
        xhr.send();
      }
    }

    // ── 1. Populăm <select> cu toți utilizatorii ─────────
    ajax('GET', 'get_users_list.php', null, function (err, data) {
      if (err || !data.success) return;
      data.users.forEach(function (u) {
        var opt = document.createElement('option');
        opt.value       = u.id;
        opt.textContent = u.id + ' — ' + u.username;
        userSelect.appendChild(opt);
      });
    });

    // ── 2. La schimbarea selecției ────────────────────────
    userSelect.addEventListener('change', function () {
      var selectedId = userSelect.value;
      if (!selectedId) return;

      // Cerința 4: dacă există modificări nesalvate, întrebăm
      if (isDirty) {
        pendingId = selectedId;

        var wantsSave = confirm(
          'Există modificări nesalvate!\n\nDorești să salvezi înainte de a continua?'
        );

        if (wantsSave) {
          // Salvăm, apoi încărcăm noul utilizator
          saveUser(function () {
            loadUser(pendingId);
            pendingId = null;
          });
        } else {
          // Renunțăm la modificări și încărcăm noul utilizator
          isDirty = false;
          loadUser(pendingId);
          pendingId = null;
        }
        return;
      }

      loadUser(selectedId);
    });

    // ── Încarcă datele unui utilizator prin AJAX ──────────
    function loadUser(id) {
      setStatus('', '');

      ajax('GET', 'get_user.php?id=' + id, null, function (err, data) {
        if (err || !data.success) {
          setStatus('Eroare la încărcare: ' + (err || data.error), 'error');
          return;
        }

        var u = data.user;
        fieldUsername.value = u.username;
        fieldPhone.value    = u.phone;
        fieldAge.value      = u.age;
        setSelectValue(fieldGender, u.gender);
        setSelectValue(fieldGoal,   u.goal);

        userFields.style.display = 'block';

        // Cerința 2: Save rămâne disabled până la o modificare
        isDirty          = false;
        btnSave.disabled = true;
      });
    }

    // ── Cerința 2: activăm Save la orice modificare ───────
    [fieldPhone, fieldAge, fieldGender, fieldGoal].forEach(function (field) {
      field.addEventListener('change', function () {
        isDirty          = true;
        btnSave.disabled = false;
        setStatus('', '');
      });
      // input pentru câmpuri text/number (change nu se declanșează la fiecare tastă)
      field.addEventListener('input', function () {
        isDirty          = true;
        btnSave.disabled = false;
        setStatus('', '');
      });
    });

    // ── Cerința 3: butonul Save ───────────────────────────
    btnSave.addEventListener('click', function () {
      saveUser(null);
    });

    function saveUser(callback) {
      var id = userSelect.value;
      if (!id) return;

      var payload = {
        id:     parseInt(id, 10),
        phone:  fieldPhone.value,
        age:    parseInt(fieldAge.value, 10) || 0,
        gender: fieldGender.value,
        goal:   fieldGoal.value
      };

      btnSave.disabled = true;
      setStatus('Se salvează...', '');

      ajax('POST', 'save_user.php', payload, function (err, data) {
        if (err || !data.success) {
          setStatus('Eroare: ' + (err || data.error), 'error');
          btnSave.disabled = false;
          return;
        }

        isDirty = false;
        setStatus('Salvat cu succes!', 'success');

        if (typeof callback === 'function') callback();
      });
    }

    // ── Helpers ───────────────────────────────────────────
    function setSelectValue(selectEl, value) {
      for (var i = 0; i < selectEl.options.length; i++) {
        if (selectEl.options[i].value === value) {
          selectEl.selectedIndex = i;
          return;
        }
      }
    }

    function setStatus(msg, type) {
      statusMsg.textContent = msg;
      statusMsg.className   = type;
    }

  })();
  </script>
</body>
</html>