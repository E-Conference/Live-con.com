
var CalEvent = function(event){
    for (var i in event){
        this[i] = event[i];
    } 
}


CalEvent.prototype.isInstant = function(){
    var diff =moment(this["start"]).diff(this["end"]);
    return (diff  === 0 ) || (diff  === 1 );   
}



CalEvent.prototype.formatDate = function () {  
    this['start'] = moment(this['start']).format();
    this['end'] = moment(this['end']).format();
};

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
 * [isOutOf description]
 * @param  {CalEvent}       event   
 * @return {Boolean}        whether the current event is out of the arg
 */
CalEvent.prototype.isOutOf = function(event){ 
    return (moment(this['end']).isBefore(event['start']) ||
            moment(this['start']).isAfter(event['end'])
            );
}; 
CalEvent.prototype.isInsideOf = function(event){
    return (moment(this['start']).isAfter(event['start']) &&
            moment(this['end']).isBefore(event['end'])
            );
};


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
    // console.log("OLOLOLOL",children);
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