(function($){
    $(document).ready(function () {
        $('.delete_posts_button').click(function(){
            $(this).css('pointer-events', 'none');
            $(this).html('...'+$(this).html());
        })
    })
})( jQuery );
