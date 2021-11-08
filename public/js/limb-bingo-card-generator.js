jQuery(document).ready(function ($) {
    /**
     * On title change
     */
    $('#lbcg-title').bind('input propertychange', function () {
        $('div.lbcg-card-header span.lbcg-card-header-text').html($(this).val());
    });

    /**
     * On subtitle letters change
     */
    $('label.lbcg-label--single input.lbcg-input').bind('input propertychange', function () {
        const letterElements = $('div.lbcg-card-subtitle span.lbcg-card-subtitle-text span');
        const $this = $(this);
        const id = $this.attr('id');
        const index = id.replace('lbcg-subtitle-', '') - 1;
        $(letterElements[index]).html($this.val());
    });
});