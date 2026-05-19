$(function () {
  var uriBase = window.location.protocol + "//" + window.location.host + "/";

  $('tr').dblclick(function () {
    var id = $(this).attr('id');
    if (id !== undefined) {
      window.location.href = uriBase + 'admin/professors/edit/' + id;
    }
  });

  // единый поиск по всем полям одновременно
  TableMultiFilter({
    tableSelector: '#sortabletable',
    columnMap: {
      searchId: 1,         // Id
      searchSurname: 2,    // Surname
      searchName: 3,       // Name
      searchEmail: 4,      // Email
      searchTelephone: 5,  // Telephone
      searchOffice: 6      // Office
    },
    // как обычно: id/surname/name начинаются с, остальное содержит
    startsWithInputs: ['searchId', 'searchSurname', 'searchName']
  });
});
