
 

var EventCollection = { 

 
      /**
       * events that need an UI update
       * @type {CalEvent}
       */
      //used to get day(s) to recalculate broCountRange
      eventToRender:undefined,
 


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
    updateMainConfEvent : function(newStart,newEnd){ 
            console.log("mainConfEvent changed, rendering...");  
            stopRender = true;
            mainConfEvent.start = moment(newStart, "YYYY-MM-DD HH:mmZ").format();
            mainConfEvent.end = moment(newEnd, "YYYY-MM-DD HH:mmZ").format(); 

            bootstrapAlert("success","conference event "+mainConfEvent.title+" have been updated") 
            // EventCollection.eventToRender = mainConfEvent;
            EventCollection.refetchEvents();
    },
    refetchEvents : function(force){

        function doWork() {
          mainConfEvent.renderForRefetch(); 
          stopRender = false;
          fetched = force === true ? false : true ;
          $calendar.fullCalendar('refetchEvents');   
        }
        setTimeout(doWork, 1);
    },

    resetEvents : function (){
      EventCollection.rtnArray = {};
      EventCollection.eventToRender = undefined;
      EventCollection.refetchEvents(true);
    },
    
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

    /**
     * get Toppest Events in the eventsToRender array
     * @return {[CalEvent]} 
     */
    // getToppestEventsToRender : function (){

    //     var toppestParent = []; 

    //       // get toppest parent 
    //     for (var i in this.eventsToRender){
    //         var event = Events[this.eventsToRender[i]];
    //         var isSidebar =false;
    //         var breakWhile=false;
    //         while(breakWhile===false){ 
    //           // console.log(event);
    //           var parent = EventCollection.find(event.parent.id);  
    //           if(!parent || !parent.elem){ 
    //             breakWhile = true;
    //           }else {
    //             event = parent;
    //           }
    //           isSidebar = $(event.elem).hasClass("external-event");

    //         }

    //         //toppest parent
    //         if(isSidebar || event.isInstant() || $.inArray(event, toppestParent)!==-1 ){
    //           // console.log("event "+event.id+" already toppest") ;
    //           continue;
    //         }
    //         toppestParent.push(event); 
    //     }
    //     return toppestParent;
    // },


    getToppestParent : function (view){ 
        var toppestParent = []; 

        var tmp =  EventCollection.getChildren(mainConfEvent, {recursive:false,onlyEvent:true,noSidebar:true}); 
        //ignore allday events
        for(var i in tmp){
          var event = tmp[i];
          if(event.allDay === true){
            toppestParent = toppestParent.concat(EventCollection.getToppestNonAllDayChildren(event)); 
          }else{
            toppestParent.push(event);
          }
        }
        return toppestParent;
        // $(view.getSlotContainer()).find(".fc-event").each(function(){
        //   var e = EventCollection.getEventByDiv($(this));
        //   parent = Events[e.parent.id];
        //   if(parent){
        //     if(parent.is_mainconfevent){
        //       toppestParent.push(e); 
        //     }
        //   }else{
        //     alert("no parent for "+e.id+":"+e.title+"\n setting "+mainConfEvent.title+" as new parent");

        //     e.setParent(mainConfEvent);
        //     e.updateParentDate();
        //     e.persist();
        //   }
        // }); 
        //   // get toppest parent 
        // for (var i in Events){

        //     var event = Events[i];
        //     if(this.find(event.parent.id,{noSidebar:true}) ){ 
        //       continue;
        //     }
            
        //     // var breakWhile=false;
        //     // while(breakWhile===false){  
        //     //   var parent = EventCollection.find(event.parent.id);  
        //     //   if(!parent || !parent.elem){ 
        //     //     breakWhile = true;
        //     //   }else {
        //     //     event = parent;
        //     //   }

        //     // }

        //     // //toppest parent
        //     // if( $.inArray(event, toppestParent)!==-1 ){
        //     //   console.log("event "+event.id+" already toppest") ;
        //     //   continue;
        //     // }
        //     // if( event.isInstant() ){
        //     //   console.log("event "+event.id+" instant") ;
        //     //   continue;
        //     // }


        //     toppestParent.push(event); 
        // }
        return toppestParent;
    },

    getToppestNonAllDayChildren : function(parent)
    {
        var toppestNonAllDayChildren = []; 
        var tmp = EventCollection.getChildren(parent, {recursive:false,onlyEvent:true,noSidebar:true});
        for(var i in tmp){
          var event = tmp[i];
          if(event.allDay){
            toppestNonAllDayChildren = toppestNonAllDayChildren.concat(EventCollection.getToppestNonAllDayChildren(event));

          }else{
            // alert(event.id+" "+ event.title+"were child of "+parent.id+" "+parent.title+" and was added");
            toppestNonAllDayChildren.push(event);
          }
        }

        return toppestNonAllDayChildren;
    },
    rtnArray:{},
    getBroCountRange : function(brothers){ 
      if(brothers.length==0)return false;
      var brothers = brothers;
      var minLeft;
      var done     = []; 
      var bro;
      var curBro;
      var baseCount; 

      // console.log("getBroCountRange : brothers before = ",brothers);
      // TODO children
      // TODO ghost bro
      if(EventCollection.eventToRender){
        
        var brothersTmp = brothers.slice(0);
        brothers = [];
        //compute days to render
        var eventToRender = Events[EventCollection.eventToRender.id]; 
        var oldDayToRender = {
          start:moment(EventCollection.eventToRender.oldStart).startOf('day')
          ,end :moment(EventCollection.eventToRender.oldEnd).endOf('day')
        };
        var newDayToRender = {
          start:moment(eventToRender.start).startOf('day')
          ,end:moment(eventToRender.end).endOf('day')
        }; 

        //compute affected events
        for(var i in brothersTmp){
          var e = brothersTmp[i]; 
          // alert(e.title)
          if( e.id == mainConfEvent.id  )continue; 
          // if( e.id == eventToRender.id  )continue; 
          if(!e.isOutOf(oldDayToRender) || !e.isOutOf(newDayToRender) ){
            // console.log("#######affecting "+e.id);
            delete EventCollection.rtnArray[e.id];
            brothers.push(e);
          }
        }

        // EventCollection.eventToRender = undefined; 


        // // var brothersTmp = brothers.slice(0);
        // var eventToRender = Events[EventCollection.eventToRender.id];  
        // brothers = [];
        
        // // var brothersTmp = brothers.slice(0);
        // var brothersTmp = EventCollection.getChildren(Events[eventToRender.parent.id], {concat:true,recursive:false,onlyEvent:true,noSidebar : true}); 

        // // alert(moment(eventToRender.start).format())
        // // console.log("#######affecting "+eventToRender.title);
        // //compute affected events
        // for(var i in brothersTmp){
        //   var e = brothersTmp[i]; 
        //   // alert(e.title)
        //   if( e.id == mainConfEvent.id  )continue; 
        //   // if( e.id == eventToRender.id  )continue; 
        //   if(!e.isOutOf(eventToRender) || !e.isOutOf({start:EventCollection.eventToRender.oldStart,end:EventCollection.eventToRender.oldEnd})){
        //     console.log("#######affecting "+e.id);
        //     delete EventCollection.rtnArray[e.id];
        //     brothers.push(e);
        //   }
        // }
        // // console.log("#######affecting "+eventToRender.id);
        // // delete EventCollection.rtnArray[eventToRender.id];
        // // brothers.push(eventToRender)
        // EventCollection.eventToRender = undefined; 
      }else{
        // EventCollection.rtnArray= {};
      }
      var remaining = brothers.slice(0);
      // console.log("----------------------------------------------------");
      // console.log("affected = ",brothers); 
      // console.log("non affected : ",EventCollection.rtnArray);
      // console.log("----------------------------------------------------");

      for (var i in brothers){
        curBro = brothers[i];  
        // console.log("curBro",curBro.id)
        //create rtn object for curBro
        if(!EventCollection.rtnArray[curBro.id])EventCollection.rtnArray[curBro.id] = {count:1,range:0,minLeft:Events[curBro.id].elem.position().left};

        baseCount = EventCollection.rtnArray[curBro.id].count;

        for (var j in remaining){
          bro = remaining[j]; 

          //ensure the bro is not itself
          if(curBro.id===bro.id )continue;

          //ensure the bro has never been read 
          // if($.inArray(bro.id, done)!==-1)continue;

          //ensure the bro is a real bro
          if(curBro.isOutOf(bro,true))continue;

          // console.log("curBro ",curBro.id," discovering ",bro.id) 

          minLeft = Math.min(EventCollection.rtnArray[curBro.id].minLeft,Events[bro.id].elem.position().left); 
          
          //update self properties
          EventCollection.rtnArray[curBro.id].minLeft = minLeft;
          EventCollection.rtnArray[curBro.id]["count"]++; 

          //register bro as bro of curBro
          EventCollection.rtnArray[bro.id] = {count:baseCount+1,range:EventCollection.rtnArray[curBro.id]["range"]+1,minLeft:minLeft}; 

          //the bro has been read
          // if( $.inArray(bro.id, done)=== -1)
          // {
          //   //the bro has been read and is a real bro of curBro
          //   if($.inArray(bro.id, EventCollection.rtnArray[curBro.id].brosId)!==-1){
          //     //do nothing 
          //   }
          //   //the bro has been read and was out of curBro
          //   else
          //   {
          //     //do nothing 
          //   }

          // }
          
          //old alorithme
          // if(!curBro.isOutOf(bro,true))
          // {
          //   if(!EventCollection.rtnArray[bro.id])EventCollection.rtnArray[bro.id] = {count:0,range:0,minLeft:Events[bro.id].elem.position().left}; 
          //   EventCollection.rtnArray[curBro.id]["count"]++;
          //   EventCollection.rtnArray[bro.id]["count"]++;

          //   minLeft = Math.min(EventCollection.rtnArray[curBro.id].minLeft,EventCollection.rtnArray[bro.id].minLeft); 
          //   EventCollection.rtnArray[bro.id].minLeft = minLeft;
          //   EventCollection.rtnArray[curBro.id].minLeft = minLeft;

          //   range++; 

          // } 
        }
        // console.log(remaining)
        delete remaining[i]
        // done.push(curBro.id);

      }
      // console.log("bro count range",EventCollection.rtnArray) 
      return EventCollection.rtnArray;
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
        var id = div.find(".fc-event-id").text();
        // console.log()
        return Events[id];
    },
}