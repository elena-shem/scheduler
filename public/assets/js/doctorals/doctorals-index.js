$(function () {

  TableMultiFilter({
    tableSelector: '#sortabletable',
    columnMap: {
      searchId: 1,
      searchAM: 2,
      searchSurname: 3,
      searchName: 4,
      searchProfessor: 5,
      searchEmail: 6,
      searchHoursRemaining: 11
    },
    startsWithInputs: ['searchId','searchAM','searchSurname','searchName','searchHoursRemaining']
  });

  var uriBase = window.location.protocol + "//" + window.location.host + "/";

  // double click row -> open doctoral
  $('#sortabletable tbody').on('dblclick', 'tr', function () {
    var id = $(this).attr('id');
    if (id) {
      window.location.href = uriBase + 'admin/doctorals/view/' + id;
    }
  });

  // sticky horizontal scrollbar
var $wrap = $('#doctoralsTableScroll');
var $sticky = $('#stickyHScroll');
var $inner = $sticky.find('.sticky-hscroll__inner');

if (!$wrap.length || !$sticky.length || !$inner.length) return;

function positionSticky() {
  var rect = $wrap.get(0).getBoundingClientRect();
  $sticky.css({
    position: 'fixed',
    left: rect.left + 'px',
    width: rect.width + 'px',
    bottom: '0px'
  });
}
function refresh() {
  positionSticky();
  $inner.width($wrap.get(0).scrollWidth);
}

function inView() {
  var r = $wrap.get(0).getBoundingClientRect();
  var vh = window.innerHeight || document.documentElement.clientHeight;
  return r.top < (vh - 120) && r.bottom > 120;
}

function toggle() {
  var wrap = $wrap.get(0);
  if (inView() && wrap.scrollWidth > wrap.clientWidth + 2) {
    refresh();
    $sticky.show();
    $sticky.scrollLeft($wrap.scrollLeft());
  } else {
    $sticky.hide();
  }
}

var lock = false;

$sticky.on('scroll', function () {
  if (lock) return;
  lock = true;
  $wrap.scrollLeft($sticky.scrollLeft());
  lock = false;
});

$wrap.on('scroll', function () {
  if (lock) return;
  lock = true;
  $sticky.scrollLeft($wrap.scrollLeft());
  lock = false;
});

$(window).on('resize', function () {
  refresh();
  toggle();
});

$(window).on('scroll', function () {
  toggle();
});

refresh();
toggle();

});
