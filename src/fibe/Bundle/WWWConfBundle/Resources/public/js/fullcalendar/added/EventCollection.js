
 

var EventCollection = { 

 
      /**
       * events that need an UI update
       * @type {CalEvent}
       */
      eventsToRender:[],


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
          if(!Events[id] || (op.noSidebar ===true && Events[id].isInstant()))return; 
          return event; 
    }, 
        /** 
         * @param parent        :  db model event
         * @param op            : concat : ( boolean ) if true : dont preserve the tree nature of the relation (just concat children/subchildren/subsu... them)
         *                        noSidebar(default false),
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
            if( !parent.children.hasOwnProperty( i ) ) return;

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

    /**
     * get Toppest Events in the eventsToRender array
     * @return {[CalEvent]} 
     */
    getToppestEventsToRender : function (){

        var toppestParent = []; 

          // get toppest parent 
        for (var i in this.eventsToRender){
            var event = Events[this.eventsToRender[i]];
            var isSidebar =false;
            var breakWhile=false;
            while(breakWhile===false){ 
              // console.log(event);
              var parent = EventCollection.find(event.parent.id);  
              if(!parent || !parent.elem){ 
                breakWhile = true;
              }else {
                event = parent;
              }
              isSidebar = $(event.elem).hasClass("external-event");

            }

            //toppest parent
            if(isSidebar || event.isInstant() || $.inArray(event, toppestParent)!==-1 ){
              // console.log("event "+event.id+" already toppest") ;
              continue;
            }
            toppestParent.push(event); 
        }
        return toppestParent;
    },


    getToppestParent : function (){
        var toppestParent = []; 

          // get toppest parent 
        for (var i in Events){
            var event = Events[i];
            var breakWhile=false;
            while(breakWhile===false){ 
              // console.log(event);
              var parent = EventCollection.find(event.parent.id);  
              if(!parent || !parent.elem){ 
                breakWhile = true;
              }else {
                event = parent;
              }

            }

            //toppest parent
            if( $.inArray(event, toppestParent)!==-1 ){
              console.log("event "+event.id+" already toppest") ;
              continue;
            }
            if( event.isInstant() ){
              console.log("event "+event.id+" instant") ;
              continue;
            }
            toppestParent.push(event); 
        }
        return toppestParent;
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