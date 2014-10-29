$(document).ready(function() {
    /** Responsive drawing **/
    function respondDrawing() {
        if ($("#drawing").length === 0) {
            console.log("No drawing");
            return;
        }
        
        var width = $("#drawing").width() - 42;
        var dWidth = $("#drawing").attr("drawing-width");
        var pWidth = Math.floor(width / dWidth);
        
        $("#drawing").find(".pixel").css("width", pWidth + "px").css("height", pWidth + "px");
        $("#drawing").find(".line:first .pixel").css("height", (pWidth + 20) + "px");
        $("#drawing").find(".line:last .pixel").css("height", (pWidth + 20) + "px");
        $("#drawing").find(".line").find(".pixel:first").css("width", (pWidth + 20) + "px");
        $("#drawing").find(".line").find(".pixel:last").css("width", (pWidth + 20) + "px");
    }
    $(window).resize(respondDrawing);
    respondDrawing();
    /** End responsibe drawing **/
    
    // select a color
    $(".color").click(function(e) {
        e.preventDefault();
        $(".color").removeClass("selected");
        $(this).addClass("selected");
        $("#drawing").find(".pixel").css("border-color", $(this).css("background-color"));
        $('input[name="color"]').val($(this).attr("data-id"));
    });
    
    // add a pixel
    $(".empty-pixel").click(function(e) {
        $(".empty-pixel").removeClass("selected");
        $(this).addClass("selected");
        $('input[name="position"]').val($(this).attr("data-pos"));
        $("#addPixelForm").submit();
    });
    
    // load bitmap
    $(".bitmap").each(function() {
        if ($(this).find("img").attr("bitmap-url")) {
            $(this).find("img").attr("src", $(this).find("img").attr("bitmap-url"));
        }
    });
    
    // set title
    $(".name-it-link").click(function(e) {
        $(this).hide();
        $(".name-it-form").removeClass("hidden").show();
    });
});