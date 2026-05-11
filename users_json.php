<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Utilizatori</title>
  <link rel="stylesheet" href="styles1.css">
  <style>
    #paginated-section {
      margin: 2rem auto;
      max-width: 960px;
    }
    #table-container table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 1rem;
    }
    #table-container th,
    #table-container td {
      padding: 0.55rem 0.9rem;
      border: 1px solid #ccc;
      text-align: left;
      font-size: 0.95rem;
    }
    #table-container th {
      background: #e8f5e9;
      font-weight: bold;
    }
    #table-container tr:nth-child(even) {
      background: #f9f9f9;
    }
    #pagination-controls {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-top: 0.5rem;
    }
    #pagination-controls button {
      padding: 0.45rem 1.1rem;
      cursor: pointer;
      border: 1px solid #4caf50;
      background: #4caf50;
      color: white;
      border-radius: 4px;
      font-size: 0.9rem;
    }
    #pagination-controls button:disabled {
      opacity: 0.35;
      cursor: not-allowed;
    }
    #page-info {
      font-size: 0.88rem;
      color: #555;
    }
  </style>
</head>
<body>

  <section id="paginated-section">
    <h2>Utilizatori înregistrați</h2>
    <div id="table-container">
      <p id="loading-msg">Se încarcă...</p>
    </div>
    <div id="pagination-controls">
      <button id="btn-prev" disabled>&#8592; Previous 2</button>
      <span id="page-info"></span>
      <button id="btn-next">Next 2 &#8594;</button>
    </div>
  </section>

  <script>
  (function () {
    var K        = 2;
    var ENDPOINT = 'get_users_json.php';

    var COLUMNS = {
      id:         'ID',
      username:   'Utilizator',
      created_at: 'Data înregistrării',
      phone:      'Telefon',
      age:        'Vârstă',
      gender:     'Gen',
      goal:       'Obiectiv'
    };

    var currentOffset = 0;
    var totalRecords  = 0;

    var tableContainer = document.getElementById('table-container');
    var btnPrev        = document.getElementById('btn-prev');
    var btnNext        = document.getElementById('btn-next');
    var pageInfo       = document.getElementById('page-info');

    function loadPage(offset) {
      btnPrev.disabled = true;
      btnNext.disabled = true;

      var xhr = new XMLHttpRequest();
      xhr.open('GET', ENDPOINT + '?k=' + K + '&offset=' + offset, true);

      xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4) return;

        if (xhr.status === 200) {
          var data;
          try {
            data = JSON.parse(xhr.responseText);
          } catch (e) {
            tableContainer.innerHTML = '<p style="color:red">Eroare la parsarea JSON.</p>';
            return;
          }

          if (!data.success) {
            tableContainer.innerHTML = '<p style="color:red">Eroare: ' + data.error + '</p>';
            return;
          }

          currentOffset = data.offset;
          totalRecords  = data.total;

          renderTable(data.records);
          updateControls();
        } else {
          tableContainer.innerHTML = '<p style="color:red">Eroare HTTP ' + xhr.status + '.</p>';
        }
      };

      xhr.send();
    }

    function renderTable(records) {
      var loadingMsg = document.getElementById('loading-msg');
      if (loadingMsg) loadingMsg.style.display = 'none';

      if (!records || records.length === 0) {
        tableContainer.innerHTML = '<p>Nu există înregistrări.</p>';
        return;
      }

      var keys = Object.keys(COLUMNS);
      var html = '<table><thead><tr>';

      for (var i = 0; i < keys.length; i++) {
        html += '<th>' + COLUMNS[keys[i]] + '</th>';
      }
      html += '</tr></thead><tbody>';

      for (var r = 0; r < records.length; r++) {
        html += '<tr>';
        for (var c = 0; c < keys.length; c++) {
          var val = records[r][keys[c]];
          if (val === null || val === undefined) val = '-';
          html += '<td>' + escapeHtml(String(val)) + '</td>';
        }
        html += '</tr>';
      }

      html += '</tbody></table>';
      tableContainer.innerHTML = html;
    }

    function updateControls() {
      var currentPage = Math.floor(currentOffset / K) + 1;
      var totalPages  = Math.ceil(totalRecords / K);

      pageInfo.textContent = 'Pagina ' + currentPage + ' din ' + totalPages +
                             ' (' + totalRecords + ' utilizatori total)';

      btnPrev.disabled = (currentOffset === 0);
      btnNext.disabled = (currentOffset + K >= totalRecords);
    }

    function escapeHtml(str) {
      return str
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;');
    }

    btnNext.addEventListener('click', function () {
      loadPage(currentOffset + K);
    });

    btnPrev.addEventListener('click', function () {
      loadPage(currentOffset - K);
    });

    loadPage(0);
  })();
  </script>
</body>
</html>