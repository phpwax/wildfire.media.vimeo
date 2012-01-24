jQuery(document).ready(function(){

  jQuery(window).bind("media.wildfirevimeofile.preview", function(e, row, preview_container){
    var str = "";

    row.find("td").each(function(){
      var html = jQuery(this).html();

      if(html.indexOf("<iframe") >= 0){
        var h = parseInt(jQuery(html).find("iframe").attr("height")),
            w = parseInt(jQuery(html).find("iframe").attr("width")),
            r = 200/w
            ;
        console.log(w);
        if(h && w) str += html.replace(h, Math.round(h*r)).replace(h, Math.round(h*r)).replace('"'+w+'"',200);
      }
      else str += html;
    });
    preview_container.html(str);

  });

});