<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Utilizatori — XML</title>
  <link rel="stylesheet" href="styles1.css">
  <link rel="stylesheet" href="pagination.css">
</head>
<body>
  <section id="paginated-section">
    <h2>Utilizatori inregistrati (XML)</h2>
    <div id="table-container">
      <p id="loading-msg">Se incarca...</p>
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
    var ENDPOINT = 'get_users_xml.php';

    var COLUMNS = {
      id:         'ID',
      username:   'Utilizator',
      created_at: 'Data inregistrarii',
      phone:      'Telefon',
      age:        'Varsta',
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
          // parsare XML - diferenta fata de versiunea JSON
          var xmlDoc = xhr.responseXML;

          if (!xmlDoc) {
            // fallback: parsam manual daca browserul nu a detectat automat
            var parser = new DOMParser();
            xmlDoc = parser.parseFromString(xhr.responseText, 'application/xml');
          }

          var successNode = xmlDoc.getElementsByTagName('success')[0];
          if (!successNode || successNode.textContent !== 'true') {
            var errNode = xmlDoc.getElementsByTagName('error')[0];
            tableContainer.innerHTML = '<p style="color:red">Eroare: ' + (errNode ? errNode.textContent : 'necunoscuta') + '</p>';
            return;
          }

          totalRecords  = parseInt(xmlDoc.getElementsByTagName('total')[0].textContent, 10);
          currentOffset = parseInt(xmlDoc.getElementsByTagName('offset')[0].textContent, 10);

          // extragem nodurile <user>
          var userNodes = xmlDoc.getElementsByTagName('user');
          var records = [];
          for (var i = 0; i < userNodes.length; i++) {
            var record = {};
            var keys = Object.keys(COLUMNS);
            for (var j = 0; j < keys.length; j++) {
              var node = userNodes[i].getElementsByTagName(keys[j])[0];
              record[keys[j]] = node ? node.textContent : '-';
            }
            records.push(record);
          }

          renderTable(records);
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
        tableContainer.innerHTML = '<p>Nu exista inregistrari.</p>';
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
          var val = records[r][keys[c]] || '-';
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