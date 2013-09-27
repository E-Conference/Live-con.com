


var EventCollection = {
        /** 
         * @param int id    : event id
         * @param obj op    : noSidebar
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
    }
}