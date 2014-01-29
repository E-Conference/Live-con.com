
 

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
        if(force!==true && (EventCollection.forceMainConfRendering!==true && EventCollection.eventsToComputeBroCountRange.length==0)){ 
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
            console.log(EventCollection.broCountRange) 
            // return EventCollection.broCountRange;
            
            function computeCountRange(bros,doChildren){
              //copy array
              var remaining = bros.slice(0);
              for (var i in bros){
                curBro = bros[i];  
                // console.log("curBro",curBro.id)
                //create rtn object for curBro  
                baseCount = EventCollection.broCountRange[curBro.id].count;
                var brosId = curBro.getBrosId(); 
                for (var j in remaining){
                  bro = remaining[j];  
                  //ensure the bro is not itself  
                  if(curBro.id===bro.id )continue;  
                  //ensure the bro is a real bro
                  if(curBro.isOutOf(bro,true) || ($.inArray(bro.id, brosId) === -1))continue;   
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
              } 
            } 
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
}

