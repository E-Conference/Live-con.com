 
//filter must be a <select> inside a #filters
    
    function initFilter(endPointUrl){
      var self = this;
      var endPointUrl = endPointUrl;
      var currentFilter= {};
      this.getFilters = function(){ return currentFilter;}

      $('#filters').find("select").each(function(){
        $(this).select2("destroy").select2({
                    closeOnSelect : false,
                    formatSelectionCssClass : function(obj,ctn){ 
                      var elem = $(obj.element); 
                      obj.css = {"background":elem.data("color")};
                    }
        })
                .change(function(){  

                    $(this).each(function(){ 
                      $(self).trigger("change", [$(this).data("filter"),$(this).val(),$(this).data("res")]);
                      op.data[$(this).data("filter")] = $(this).val(); 
                      currentFilter[$(this).data("filter")] = $(this).val();
                    });
  
                    $.get(
                          endPointUrl,
                          op.data,
                          function(data) {    
                            $(self).trigger("changed",[data,EventCollection.getIds(data)]); 
                          },
                          'json'
                        ).error(function(jqXHR, textStatus, errorThrown) {
                          bootstrapAlert("warning","there was an error during the fetch of events",""); 
                    });
                });  
      }) 
    }