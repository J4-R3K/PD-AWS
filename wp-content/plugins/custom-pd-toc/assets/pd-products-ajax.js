(function ($) {

  $(document).on('click', '.pd-ajax-page', function (e) {
    e.preventDefault();

    const $oldWrapper = $(this).closest('.pd-woo-products-wrapper');
    const paged   = $(this).data('page');
    const posts   = $oldWrapper.data('count');
    const cat     = $oldWrapper.data('cat');
    const columns = $oldWrapper.data('columns');

    $.ajax({
      url:   pd_ajax_obj.ajax_url,
      method:'POST',
      data:  {
        action: 'pd_load_more_products',
        nonce:  pd_ajax_obj.nonce,
        paged:  paged,
        posts_per_page: posts,
        filter_cat: cat,
        columns: columns
      },
      beforeSend() {
        $oldWrapper.addClass('loading');
      },
      success(res) {
        if (res.success) {
          const $newWrapper = $(res.data);
          $oldWrapper.replaceWith($newWrapper);
          $newWrapper.removeClass('loading');   // ✅  remove spinner from *new* markup
        } else {
          console.warn('PD Ajax responded with error:', res);
        }
      },
      error(err) {
        console.error('PD Ajax error:', err);
        $oldWrapper.removeClass('loading');
      }
    });
  });

})(jQuery);
