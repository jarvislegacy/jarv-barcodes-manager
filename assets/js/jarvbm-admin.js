jQuery(document).ready(function ($) {

    /* ---------------------------
       CONTROL DE PESTAÑAS DEL PANEL
    --------------------------- */
    $('.nav-tab').on('click', function (e) {
        e.preventDefault();

        // Quitar activo de todas
        $('.nav-tab').removeClass('nav-tab-active');
        $('.jarvis-tab-content').hide();

        // Activar la pestaña clicada
        $(this).addClass('nav-tab-active');
        $($(this).attr('href')).fadeIn();
    });

    /* ---------------------------
       SELECT2 (opcional)
    --------------------------- */
    if ($.fn.select2) {
        $('.select2').select2({
            placeholder: function () {
                return $(this).data('placeholder') || '';
            },
            allowClear: true,
            width: 'resolve'
        });
    }

});
