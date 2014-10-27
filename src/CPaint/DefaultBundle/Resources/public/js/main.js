$(document).ready(function() {
    /** Responsive drawing **/
    function respondDrawing() {
        if ($("#drawing").length === 0) {
            console.log("No drawing");
            return;
        }
        
        var width = $("#drawing").width();
        var dWidth = $("#drawing").attr("drawing-width");
        var pWidth = Math.floor(width / dWidth) + "px";
        $("#drawing").find(".pixel").css('width', pWidth).css('height', pWidth);
    }
    $(window).resize(respondDrawing);
    respondDrawing();
    /** End responsibe drawing **/
    
    // select a color
    $('.color').click(function(e) {
        e.preventDefault();
        $('.color').removeClass('selected');
        $(this).addClass('selected');
        $('#drawing').css('border-color', $(this).css('background-color'));
        $('input[name="color"]').val($(this).attr('data-id'));
    });
    
    // add a pixel
    $('.empty-pixel').click(function(e) {
        $('.empty-pixel').removeClass('selected');
        $(this).addClass('selected');
        $('input[name="position"]').val($(this).attr('data-pos'));
        $("#addPixelForm").submit();
    });
    
    
    $('.bitmap').each(function() {
        if ($(this).find("img").attr("bitmap-url")) {
            $(this).find("img").attr("src", $(this).find("img").attr("bitmap-url"));
        }
    });
});