<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
    if ( hello_elementor_display_header_footer() ) {
        if ( did_action( 'elementor/loaded' ) && hello_header_footer_experiment_active() ) {
            get_template_part( 'template-parts/dynamic-footer' );
        } else {
            get_template_part( 'template-parts/footer' );
        }
    }
}
?>

<?php wp_footer(); ?>
<!-- Existing scripts for LinkedIn, Google, etc. -->
<!-- LinkedIn Insight Tag -->
<script type="text/javascript">
_linkedin_partner_id = "6601841";
window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script>
<script type="text/javascript">
(function(l) {
if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
window.lintrk.q=[]}
var s = document.getElementsByTagName("script")[0];
var b = document.createElement("script");
b.type = "text/javascript";b.async = true;
b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
s.parentNode.insertBefore(b, s);})(window.lintrk);
</script>
<noscript>
<img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid=6601841&fmt=gif" />
</noscript>
<!-- End LinkedIn Insight Tag -->


</body>
</html>
