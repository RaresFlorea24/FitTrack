<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Utilizatori — jQuery</title>
  <link rel="stylesheet" href="styles1.css">
  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
    <h2>Utilizatori înregistrați (jQuery)</h2>
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
    var ENDPOINT = 'get_users_json.php'; // același backend JSON de la cerința 1

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

    function loadPage(offset) {
      // Dezactivăm butoanele pe durata request-ului
      $('#btn-prev').prop('disabled', true);
      $('#btn-next').prop('disabled', true);

      // Apel AJAX prin jQuery
      $.ajax({
        url:      ENDPOINT,
        method:   'GET',
        dataType: 'json',           // jQuery parsează automat JSON-ul
        data: {
          k:      K,
          offset: offset
        },
        success: function (data) {
          if (!data.success) {
            $('#table-container').html('<p style="color:red">Eroare: ' + data.error + '</p>');
            return;
          }

          currentOffset = data.offset;
          totalRecords  = data.total;

          renderTable(data.records);
          updateControls();
        },
        error: function (xhr) {
          $('#table-container').html('<p style="color:red">Eroare HTTP ' + xhr.status + '.</p>');
        }
      });
    }

    function renderTable(records) {
      $('#loading-msg').hide();

      if (!records || records.length === 0) {
        $('#table-container').html('<p>Nu există înregistrări.</p>');
        return;
      }

      var keys = Object.keys(COLUMNS);

      // Construim headerul tabelului cu jQuery
      var $table = $('<table>');
      var $thead = $('<thead>').appendTo($table);
      var $headerRow = $('<tr>').appendTo($thead);

      $.each(COLUMNS, function (key, label) {
        $('<th>').text(label).appendTo($headerRow);
      });

      // Construim rândurile cu jQuery
      var $tbody = $('<tbody>').appendTo($table);

      $.each(records, function (i, row) {
        var $tr = $('<tr>').appendTo($tbody);
        $.each(keys, function (j, key) {
          var val = (row[key] !== null && row[key] !== undefined) ? row[key] : '-';
          $('<td>').text(val).appendTo($tr);  // .text() face escape automat
        });
      });

      $('#table-container').html($table);
    }

    function updateControls() {
      var currentPage = Math.floor(currentOffset / K) + 1;
      var totalPages  = Math.ceil(totalRecords / K);

      $('#page-info').text(
        'Pagina ' + currentPage + ' din ' + totalPages +
        ' (' + totalRecords + ' utilizatori total)'
      );

      $('#btn-prev').prop('disabled', currentOffset === 0);
      $('#btn-next').prop('disabled', currentOffset + K >= totalRecords);
    }

    // Event listeners prin jQuery
    $('#btn-next').on('click', function () {
      loadPage(currentOffset + K);
    });

    $('#btn-prev').on('click', function () {
      loadPage(currentOffset - K);
    });

    // Prima încărcare
    loadPage(0);
  })();
  </script>
</body>
</html>