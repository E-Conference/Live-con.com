
var CalEvent = function(event){
    for (var i in event){
        this[i] = event[i];
    } 
} 

CalEvent.prototype.render = function () {  
        this.formatDate();

        // render the event on the calendar
        $calendar.fullCalendar('removeEvents', this.id); 
        $calendar.fullCalendar('renderEvent', this);
};

CalEvent.prototype.persist = function(){ 
      var toSend = {
        parent : this['parent'],
        id  : this['id'],
        allDay  : this['allDay'],
        title : this['title'],
        parent  : this['parent'],
        end : this['end'],
        start : this['start']
      } 
      $.post(
        op.quickUpdateUrl,
        toSend,
        function(doc) {   
                bootstrapAlert("success","event <b>"+toSend['title']+"</b> has been well updated"); 
        },
        'json'
      );
      bootstrapAlert("info","update request sent ","Info : ","<i class='icon-spinner icon-spin'></i>");
};




/**
 * child date has changed, update parent's one to fit
 * @param  {obj} event db event (with start,end,allDay,title...)
 */
CalEvent.prototype.updateParentDate = function(){

    updateParentDate(this);

    function updateParentDate(event){
        //check if the event has been dropped out of the parent 
        
        var parent = EventCollection.find(event.parent.id,{noSidebar:true});

        if(!parent || event.isInsideOf(parent))return;  

        //event is out of parent
        console.log("isOutOfParent");  
        var Eduration = moment(event['end']).diff(event['start']); 

        //event start is before parent start
        if(moment(event['start']).isBefore(parent['start'])){
          parent['start'] = event['start'];

          event['end'] = moment(event['start']).add(Eduration).format();
        }
        //event end is after parent end
        if(moment(event['end']).isAfter(parent['end'])){
          // event['end'] = parent['end']; 
          parent['end'] = event['end'];

          event['start'] = moment(event['end']).subtract(Eduration).format();
        } 
        updateParentDate(parent); 
        parent.render();
        parent.persist(); 
    }
}

// when resized, affect recursively child date
CalEvent.prototype.updateChildrenDate = function(){

    updateChildrenDate(this);

    function updateChildrenDate(event){
        var children = EventCollection.getChildren(event, {concat:false,recursive:false,onlyEvent:true}); 
        var Eduration = moment(event.end).diff(event.start); 
        for(var i in children)
        {
          var child = children[i];
          var Cduration = moment(child.end).diff(child.start); 
          //TODO check if not less than 30mn  
          if(!child.isInsideOf(event))
          { 
            var childStart = child['start'],
                childEnd = child['end'];
            //child start is before event start
            if(moment(child['start']).isBefore(event['start']))
            {
              // event['start'] = parent['start'];
              childStart = event['start'];

              childEnd = moment(childStart).add(Cduration).format();
            }
            //child end is after child start
            if(moment(child['end']).isAfter(event['end']))
            {
              // event['start'] = parent['start'];
              childEnd = event['end']; 

              childStart = moment(childEnd).subtract(Cduration).format(); 
            } 
            if(Cduration>Eduration){
              childStart = event['start'];
              childEnd = event['end'];
            }
            child['start'] = childStart;
            child['end'] = childEnd;
            updateChildrenDate(child);
            child.render();
            child.persist();
          } 
        }
    }
}


/**
 * get dateless date given to the this Calevent start date
 *   used when a parent CalEvent is dropped from sidebar to calendar
 */
CalEvent.prototype.SetRecurDate = function(){
    var lastMoment; 

    var children = EventCollection.getChildren(this, {concat:false,onlyEvent:true});
    lastMoment = moment(this['start']);   
    this['start']  = lastMoment.format(); 
    lastMoment = lastMoment.add("minutes",30);
    this['end']  = lastMoment.format(); 
    if(!this.subChildren ||  children.length > 0) {
      for(var i in children){
        setRecurChildDate(children[i]); 
      } 
    }else{
        
        lastMoment = lastMoment.add("hours",1);
      } 
    lastMoment = lastMoment.add("minutes",30);
    this['end']  = lastMoment.format(); 
    this.deleteParent();
   
  
      function setRecurChildDate(child){ 
        child['start']  = lastMoment.format();
        // lastMoment = lastMoment.add("minutes",30);
        lastMoment = lastMoment.add("minutes",30);

        if(child.subChildren && child.subChildren.length > 0){
          for(var i in child.subChildren){
            setRecurChildDate(child.subChildren[i]); 
          }
        }else{
          
          lastMoment = lastMoment.add("hours",1);
        }

        lastMoment = lastMoment.add("minutes",30);

        child['end']  = moment(lastMoment).format();    
        child.elem.remove();  

        child.render();
        child.persist();
      } 
} 


