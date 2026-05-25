<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Utilizatori — jQuery</title>
  <link rel="stylesheet" href="styles1.css">
  <link rel="stylesheet" href="pagination.css">
  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

  <section id="paginated-section">
    <h2>Utilizatori inregistrati (jQuery)</h2>
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
    var ENDPOINT = 'get_users_json.php'; // acelasi backend JSON ca la cerinta 1

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

    function loadPage(offset) {
      // dezactivam butoanele cat timp se incarca
      $('#btn-prev').prop('disabled', true);
      $('#btn-next').prop('disabled', true);

      // apel AJAX prin jQuery
      $.ajax({
        url:      ENDPOINT,
        method:   'GET',
        dataType: 'json',           // jQuery parseaza automat JSON-ul
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
        $('#table-container').html('<p>Nu exista inregistrari.</p>');
        return;
      }

      var keys = Object.keys(COLUMNS);

      // construim headerul tabelului cu jQuery
      var $table = $('<table>');
      var $thead = $('<thead>').appendTo($table);
      var $headerRow = $('<tr>').appendTo($thead);

      $.each(COLUMNS, function (key, label) {
        $('<th>').text(label).appendTo($headerRow);
      });

      // construim randurile cu jQuery
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

    // event listeners cu jQuery
    $('#btn-next').on('click', function () {
      loadPage(currentOffset + K);
    });

    $('#btn-prev').on('click', function () {
      loadPage(currentOffset - K);
    });

    // prima incarcare
    loadPage(0);
  })();
  </script>
</body>
</html>