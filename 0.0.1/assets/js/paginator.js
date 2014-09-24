$(document).ready(function() {
    
    $('select[name="items_per_page"]').on('change', function() {
        var form = $('form#per-page');
        form.attr('action', form.attr('action') + '/' + $('form#per-page option:selected').attr('value'));
        form.submit();
    });
});