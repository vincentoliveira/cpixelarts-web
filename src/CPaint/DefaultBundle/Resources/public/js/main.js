$(document).ready(function() {
    $('.bitmap').each(function(){
        if($(this).find("img").attr("bitmap-url")) {
            $(this).find("img").attr("src", $(this).find("img").attr("bitmap-url"));
        }
    });
});