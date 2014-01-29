 
//filter must be a <select> inside a #filters
    
    function initFilter(endPointUrl){
      var self = this;
      var endPointUrl = endPointUrl;
      $('#filters').find("select").each(function(){ 
        $(this).select2("destroy").select2({
                    closeOnSelect : false,
                    formatSelectionCssClass : function(obj,ctn){ 
                      var elem = $(obj.element);
                      console.log(obj);
                      obj.css = {"background":elem.data("color")};
                    },
                })
                .change(function(){  
                    //update fullcalendar getEvent 's url 
                    $(this).each(function(){ 
                      $(self).trigger("change", [$(this).data("filter"),$(this).val()]);
                      op.data[$(this).data("filter")] = $(this).val(); 
                    });
  
                    $.get(
                          endPointUrl,
                          op.data,
                          function(events) {    
                            $(self).trigger("changed",[$(events).map(function(key,val){ return val.id;})]); 
                          },
                          'json'
                        ).error(function(jqXHR, textStatus, errorThrown) {
                          bootstrapAlert("warning","there was an error during the fetch of events",""); 
                    });
                });  
      }) 
    }