// remove relation with old parent if exists
// and update relation with new parent (render event but dont persist changes to db)
CalEvent.prototype.setParent = function (parent){

    this.deleteParent();
    //check if this is going to do a loop in the tree
    if(this.isChild(parent)){
      bootstrapAlert("warning","cannot set this <b>"+this.title+"</b> as child of <b>"+parent.title+"</b> because this is going to do a loop in the event tree","Circular reference : ");
      return;
    }
    //affect parent to child
    this.parent = { "id": parent.id};
    //add child to parent
    parent.children.push( { "id": this.id});

    //update parentDate 
    this.render();
    parent.render();
};

CalEvent.prototype.deleteParent = function (){   
    if(!this)return;
        
    parent = Events[this.parent.id];
    if(!parent)return;
    this.parent.id = "";
    this.parent.title = "";
    this.parent.name = "";
    for( var i in parent.children){
      if(parent.children[i].id === this.id && this.id !== ""){ 
        delete parent.children[i];
        return;
      }
    } 
};

CalEvent.prototype.isChild = function (parent){

    var children = EventCollection.getChildren(parent, {concat:true,onlyEvent:true}); 
    for(var i in children){
        if(children[i].id === this.id){ 
          return true;  
        }
    }
    return false;
};

CalEvent.prototype.hasChild = function (){
    //check if it's going to do a loop in the tree

    var children = EventCollection.getChildren(this,  {concat:true,onlyEvent:true});
    if(!children || children.length < 1)
      return false;
    return true;
};


/**
 * construct popOverContent html
 * @return {html string} (to be appended to bootstrap popover )
 */
CalEvent.prototype.getPopoverContent = function(){
    var categories = "no categories"
    if(this.categories && this.categories[0] && this.categories[0].name!==""){
        categories = "<ul>";
        for (var i=0;i<this.categories.length;i++){
          categories += "<li style='color:"+this.categories[i].color+";'>"+this.categories[i].name+"</li>";
        }
        categories += "</ul>";
    }
    var themes = "no themes"
    if(this.themes && this.themes[0] && this.themes[0].name!==""){
        themes = "<ul>";
        for (var i=0;i<this.themes.length;i++){
            themes += "<li>"+this.themes[i].name+"</li>";
        }
        themes += "</ul>";
    }
    return "<ul >\
              <li><b>duration : </b>"+moment.duration(moment(this.end).diff(this.start)).humanize()+"</li>\
              <li><b>location : </b>"+((this.location && this.location.name && this.location.name!=="" && this.location.name) || "no location")+"</li>\
              <li class='description'><b>description : </b>"+(this.description || "no description")+"</li>\
              <li><b>categories : </b>"+categories+"\
              <li><b>themes : </b>"+themes+"\
              </li>\
            </ul>"
}


/*--------------------------------------------------------------------------------------------------*/
/*------------------------------------- utils functions --------------------------------------------*/
/*--------------------------------------------------------------------------------------------------*/

/**
 * whether the event is out of the given arg event
 * @param  {CalEvent}  event    to be compared with 
 * @param  {Boolean}   same     (default false) if set to true, will exclude the case event are concatened.
 * @return {Boolean}        
 */
CalEvent.prototype.isOutOf = function(event,same){
    var rtn = ( moment(this['end']).isBefore(event['start']) ||
                moment(this['start']).isAfter(event['end']));
    if(same ===true) rtn = rtn || moment(this['end']).isSame(event['start'])
                               || moment(this['start']).isSame(event['end'])
    return  rtn;
}
CalEvent.prototype.isInsideOf = function(event){
    return (moment(this['start']).isAfter(event['start']) &&
            moment(this['end']).isBefore(event['end'])
    );
};

CalEvent.prototype.isInstant = function(){
    var diff =moment(this["start"]).diff(this["end"]);
    return (diff  === 0 ) || (diff  === 1 );   
}

CalEvent.prototype.formatDate = function () {  
    this['start'] = moment(this['start']).format();
    this['end'] = moment(this['end']).format();
};