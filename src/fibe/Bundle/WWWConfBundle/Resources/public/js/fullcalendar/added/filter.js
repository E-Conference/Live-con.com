 
//filter must be a <select> inside a #filters
    
    function initFilter(){
      var self = this;
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
                    }); 
                });  
      }) 
    }