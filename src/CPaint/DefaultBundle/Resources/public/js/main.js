$(document).ready(function() {
    $('.bitmap').each(function(){
        if($(this).find("img").attr("bitmap-url")) {
            $(this).find("img").attr("src", $(this).find("img").attr("bitmap-url"));
        }
    });
    $('.empty-pixel').click(function(e){
        $('.empty-pixel').removeClass('selected');
        $(this).addClass('selected');
        $('input[name="position"]').val($(this).attr('data-pos'));
    });
    $('.color').click(function(e){
        e.preventDefault();
        $('.color').removeClass('selected');
        $(this).addClass('selected');
        $('.selected-color').css('background-color', $(this).css('background-color'));
        $('input[name="color"]').val($(this).attr('data-id'));
    });
});