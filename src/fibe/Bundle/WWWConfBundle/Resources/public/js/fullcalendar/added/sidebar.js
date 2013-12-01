

function Sidebar(){

        var self = this;
        this.populate = populate; 
        this.setInstantEvents = setInstantEvents; 
        this.setSidebarEvent = setSidebarEvent; 
 
        var eventHtml =  "<div class='external-event fc-event fc-event-draggable'></div>";
        var $sidebarTmp = $(eventHtml);

        function populate(url){ 
        $.get(
          url,
          null,
          function(events) {  
              var instant_events = [];
              for(var i=0;i<events.length;i++){

                var event = new CalEvent(events[i]); 
                instant_events.push(Events[events[i]["id"]]); 
              }
              self.setInstantEvents(instant_events); 

              // if(stopRender===true)return;
              bootstrapAlert("success",events.length+" dateless events have been well fetched" ); 
              //$calendar.fullCalendar('renderEvent',e  ); // 3rd arg make the event "stick" 
          },
          'json'
        ).error(function(jqXHR, textStatus, errorThrown) {
          bootstrapAlert("warning","there was an error during the fetch of events",""); 
        });
        }
        function setInstantEvents(instant_events){
          $sidebar.html("");  
          $sidebarTmp.prependTo($sidebar).hide(); 
          //set sidebar bubble draggable
          for (var i in instant_events){ 
            // if(!isInstant(instant_events[i])) continue;
            var $event = $(eventHtml);
            $event = sidebarDraggable($event,instant_events[i]);
          }
// external-event fc-event               fc-event-draggable                                          ui-draggable                     
//                fc-event fc-event-vert fc-event-draggable fc-event-start fc-event-end ui-droppable ui-draggable ui-resizable
          //set sidebar droppable
          $sidebar.droppable({
            accept: ".ui-resizable" ,
            over: function( event, ui ) {
                if( $(ui.draggable).hasClass("fc-event")) {
                    var event = Events[dragged[1].id];
                    if(!event)return;
                    // console.log("dropped into sidebar",event);
                    $(this).css("border-color","green"); 
                    sidebarEventHtml($sidebarTmp,event);
                    $sidebarTmp.show() ;
                }
            },
            out: function( event, ui ) {
                if($(ui.draggable).hasClass("fc-event")) {
                    $(this).css("border","solid 1px #ccc"); 
                    $sidebarTmp.hide();
                } 
            },
            drop: function( event, ui ) { 
              if ( $(ui.draggable).hasClass("fc-event")){
                //update ui
                $sidebarTmp.hide();
                $(this).css("border","solid 1px #ccc"); 

                // console.log("end Drag to sidebar");

                var event = Events[dragged[1].id] ;
                    if(!event)return;
                $(self).trigger("dropped",[event]);
              }
            }
          });
        }

        function sidebarDraggable($event,event){
          $event.appendTo($sidebar);
          // console.log(event);
          sidebarEventHtml ($event,event);
          $event.prepend("<span class='fc-event-id ' style='color:grey;'>"+event.id+"</span>");
          //set child drag 
          // delete event['elem'];
          // console.log(event);
          $event.draggable({
                    zIndex: 999,
                    revert: true,      // will cause the event to go back to its
                    appendTo: 'body',
                    containment: 'window', 
                    helper: 'clone',
                    revertDuration: 0,  //  original position after the drag
                    start : function (ev,ui){
                          $(this).hide();   
                          dragged = [ ui.helper[0], event ];
                          setTimeout(function(){ //bug... event isn't yet updated  
                            $(self).trigger("drag",[event]); 
                          },1);//bug... event isn't yet updated   
                    },
                    stop: function(){
                        $(this).show()
                    } 
                  }) 

          // store the Event Object in the DOM element so we can get to it later
          return $event;
        }

        
        function setSidebarEvent(event){
          var $event = $(eventHtml);
          var $event = sidebarDraggable($event,event);
        }

        function sidebarEventHtml ($event,event){
          $event.html(event.title);
          $event.css({
                    "background-color": event.color,
                    "border-color": event.border_color || event.color
                 });  
        }

}