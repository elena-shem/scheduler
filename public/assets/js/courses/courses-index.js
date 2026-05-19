$(function () {
  var uriBase = window.location.protocol + "//" + window.location.host + "/";

  $('tr').dblclick(function () {
    var id = $(this).attr('id');
    if (id !== undefined) {
      window.location.href = uriBase + 'admin/courses/edit/' + id;
    }
  });

  TableMultiFilter({
    tableSelector: '#sortabletable',
    columnMap: {
      searchId: 1,          // ID
      searchOfCourseId: 2,  // Official Course Id
      searchCode: 3,        // Course Code 1
      searchCode2: 4,       // Course Code 2
      searchTitle: 5,       // Title
      searchProfessor: 6,   // Professor
      searchWinter: 7,      // Sups Winter
      searchSummer: 8,      // Sups Summer
      searchSeptember: 9    // Sups September
    },

    startsWithInputs: [
      'searchId',
      'searchOfCourseId',
      'searchCode',
      'searchCode2'
    ]
  });
});
