$("#footer a").each(function(){
    if($(this).hasClass("on")){
        var newClass = $(this).attr('data-class');

        $(this).find("use").attr('xlink:href','#'+newClass);
    }
});
