
 

var EventCollection = { 
 

    forceMainConfRendering : true,
    broCountRange:{}, 
    eventsToComputeBroCountRangeIndexes:[],  

    isLoginPage : function (html){
      if(Object.prototype.toString.call(html) == '[object String]' && html.substring(0,9)=="<!DOCTYPE"){
        alert("Session expired :(\n\n\t refresh the page to reconnect!")
        bootstrapAlert("warning","you must reconnect to continue!")
          
        return true;
      }
      return false;
      
    },

    refetchEvents : function(refetch,force){ 
        if(force!==true && refetch!==true && (EventCollection.forceMainConfRendering!==true && EventCollection.eventsToComputeBroCountRangeIndexes.length==0)){ 
          console.log("not rendered")
          return; 
        }  
        EventCollection.forceMainConfRendering = false;
        // function doWork() {
        

          // mainConfEvent.renderForRefetch(); 
          
          updateBroCountRange();  
          stopRender = false;
          fetched = !refetch;
          $calendar.fullCalendar('refetchEvents');   
        // }
        // setTimeout(doWork, 1);

        function updateBroCountRange(doChildren){ 
            //if there's no EventCollection.eventToRender, calculate for every events
            var done     = []
                ,brothersIds= [] 
                ,minLeft 
                ,bro
                ,curBro
                ,baseCount; 
  
              brothersIds = EventCollection.eventsToComputeBroCountRangeIndexes;   
            
            // console.log("----------------------------------------------------");
            console.log(brothersIds.length+" affected :",brothersIds); 
            // console.log("non affected : ",EventCollection.broCountRange);
            // console.log("----------------------------------------------------");
            
            var startScript = moment();
            computeCountRange(brothersIds,doChildren);
            console.debug("BroCountRange : updated "+brothersIds.length+" events in "+moment().diff(startScript)+" ms",EventCollection.broCountRange);

            EventCollection.eventsToComputeBroCountRangeIndexes = []; 
            return EventCollection.eventsToComputeBroCountRangeIndexes;
            
            function computeCountRange(brosIds,doChildren){
              
              var brosIdsofcurBro,
                  curBroResId,
                  sameRes,
                  remainingIds = brosIds.slice(0); //array copy 
              for (var i in brosIds){
                curBro = Events[brosIds[i]];  
                if(curBro.allDay)continue;

                brosIdsofcurBro = curBro.getNonAllDayBrosId(); 
                curBroResId = curBro.resourceId || curBro.resource.id;

                baseCount = EventCollection.broCountRange[curBro.id].count;
                baseResCount = EventCollection.broCountRange[curBro.id].resCount;
                // console.debug(curBro.id +" has "+brosIdsofcurBro.length+" non all day bros")
                
                for (var j in remainingIds){

                  bro = Events[ remainingIds[j] ];  
                  
                  if(curBro.id===bro.id || bro.allDay )continue;   //ensure the bro is not itself or an all day event
                  
                  if(curBro.isOutOf(bro,true) || ($.inArray(bro.id, brosIdsofcurBro) === -1))continue;    //ensure the bro is a real bro  
                  EventCollection.broCountRange[curBro.id]["count"]++;  //increments self count  
                  EventCollection.broCountRange[curBro.id]["resCount"]++;  //increments self count  

                  var baseBroResCount = EventCollection.broCountRange[bro.id]["resCount"];
                  var baseBroResRange = EventCollection.broCountRange[bro.id]["resRange"];
                  //increments bro count and range
                  EventCollection.broCountRange[bro.id] = {
                    count   :baseCount+1,
                    range   :EventCollection.broCountRange[curBro.id]["range"]+1, 
                    resCount:baseResCount+1,
                    resRange:EventCollection.broCountRange[curBro.id]["resRange"]+1
                  };

                  //resource view : check if bros have the same resource 
                  if(curBroResId != (bro.resourceId || bro.resource.id)){
                    console.debug("decrementing "+curBro.id+" and "+bro.id)

                    //decrements bro resCount and resRange
                    EventCollection.broCountRange[bro.id]["resCount"] = baseBroResCount;
                    EventCollection.broCountRange[bro.id]["resRange"] = baseBroResRange;

                    EventCollection.broCountRange[curBro.id]["resCount"]--;
                  }
                }
                delete remainingIds[i];
              } 
            } 
        }
    },
 


    /*-----------------------------------------------------------------------------------------------------*/
    /*------------------------------------- get/find functions --------------------------------------------*/
    /*-----------------------------------------------------------------------------------------------------*/

    /** 
     * @param int id    : event id
     * @param obj op    : noSidebar (default false)
     * 
     * return children = [{event:event,element:$element}, ... ]
     *         events   : db model events
     *         elements : jquery draggable div array;
     */
    find : function (id,op){ 
          if(id==="" || !id)return ;
          if(!op)op={}; 
          var event = Events[id];
          if(!Events[id] || (op.noSidebar ===true && Events[id].isInstant()) || (op.noAllDay === true && Events[id].allDay ))return; 
          return event; 
    }, 
    /** 
     * @param parent        :  event
     * @param op            : concat : ( boolean default : false ) if true : dont preserve the tree nature of the relation (just concat children/subchildren/subsu...)
     *                        noSidebar(default false),
     *                        noAllDay(default false),
     *                        recursive(default true) get only direct children
     * return children      : [{event:event,element:$element}, ... ]
     * events               : db modele events
     * elements             : jquery draggable div array;
     */
    getChildren : function(parent,op){
          // console.log("getChildren("+parent.id+")",op);
          var children = [];
          if(!op)op={};

          // console.log("getChildren",parent)
          $.each(parent.children,function(i,childId){
            if( !parent.children.hasOwnProperty( i )  ) return;
            if( childId === parent.id)parent.deleteParent();
            var child = EventCollection.find(childId.id,op);
            if( !child) return;
            // console.log("child",child);

            if(op.recursive!==false){
              var subChildren  = EventCollection.getChildren(child,op);  

              if (subChildren && subChildren.length > 0){
                if(op.concat===true)
                  children = children.concat(subChildren);
                else{
                  child['subChildren'] = subChildren;
                } 
              }
            }
            children.push(child );   

          }); 
          return children; 
    },

    updateMainConfEvent : function(newStart,newEnd){
    if(moment(mainConfEvent.start).dayOfYear() !== moment(newStart).dayOfYear() ||
       moment(mainConfEvent.end).dayOfYear() !== moment(newEnd).dayOfYear()){  
         console.debug("mainConfEvent changed, rendering...");  
         stopRender = true;
         mainConfEvent.start = moment(newStart, "YYYY-MM-DD HH:mmZ").format();
         mainConfEvent.end = moment(newEnd, "YYYY-MM-DD HH:mmZ").format(); 

         bootstrapAlert("success","conference event "+mainConfEvent.title+" have been updated") 
         mainConfEvent.renderForRefetch();  
         firstDay = moment(mainConfEvent.start);
         EventCollection.forceMainConfRendering = true;
      }
    },

    resetEvents : function (){ 
      Events = {};
      EventCollection.eventToRender = undefined;
      EventCollection.refetchEvents(true,true);
    }, 


    getToppestParent : function (){ 
        return EventCollection.getChildren(mainConfEvent, {recursive:false,onlyEvent:true,noSidebar:true});  
    },
 
    /**
    * @param id : event i
    * get div with with class fc-event and with a hidden div containing event id
    * <div class='fc-event-id hide'>event.id</div>
    */ 

    getDivById : function(id){
        // console.log(id,Events[parseInt(id)])
        return Events[parseInt(id)]['elem'];
    },

    getEventByDiv : function(div){
        var id = div.data("id")
        // console.log()
        return Events[id];
    },

    getIds : function(events){
      return $(events).map(function(key,val){ return val.id;})
    }, 

    /**
     *  add UI (popover, border color etc...)
     *  just after to the fullcalendar "all event render" function
     */ 
    stylizeBlocks : function(){ 
        var popoverWidth = 276;
        var dragOverEvents = []; 
        var currentDragOverEvent = null; 
            /*****************  styling (opacity, hover, drag, drop) ********************/

        var calendarEventsIds = EventCollection.getIds($calendar.fullCalendar('clientEvents'));
        for (var i in Events){
          var event = Events[i]; 

          var element = event.elem; 
          if(!element)  continue; //event is in another view
          $(element).each(function(i,element){
            element = $(element)
            //action on hovered by another dragged event  
            element.data("border-color",element.css("border-color"))
                   // .data("background-color",element.css("background-color"))
                   .data("prop",getProp(element)); 

            
            if($.inArray(event.id, calendarEventsIds) !== -1 ){ //stylize only event in the calendar

              /*************** popover *****************/
              element.popover({
                  trigger : 'hover',
                  html : true,
                  placement : function( context,source){
                    var popoverProp = getProp($(context));
                    var eventProp = getProp(source);
                    var calendarProp = getProp($calendar);
                    // console.log(popoverProp,eventProp,calendarProp)
                    if(eventProp.x + eventProp.w + popoverWidth < calendarProp.x + calendarProp.w )
                      return "right";
                    if(eventProp.x - popoverWidth > calendarProp.x)
                      return "left";
                    return "bottom";
                  },
                  title : ' <b><span class="muted">#'+event.id+'</span> '+event.title+'</b>',
                  content : event.getPopoverContent()
              });

              if (authorized) {

                //the main conf event isnt resizable
                if(event.id==mainConfEvent.id){
                  element.find(".ui-resizable-handle").remove();
                }
                //droppable = set as child
                element.droppable({ 
                  tolerance: "pointer" ,
                  over: function( ev, ui ) {
                    if ( $(ui.draggable).hasClass("fc-event") ){ 
                        var event = EventCollection.getEventByDiv($(this));
                        var draggedEvent = dragged[1];

                        //check if it's going to do a loop in the tree
                        if(event.isChild(draggedEvent)){
                          return;
                        }

                        if(currentDragOverEvent)currentDragOverEvent.elem.removeClass("drag-over-events");

                        currentDragOverEvent = {id:event.id,elem:$(this)};  
                        dragOverEvents.push(currentDragOverEvent);
                        if(draggedEvent.parent.id !== event.id) currentDragOverEvent.elem.addClass("drag-over-events")  
                        
                    }
                  },
                  out: function( ev, ui ) { 
                    if ( $(ui.draggable).hasClass("fc-event") ){
                        // $(this).animate({"background-color":$(this).data("background-color")},{queue:false});
                        $(this).removeClass("drag-over-events")
                        var event = EventCollection.getEventByDiv($(this));

                        for(var i in dragOverEvents){
                          if(dragOverEvents[i].id == event.id){
                            dragOverEvents.splice(i,1); 
                          }
                        }
                        if(dragOverEvents.length>0){
                          currentDragOverEvent = dragOverEvents[dragOverEvents.length-1]; 

                          // if(dragged[1].parent.id !== event.id)
                            currentDragOverEvent.elem.addClass("drag-over-events");
                        }
                    }
                  },
                  drop: function( ev, ui ) { 
                    if ( $(ui.draggable).hasClass("fc-event") &&  currentDragOverEvent){  
                    
                      var event = Events[currentDragOverEvent.id];
                      if(currentDragOverEvent.id === event.id){
                        currentDragOverEvent.elem.removeClass("drag-over-events");
                        dragOverEvents = []; 
                        currentDragOverEvent = null;
                      } else {
                        return;
                      }

                      var draggedEvent = dragged[1]; 
                      // check if it's not already a child
                      if(draggedEvent.parent.id === event.id){
                        return;
                      }  
                      //check if it's going to do a loop in the tree
                      if(event.isChild(draggedEvent)){ 
                        return;
                      }  
                      console.log(event)
                      console.log(draggedEvent)

                      // if(event.isOutOf(draggedEvent))return;  
                      
                      setTimeout(function(){   

                        $modalSetParent.modal('show').find(".sub-event").text(draggedEvent.title);
                        $modalSetParent.find(".super-event").text(event.title);

                        $modalSetParent.find('button.yes').off("click").click(function(){ 
                          //set event as parent of draggedEvent and children relation
                          console.log(draggedEvent+" is now the child of "+event.id)
                          
                          draggedEvent.computeCountRange({allBrosInDay:true});
                          draggedEvent.setParent(event);
                          draggedEvent.updateParentDate(); 
                          
                          draggedEvent.renderForRefetch();
                          draggedEvent.computeCountRange({allBrosInDay:true});

                          event.renderForRefetch();
                          event.computeCountRange({allBrosInDay:true});
                          EventCollection.refetchEvents();
                           
                          draggedEvent.persist(); 
                        });    
                      },0);  
                    }
                  }
                }); 
              }//end if authorized
          }//end stylize only event in the calendar

          /*************** hover : change border color and fade children *****************/
          element.hover(function(){
                //enter
                $(this).animate({"border-color":"#3F3F3F"},{queue:false});

                var elemEvent = EventCollection.getEventByDiv($(this));
                var childrenDiv = EventCollection.getChildren(elemEvent,{concat:true,onlyEvent:true});
                for (var j in childrenDiv){
                  var curChildDiv = childrenDiv[j].elem;
                  if(!curChildDiv || childrenDiv[j].hide)continue;
                  curChildDiv.animate({opacity:0.3},{duration:'fast',queue:false});
                }
            },function(){
                $(this).animate({"border-color":$(this).data("border-color")},{queue:false})
                var elemEvent = EventCollection.getEventByDiv($(this));
                var childrenDiv = EventCollection.getChildren(elemEvent,{concat:true,onlyEvent:true});
                for (var j in childrenDiv){
                  var curChildDiv = childrenDiv[j].elem;
                  if(!curChildDiv || childrenDiv[j].hide)continue;
                  curChildDiv.animate({opacity:1},{duration:'fast',queue:false})
                }
          });    
        })
      }
    },

    /*************************************************************************************************************** 
     ************************************** Fullcalendar callback functions ****************************************
     ***************************************************************************************************************/

    initing : true, 
    events : function(start, end, callback) { //fetch events
        
        if(fetched === true ){
          //events have already been fetched
          fetched = false;
          stopRender = false;  
          // console.log("fetched",calendar_events_indexes) 
          // console.log(calendar_events)
          console.log("########fullcalendar rendering "+calendar_events.length+" events")

          logtime = moment();
          callback(calendar_events ); 
          console.debug(moment().diff(logtime)+" for fullcalendar to render"); 
          logtime = moment();
          return;
        } 
        //compute dates to filter
        if(EventCollection.initing){
          op.data['before']=moment(firstDay).endOf('week').add("days",firstWeekDay).format();
          op.data['after']=moment(firstDay).startOf('week').add("days",firstWeekDay-1).format();
          EventCollection.initing = false;
        }else{
          op.data['before']=moment(end).format();
          op.data['after']=moment(start).format();
        }
          console.log("########fetching")
        
        $.get(
          op.getOrderedUrl,
          op.data,
          function(events) {  
              // if(stopRender===true)return;
              if(EventCollection.isLoginPage(events))return;
              
              console.log(events) 
              if(events.length!=0)bootstrapAlert("success",events.length+" events have been well fetched" );
              else {bootstrapAlert("info","no event found");}

              logtime = moment(); 

              //initialize events
              calendar_events   = []; 
              calendar_events_indexes   = {};

              //non-blocking loop over events[i]
              var i = 0;
              if(events[i])setTimeout(doWork, 1);
              function doWork() {
                
                var known = !!Events[events[i].id];
                var event = new CalEvent(events[i]);
                if(!known)event.computeCountRange();

                if(event.mainconfevent){
                  mainConfEvent = event; 
                }   

                i++; 

                //last iteration  
                if (!events[i]) { 
                  fetched = true;  
                  console.debug(moment().diff(logtime)+" to init"); 
                  EventCollection.refetchEvents(false,true);
                }else if (stopRender!==true){ 
                  //the loop goes on
                  setTimeout(doWork, 1);
                }
              };  
          },
          'json'
        ).error(function(jqXHR, textStatus, errorThrown) {
          bootstrapAlert("warning","there was an error during the fetch of events",""); 
        });
        bootstrapAlert("info","event request sent","","<i class='fa-2x fa fa-spinner fa-spin'></i>");
    },

    eventAfterAllRender : function( ) { 
        //avoid repeating this function 10 times... 
        if(!mainConfEvent || stopRender)return;

        // setTimeout(function(){    
          logtime = moment() 
          EventCollection.stylizeBlocks();
          console.debug(moment().diff(logtime)+" to stylizeBlocks"); 
          console.log( "######################################################"); 
        // },0);
             
      if($calendar.fullCalendar('getView').name == "resourceDay" && mainConfEvent.hasChild() ) 
        $(mainConfEvent.elem).hide();
    },

    eventAfterRender : function( event, element, view ) { //each event
      event = Events[event.id];
      //if the event is longer than a day, multiple divs can represent one event
      if(event["elem"] && event["elem"].is(":visible"))
        event["elem"].push(element)
      else
        event["elem"] = element; 

      // add id in the dom 
      $(element).data("id",event.id)

      //add class to the mainConfEvent
      if(event.id == mainConfEvent.id)return $(element).addClass("main-conf-event");

      // hide filtered events
      if(event.hide === true) 
        $(element).hide();
      
      //set z-index calculated in calculateWidth
      if(!event.allDay)
        $(element).css("z-index",EventCollection.broCountRange[event.id].zindex)
      
      // hide events that aren't a leaf in the hierarchy in resource mode 
      if($calendar.fullCalendar('getView').name == "resourceDay" && event.hasChild() ) 
        $(element).hide();
    },
    eventCalculateWidth : function(event, seg, leftmost, availWidth, outerWidth, levelI, bottom, top, forward, dis,rtl) {  
      if(event.allDay){
        return;
      }
      event.calculateWidth(seg, leftmost, availWidth, outerWidth, levelI, bottom, top, forward, dis,rtl); 
    },
    eventClick : function(calEvent, jsEvent, view) {  // get the full edit form
      $.ajax({
          url: op.updateUrl+"?id="+calEvent.id,  
          success: function(doc,b,c) { 
            if(EventCollection.isLoginPage(doc))return;
              $modalBody.html(doc);
              bootstrapAlert("success","Options for event : <b>"+calEvent['title']+"</b> has been well fetched"); 

              $modal.off('shown.bs.modal').on('shown.bs.modal', function () {
                  $modal.off('hidden.bs.modal');

                    // rerender if changed
                  var rerender = function(){    
                    $modal.on('hidden.bs.modal', function () {
                      setTimeout(function(){EventCollection.resetEvents()},10); 
                    })
                  }

                  //refetch if changed
                  $modalBody.find("form").each(function(){
                    $(this).submit(function(){
                      rerender();
                    })
                  }) 
                  //refetch if changed
                  $modalBody.find("a").click(function(){
                      rerender();
                  }) 
                  // close and refetch event 
                  $modalBody.find("#eventForm").submit(function(){ 
                      $modal.modal("hide"); 
                  }) 
                  $modalBody.find("#delete-event-form").submit(function(){
                      calEvent.removeForRefetch(); 
                      $modal.modal("hide"); 
                  }) 
              }) 
              $modal.modal("show");
          }
      }); 
      bootstrapAlert("info","edit <b>"+(calEvent['title'] || calEvent['name'])+"</b> request sent","Info : ","<i class='fa-2x fa fa-spinner fa-spin'></i>");  
    },
    eventCreate : function(start, end, allDay,ev,resourceObj) { //new event
      $modalNewEvent.off('shown.bs.modal').on('shown.bs.modal', function() {
                        $(this).find("#name").val("").focus();
                    })
                    .modal("show"); 
      $modalNewEvent.find("form").off("submit").submit(function(e){
        var title = $(this).find("#name").val();
        if (title) {
            var tmp = {
                title    : title,
                parent   : {id:mainConfEvent.id},
                children : [],
                start    : start,
                end      : moment(start).isSame(moment(end)) ? moment(start).add("hours",1).format() : end,
                allDay   : allDay
            };
 
            if( resourceObj){  
              tmp['currentRes'] = currentRes;
              tmp['resourceId'] = resourceObj.id;
            } 

            $.post(
                op.quickAddUrl,
                $.extend( {} , tmp ),
                function(response) {  
                    if(EventCollection.isLoginPage(response))return; 
                    bootstrapAlert("success","event <b>"+tmp['title']+"</b> has been well added");
                    tmp.id =response.id;
                    var ev = new CalEvent(tmp);    
                    ev.setParent(mainConfEvent)  
                    ev.renderForRefetch(); 
                    ev.computeCountRange({allBrosInDay:true});  
                    if(response.mainConfEvent){
                      EventCollection.updateMainConfEvent(response.mainConfEvent.start,response.mainConfEvent.end); 
                    }  
                    EventCollection.refetchEvents(); 
                },
                'json'
            ).fail(function(a,b,c) { 
              bootstrapAlert("warning","Could not have been able to add the event.",c+" : "); 
            });
            bootstrapAlert("info","add request sent","Info : ","<i class='fa-2x fa fa-spinner fa-spin'></i>");
            $modalNewEvent.modal("hide");
        }else{
            bootstrapAlert("info","You must give a name to the event");
            $modalNewEvent.find("#name").focus();
        }
        return false;
      });
      // $calendar.fullCalendar('unselect');
      
    },
    eventResize : function(event,dayDelta,minuteDelta,revertFunc){ 

      if(!event["end"] && event.allDay){ //event now ends before it starts :S
          //fit the end date thanks to children
          // event["end"] = moment(event["start"]).add("hours",1).format();
           revertFunc();
      }
      var oldEnd = moment(event["end"]).subtract({'d':dayDelta,'m':minuteDelta});

      console.log(dayDelta,minuteDelta,event);
      // if(!event.isOneDayLong() && !event.allDay){ 
      //   // event.allDay = true;
      //   event.fitToDay( event["start"],oldEnd.format());  
      // }

      event.updateParentDate();
      event.updateChildrenDate(); 

      event.computeCountRange({allBrosInDay:true}); 
      event.renderForRefetch();

      EventCollection.refetchEvents();
      event.persist();  
    },
    eventSidebarDrop : function(date, allDay, ev, ui, resource) { //drop from SIDEBAR

      // retrieve the dropped element's stored Event Object 
      var event = dragged[1]; 
      // var event = $.extend({},dragged[1]);  
      if(event.elem)event.elem.remove();
      // delete event.elem;
      event.allDay = allDay;
      event['start'] = date;
      event['resourceId'] = resource.id || "0";


      // event = new CalEvent(event); 
      console.log("dropped from sidebar", event);
      // assign it the date that was reported
      
      // e['end'] = moment(date).add("hours",1).format();

      event.SetRecurDate();
      // event.deleteParent();
      Events[event.id] = event;

      event.setParent(mainConfEvent);
      event.updateParentDate(); 
      event.computeCountRange({allBrosInDay:true});
      event.renderForRefetch();
      EventCollection.refetchEvents();
      event.persist();
      // render the event on the calendar  
    },
    eventDragStart : function( event, jsEvent, ui, view ) { 
      dragged = [ ui.helper[0], event ];
      setTimeout(function(){   
        event.dragChildren(jsEvent, ui, view);
      },1);
    },
    eventDragStop : function( event, jsEvent, ui, view ) {  
      // var parent = EventCollection.find(event.parent.id,{noSidebar:true});  
      var event = Events[event.id]
      if(mainConfEvent.id==event.id && !event.allDay)return;
      var parent = EventCollection.find(event.parent.id,{noSidebar:true}); 
      var children = EventCollection.getChildren(event,{concat:true,onlyEvent:true}); 
      
      //compute old day too 
      //TODO put this in dragstart 
      event.computeCountRange({allBrosInDay:true});  
      //TODO : parent  has here a wrong start/end updated from somewhere and need to be computed back :S 
      //TODO : parent  has here a wrong start/end updated from somewhere and need to be computed back :S  
      dragged = [ ui.helper[0], 
                  event, 
                  {start:moment(event.start), end:moment(event.end)},
                  (parent?{start:moment(parent.start),end:moment(parent.end)}:{}),
                  $(children).map(function(){return {start:moment(this.start),end:moment(this.end)};})
                ];
    },
    eventDrop : function( event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view ) {
      if(mainConfEvent.id==event.id && !event.allDay)return revertFunc();
      // event.computeCountRange({allEventsInDay:true}); 
      //TODO : parent  has here a wrong start/end updated from somewhere and need to be computed back :S 
      //TODO : parent  has here a wrong start/end updated from somewhere and need to be computed back :S 

      var event = dragged[1];
      var diff = moment(dragged[2].start).diff(moment(event['start']));

      var parent = EventCollection.find(event.parent.id,{noSidebar:true});

      stopRender = true;
      // if(!event.isOneDayLong())event["allDay"]

      //apply to children   
      var childrenDates = dragged[4]; 
      var children = EventCollection.getChildren(event,{concat:true,onlyEvent:true});
      $.each(children,function(i,child){ 
          child['start'] = moment(childrenDates[i]['start']).subtract(diff);
          child['end']   = moment(childrenDates[i]['end']).subtract(diff);

          child.computeCountRange();
          child.renderForRefetch();
          child.formatDate();
          child.persist();
      });   

      //apply to parent 
      if(parent){
        var parentDate = dragged[3];
        parent.start = parentDate.start.format(); 
        parent.end = parentDate.end.format(); 
        if(event.isOutOf(parent,true)){
          //event dropped out of parent
          console.log(" #### moved out ####");  
          event.setParent(mainConfEvent); 
        } 
        event.updateParentDate();  
      } 
      event.computeCountRange({allBrosInDay:true});

      EventCollection.refetchEvents(false,true); 
      event.persist(); 
    }
}

