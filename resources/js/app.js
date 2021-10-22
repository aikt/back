require('./bootstrap');

$("a").each(function(){
  if($(this).text() == "Selected rows"){
    var ids = "";
    $(".column-id").each(function(){
      if(!isNaN($(this).text().trim())){
        var number = $(this).text().trim();
        ids+=number+",";
      }
    });
    $(this).parent().after('<li><a href="'+location.origin+'/admin/api/exportnews?ids='+ids.slice(0,-1)+'" target="_blank">Exportar noticias</a></li>');
  }
});
