$(document).ready(function() {
    /** Responsive drawing **/
    function respondDrawing() {
        if ($("#drawing").length === 0) {
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

    // get selected color
    function getSelectedColor()
    {
        return $('.color.selected').css("background-color");
    }

    // select a color
    $(".color").click(function(e) {
        e.preventDefault();
        $(".color").removeClass("selected");
        $(this).addClass("selected");
        $("#drawing").find(".pixel").css("border-color", getSelectedColor());
        $('input[name="color"]').val($(this).attr("data-id"));

        // if selected color is 151 => set background to white
        if ($(this).attr("data-id") === "151") {
            $('body').css('background-color', '#ffffff');
            $('h1,h1 small,a').css('color', '#92b6ff');
        } else {
            $('body').css('background-color', '#92b6ff');
            $('h1,h1 small,a').css('color', '#ffffff');
        }
    });
    
    // empty pixel on hover
    $(".empty-pixel").hover(function(){
        if ($(this).hasClass("empty-pixel")) {
            $(this).css('background', getSelectedColor());
        }
    },function(){
        if ($(this).hasClass("empty-pixel")) {
            $(".empty-pixel").css('background', 'none');
        }
    });
    
    // if selected color is 42 => set background to white
    if ($('.color.selected').attr("data-id") === "151") {
        $('body').css('background-color', '#ffffff');
        $('h1,h1 small,a').css('color', '#92b6ff');
    }

    // add a pixel
    var isMouseDown = false;
    var positions = [];
    $(".empty-pixel").mousedown(function() {
        isMouseDown = true;
        positions = [$(this).attr("data-pos")];
        $(this).removeClass("empty-pixel").css('background', getSelectedColor());
    });
    $(".empty-pixel").mouseup(function(e) {
        e.preventDefault();
        
        isMouseDown = false;
        if (positions.length <= 0) {
            return;
        }
        
        // add hidden input form
        $(".position-input").remove();
        for (i = 0; i < positions.length; i++) {
            $('<input>').attr({
                type: 'hidden',
                name: 'position[' + i + ']',
                class: 'position-input',
                value: positions[i],
            }).appendTo('#addPixelForm');
        }

        $(this).removeClass("empty-pixel").css('background', getSelectedColor());
        if ($("#addPixelForm").attr("reload") == "true") {
            $("#addPixelForm").submit();
        } else {
            var datas = $("#addPixelForm").serialize();
            $.ajax({
                type: $("#addPixelForm").attr('method'),
                url: $("#addPixelForm").attr('action'),
                data: datas,
                success: function(response) {
                    $(".empty-pixel:hover").css('background-color', $(this).css("background-color"));
                },
                error: function() {
                    alert("An error has occured");
                    location.reload();
                }
            });
        }

        positions = [];
    });
    $(document).mouseup(function() {
        isMouseDown = false;
        for (i = 0; i < positions.length; i++) {
            $(".pixel[data-pos=" + positions[i] + "]").addClass("empty-pixel").css('background', "");
        }
        
        positions = [];
    });
    $(".empty-pixel").mouseover(function(e) {
        if (isMouseDown) {
            positions.push($(this).attr("data-pos"));
            $(this).removeClass("empty-pixel").css('background', getSelectedColor());
        }
    });

    // set title
    $(".name-it-link").click(function(e) {
        $(this).hide();
        $(".name-it-form").removeClass("hidden").show();
    });

    // async load bitmap
    $(".drawing").each(function() {
        if ($(this).find("img").attr("image-url")) {
            $(this).find("img").height();
            $(this).find("img").attr("src", $(this).find("img").attr("image-url"));
        }
    });
});