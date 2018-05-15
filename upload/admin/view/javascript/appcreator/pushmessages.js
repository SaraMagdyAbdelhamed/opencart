var CPURL = "http://robo-apps.com/";
var $ = jQuery.noConflict();
$(document).ready(function() {

  function filterVars(value) {
    value = value.replace(/\[/g, "\[");
    value = value.replace(/\]/g, "\]");
    value = value.replace(/\@/g, "\@");
    return value;
  }
  
  $("#Action_ID").change(function(){
    var action = $("#Action_ID").val();
    $(".actionSelect").hide();
    $(".action6").hide();
    $(".action2").hide();
    if(action == 6){
      $(".action6").show();
    }
    /*else if(action == 2){
      $(".action2").show();
    }*/
    else if(action == 24){
      updateSelectMenu(action);
      $(".actionSelect label").text("الملف");
      $(".actionSelect").show();
    }
    else if(action == 25){
      updateSelectMenu(action);
      $(".actionSelect label").text("الرابط");
      $(".actionSelect").show();
    }
    else if(action == 26){
      updateSelectMenu(action);
      $(".actionSelect label").text("المحتوى");
      $(".actionSelect").show();
    }
    else if(action == 28){
      updateSelectMenu(action);
      $(".actionSelect label").text("المحتوى");
      $(".actionSelect").show();
    }
    else if(action == 30){
      updateSelectMenu(action);
      $(".actionSelect label").text("المحتوى");
      $(".actionSelect").show();
    }
    else if(action == 29){
      updateSelectMenu(action);
      $(".actionSelect label").text("احداثيات lat, long");
      $(".actionSelect").show();
    }
    else if(action == 43){
      updateSelectMenu(action);
      $(".actionSelect label").text("المحتوى");
      $(".actionSelect").show();
    }
  });
  
  $(".action6 select").change(function(){
    var action = $(".action6 select").val();
    $(".actionSelect").hide();
    if(action == "viewone"){
      $(".actionSelect label").text("اختر احد المواد");
      updateSelectMenu(action);
    }
  });
  
  function updateSelectMenu(action){
    $(".actionSelect select").html("");
    $(".actionSelect").show();
    $.get(CPURL + "admin/apps/menu/load_select_data/", {"moduleid": $("#Module_ID").val(), "action": action, "no_header": 1}, function(data){
      $(".actionSelect select").html(data);
    });
  }
});