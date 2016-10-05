$("input:checkbox").on('click', function(e) {
    $checked = $("#edit-compare input:checked");
    $this = $(this);
    if ($checked.length > 2) {
        $checked.not($this).prop('checked', false);
    }
});
