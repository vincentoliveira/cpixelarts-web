$(document).ready(function() {
    $('.bitmap').each(function(){
        $(this).find("img").attr("src", $(this).find("img").attr("bitmap-url"));
    });
});