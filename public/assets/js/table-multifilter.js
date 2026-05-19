window.TableMultiFilter = function(opts) {
  var tableSelector = opts.tableSelector || '#sortabletable';
  var columnMap = opts.columnMap || {};
  var startsWithInputs = opts.startsWithInputs || [];
  var eventName = opts.eventName || 'keyup';

  function norm(s) {
    return (s || '').toString().trim().toLowerCase();
  }

  function applyFilters() {
    var filters = {};

    Object.keys(columnMap).forEach(function(inputId) {
      var el = document.getElementById(inputId);
      if (el && el.value.trim() !== '') {
        filters[inputId] = norm(el.value);
      }
    });

    var hasAny = Object.keys(filters).length > 0;

    $(tableSelector).find('tbody tr').each(function() {
      var $row = $(this);

      if (!hasAny) {
        $row.show();
        return;
      }

      var visible = true;

      $.each(filters, function(inputId, value) {
        var colIndex = columnMap[inputId];
        var cellText = norm($row.find('td:nth-child(' + colIndex + ')').text());

        var isStarts = startsWithInputs.indexOf(inputId) !== -1;

        if (isStarts) {
          if (cellText.indexOf(value) !== 0) {
            visible = false;
            return false;
          }
        } else {
          if (cellText.indexOf(value) < 0) {
            visible = false;
            return false;
          }
        }
      });

      $row.toggle(visible);
    });
  }

  Object.keys(columnMap).forEach(function(inputId) {
    $('#' + inputId).on(eventName, applyFilters);
  });

  applyFilters();
};
