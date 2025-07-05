// custom-pd-toc/assets/pd-woo-filter.js
jQuery(function ($) {

  /* let GET submit reload the page (no preventDefault) */

  /* Reset: clear selects then reload without query-string */
  $('#pd-filter-reset').on('click', function () {
    $('#pd-products-filter-form select').val('');
    window.location = window.location.pathname; // strip all ?params
  });

});
