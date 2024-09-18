// Toggles post content that comes with post title
jQuery(document).ready(function($) {
    $('.toggle-title').click(function() {
        $(this).next('.toggle-content').slideToggle();
    });
});
