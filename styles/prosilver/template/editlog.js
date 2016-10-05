$("input:checkbox").on('click', function(e) {
    $checked = $("#edit-compare input:checked");
    $this = $(this);
    if ($checked.length > 2) {
        $checked.not($this).prop('checked', false);
    }
    if ($this.prop('checked')) {
        $this.prop('checked', true);
    }
});
