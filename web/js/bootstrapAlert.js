var bootstrapAlertTimeout;
function bootstrapAlert(type,msg,title,icon){
    var $body = $("body");

    //get unique #bootstrap_alert
    var $bootstrapAlert = $('#bootstrap_alert').length < 1 ? $('<div id="bootstrap_alert"></div>').prependTo($body) : $('#bootstrap_alert');
    
    if(type== "stop"){
      clearTimeout(bootstrapAlertTimeout); 
      // $bootstrapAlert.animate({'opacity': 0 }, {queue: false, duration: 1500});
      $bootstrapAlert.stop().fadeOut(1500)
                     
      return;
    } 
    var config = {
        success:{
            title:"Success : ",
            icon:"<i class='fa fa-check'></i>"
        },
        info:{
            title:"Info : ",
            icon:"<i class='fa fa-info'></i>"
        },
        warning:{
            title:"Warning : ",
            icon:"<i class='fa fa-meh-o'></i>"
        },
        error:{
            title:"Error : ",
            icon:"<i class='fa fa-meh-o'></i>"
        }
    }
    //default values
    if(!config[type]){
        msg=type;
        type="info";
    }
    if(!icon && icon!=""){ 
        icon = config[type].icon
    } 

    if(!title && title!="" ){
        title = config[type].title;
    }

    $bootstrapAlert.attr("class","")
                   .addClass("alert alert-"+type) 
                   .html('<button type="button" class="close" data-dismiss="alert">&times;</button><strong> '+title+' </strong>'+msg)  
                   .prepend($(icon).addClass("fa-2x").css("margin-right","0.5em"));

    $bootstrapAlert.stop().hide().fadeIn('fast');
    clearTimeout(bootstrapAlertTimeout);
    bootstrapAlertTimeout=setTimeout(function(){
            // $bootstrapAlert.animate({'opacity': 0 }, {queue: false, duration: 1500});
            $bootstrapAlert.fadeOut(1500);
        },5000);
}