
 

var EventCollection = { 
 
 


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
         * @param parent        :  db model event
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
    forceMainConfRendering : true,
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

    broCountRange:{},
    eventsToComputeBroCountRange:[],
    eventsToComputeBroCountRangeIndexes:[],
    refetchEvents : function(refetch,force){
        // alert(EventCollection.eventsToComputeBroCountRange.length==0)
        // alert(EventCollection.forceMainConfRendering!==true)
        if(force!==true && (EventCollection.forceMainConfRendering!==true && EventCollection.eventsToComputeBroCountRange.length==0)){ 
          console.log("not rendered")
          return; 
        } 
        // alert("ok")
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
            var startScript = moment();
            //if there's no EventCollection.eventToRender, calculate for every events
            var done     = []
                ,brothers= [] 
                ,minLeft 
                ,bro
                ,curBro
                ,baseCount; 
  
              brothers = EventCollection.eventsToComputeBroCountRange;   
            
            // console.log("----------------------------------------------------");
            console.log("affected = ",brothers); 
            // console.log("non affected : ",EventCollection.broCountRange);
            // console.log("----------------------------------------------------");
            computeCountRange(brothers,doChildren);
            EventCollection.eventsToComputeBroCountRangeIndexes = [];
            EventCollection.eventsToComputeBroCountRange = [];
            console.debug("BroCountRange : updated "+brothers.length+" events in "+moment().diff(startScript)+" ms");
            // console.log(EventCollection.broCountRange) 
            // return EventCollection.broCountRange;
            
            function computeCountRange(bros,doChildren){
              //copy array
              var remaining = bros.slice(0);
              for (var i in bros){
                curBro = bros[i];  
                // console.log("curBro",curBro.id)
                //create rtn object for curBro  
                baseCount = EventCollection.broCountRange[curBro.id].count;

                for (var j in remaining){
                  bro = remaining[j];  
                  //ensure the bro is not itself  
                  if(curBro.id===bro.id )continue; 
                  //ensure the bro is a real bro
                  if(curBro.isOutOf(bro,true) || !curBro.isBroOf(bro))continue;  
                  // console.log("curBro ",curBro.id," discovering ",bro.id)  
                  //update self properties 
                  EventCollection.broCountRange[curBro.id]["count"]++; 

                  //register bro as bro of curBro
                  EventCollection.broCountRange[bro.id] = {
                    count:baseCount+1,
                    range:EventCollection.broCountRange[curBro.id]["range"]+1
                  };  
                }
                delete remaining[i]
                // done.push(curBro.id); 
                
                // // doChildren
                // if(doChildren){ 
                //   computeCountRange(curBro.getBros(),true)
                // }
              } 
            } 
        }
    },
    /**
     * add events to EventCollection.eventsToComputeBroCountRange
     * @param CalEvent event to add
     * @param Object   opt   :
     *                  allEventsInDay: add all brothers too 
     */
    addEventToComputeCountRange : function(event,opt){
        if(event.is_mainconfevent)return;
        if(!opt)opt={}
        if(opt.allEventsInDay || opt.allBrosInDay){
          console.log("#ComputeCountRange all day "+event.id);
          var bros = calendar_events;
          // var bros = event.getBros();
          var dayToRender = {
            start:moment(event.start).startOf('day')
            ,end:moment(event.end).endOf('day')
          };
          for(var i in bros){
            var bro = bros[i]; 
            if(!bro.isOutOf(dayToRender) || !bro.isOutOf(dayToRender) ){
              if(opt.allBrosInDay !== true || event.isBroOf(bro) ){
                addEvent(bro);
              }
            }
          }
        }else{
          addEvent(event);
        }

        function addEvent(e){

          if(!e.id || $.inArray(e.id, EventCollection.eventsToComputeBroCountRangeIndexes) === -1) { 
            EventCollection.eventsToComputeBroCountRangeIndexes.push(e.id);
            EventCollection.eventsToComputeBroCountRange.push(e);
            EventCollection.broCountRange[e.id] = {count:1,range:0};
            // console.debug("#ComputeCountRange "+e.id);
          }
          // else{ 
          //   console.debug("#ComputeCountRange didn't add event "+e.id);
          // }
        }
    },

    resetEvents : function (){ 
      Events = {};
      EventCollection.eventToRender = undefined;
      EventCollection.refetchEvents(true,true);
    },
    
    //this is now done at server side
    fitMainConfEvent : function (){
        // if(!mainConfEvent)return;
        // var oldStartDate = moment(this.start).format();
        // var oldEndDate = moment(this.end).format();
        // var minDate = moment("5000-10-10"),
        //         maxDate = moment("1990-10-10");

        // var children = EventCollection.getChildren(mainConfEvent, {concat:true,onlyEvent:true,noSidebar : true}); 
        // for(var i in children){
        //   var child = children[i];
        //   if(minDate.isAfter(child.start)) minDate = moment(child.start);
        //   if(maxDate.isBefore(child.end))  maxDate = moment(child.end); 

        // }
        // if(!minDate.isSame(moment("5000-10-10"))) mainConfEvent.start = minDate.format();
        // if(!maxDate.isSame(moment("1990-10-10"))) mainConfEvent.end = maxDate.format();

        // if(oldStartDate !== mainConfEvent.start || oldEndDate !== mainConfEvent.end){
        //     mainConfEvent.render();
        //     mainConfEvent.persist(); 
        // } 
    }, 


    getToppestParent : function (){ 
        return EventCollection.getChildren(mainConfEvent, {recursive:false,onlyEvent:true,noSidebar:true}); 

        // var toppestParent = []; 

        // var tmp =  EventCollection.getChildren(mainConfEvent, {recursive:false,onlyEvent:true,noSidebar:true}); 
        // //ignore allday events
        // for(var i in tmp){
        //   var event = tmp[i];
        //   if(event.allDay === true){
        //     toppestParent = toppestParent.concat(EventCollection.getToppestNonAllDayChildren(event)); 
        //   }else{
        //     toppestParent.push(event);
        //   }
        // }
        // return toppestParent;  
    },

    // getToppestNonAllDayChildren : function(parent)
    // {
    //     var toppestNonAllDayChildren = []; 
    //     var tmp = EventCollection.getChildren(parent, {recursive:false,onlyEvent:true,noSidebar:true});
    //     for(var i in tmp){
    //       var event = tmp[i];
    //       if(event.allDay){
    //         toppestNonAllDayChildren = toppestNonAllDayChildren.concat(EventCollection.getToppestNonAllDayChildren(event));

    //       }else{ 
    //         toppestNonAllDayChildren.push(event);
    //       }
    //     }

    //     return toppestNonAllDayChildren;
    // },

    

      // if(EventCollection.eventToRender){ 
      //     var brothersTmp = EventCollection.getToppestParent();
      //     var eventToRender = Events[EventCollection.eventToRender.id]; 
      //     var oldDayToRender = {
      //       start:moment(EventCollection.eventToRender.oldStart).startOf('day')
      //       ,end :moment(EventCollection.eventToRender.oldEnd).endOf('day')
      //     };
      //     var newDayToRender = {
      //       start:moment(eventToRender.start).startOf('day')
      //       ,end:moment(eventToRender.end).endOf('day')
      //     }; 

      //     //compute affected events
      //     for(var i in brothersTmp){
      //       var e = brothersTmp[i]; 
      //       if( e.id == mainConfEvent.id  )continue; 

      //       if(!e.isOutOf(oldDayToRender) || !e.isOutOf(newDayToRender) ){
      //         // console.log("#######affecting "+e.id);
      //         delete EventCollection.broCountRange[e.id];
      //         brothers.push(e);
      //       }
      //     } 
      // }else{
      //   brothers = EventCollection.getToppestParent();  
      // }

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
}

