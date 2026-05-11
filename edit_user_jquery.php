<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Editare utilizator — jQuery</title>
  <link rel="stylesheet" href="styles1.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
    <h2>Editare utilizator (jQuery)</h2>

    <div class="form-group">
      <label for="user-select">Selectează utilizatorul:</label>
      <select id="user-select">
        <option value="">-- alege --</option>
      </select>
    </div>

    <div id="user-fields" style="display:none;">
      <div class="form-group">
        <label for="field-username">Username</label>
        <input type="text" id="field-username" disabled>
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
  (function ($) {

    // ── Stare internă ─────────────────────────────────────
    var isDirty   = false;
    var pendingId = null;

    // ── 1. Populăm <select> cu toți utilizatorii ─────────
    $.ajax({
      url:      'get_users_list.php',
      method:   'GET',
      dataType: 'json',
      success: function (data) {
        if (!data.success) return;
        $.each(data.users, function (i, u) {
          $('#user-select').append(
            $('<option>').val(u.id).text(u.id + ' — ' + u.username)
          );
        });
      }
    });

    // ── 2. La schimbarea selecției ────────────────────────
    $('#user-select').on('change', function () {
      var selectedId = $(this).val();
      if (!selectedId) return;

      // Cerința 4: avertizare modificări nesalvate
      if (isDirty) {
        pendingId = selectedId;
        var wantsSave = confirm(
          'Există modificări nesalvate!\n\nDorești să salvezi înainte de a continua?'
        );
        if (wantsSave) {
          saveUser(function () {
            loadUser(pendingId);
            pendingId = null;
          });
        } else {
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

      $.ajax({
        url:      'get_user.php',
        method:   'GET',
        dataType: 'json',
        data:     { id: id },
        success: function (data) {
          if (!data.success) {
            setStatus('Eroare: ' + data.error, 'error');
            return;
          }
          var u = data.user;
          $('#field-username').val(u.username);
          $('#field-phone').val(u.phone);
          $('#field-age').val(u.age);
          $('#field-gender').val(u.gender);
          $('#field-goal').val(u.goal);

          $('#user-fields').show();

          isDirty = false;
          $('#btn-save').prop('disabled', true);
        },
        error: function (xhr) {
          setStatus('Eroare HTTP ' + xhr.status, 'error');
        }
      });
    }

    // ── Cerința 2: activăm Save la orice modificare ───────
    $('#field-phone, #field-age, #field-gender, #field-goal')
      .on('change input', function () {
        isDirty = true;
        $('#btn-save').prop('disabled', false);
        setStatus('', '');
      });

    // ── Cerința 3: butonul Save ───────────────────────────
    $('#btn-save').on('click', function () {
      saveUser(null);
    });

    function saveUser(callback) {
      var id = $('#user-select').val();
      if (!id) return;

      var payload = {
        id:     parseInt(id, 10),
        phone:  $('#field-phone').val(),
        age:    parseInt($('#field-age').val(), 10) || 0,
        gender: $('#field-gender').val(),
        goal:   $('#field-goal').val()
      };

      $('#btn-save').prop('disabled', true);
      setStatus('Se salvează...', '');

      $.ajax({
        url:         'save_user.php',
        method:      'POST',
        contentType: 'application/json',
        dataType:    'json',
        data:        JSON.stringify(payload),
        success: function (data) {
          if (!data.success) {
            setStatus('Eroare: ' + data.error, 'error');
            $('#btn-save').prop('disabled', false);
            return;
          }
          isDirty = false;
          setStatus('Salvat cu succes!', 'success');
          if (typeof callback === 'function') callback();
        },
        error: function (xhr) {
          setStatus('Eroare HTTP ' + xhr.status, 'error');
          $('#btn-save').prop('disabled', false);
        }
      });
    }

    // ── Helper status ─────────────────────────────────────
    function setStatus(msg, type) {
      $('#status-msg').text(msg).attr('class', type);
    }

  })(jQuery);
  </script>
</body>
</html>