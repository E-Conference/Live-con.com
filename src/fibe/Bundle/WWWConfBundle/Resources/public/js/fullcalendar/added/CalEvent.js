
var CalEvent = function(event){

    this["id"]          = event.id; 
    this["description"] = event.description;
    this["topics"]      = event.topics;
    this["roles"]       = event.roles;
    this["location"]    = event.location; 
    this["title"]       = event.name || event.title; 
    this["start"]       = event.start || event.start_at;
    this["end"]         = event.end || event.end_at; 
    this["allDay"]      = (event.allDay === true || event.allDay === false) ?
                                event.allDay : 
                                event.is_allday ? 
                                        event.is_allday ==="true" : 
                                        event.allDay ==="true" ;
    this["is_mainconfevent"]     = (event.is_mainconfevent === true || event.is_mainconfevent === false) ?  
                                event.is_mainconfevent : 
                                event.is_mainconfevent ==="true" ;
    if(this["is_mainconfevent"]){
      mainConfEvent = this;
      this["editable"] = false
    }
    this["parent"]     = event.parent;
    this["children"]   = event.children;
    this["categories"] = event.categories || [];

    if( event.categories && event.categories.length > 0 && event.categories[0].color)
      this["color"] = event.categories[0].color;  
    
    this["borderColor"] = ColorLuminance(this["color"] ? this["color"] : "#3a87ad", -0.35); 

    //resources
    if(!this["resource"])this["resource"] =  resConfig[currentRes].parse(event);
    // if(this.location && this.location.id!= ""){
    //   this["resource"].push(this.location.id);
    // }else{
    //   //TODO put to a "not defined yet" resource
    //   //TODO put to a "not defined yet" resource
    // }
    this.renderForRefetch();
    // if(!calendar_events_indexes[this.id]){
    //   calendar_events.push(this);
    //   calendar_events_indexes[this.id]=calendar_events.length-1; 
    // }
    // else{
    //   calendar_events.splice(calendar_events_indexes[this.id],1,this); 
    // }

    Events[this["id"]] = this; 
 
}; 

CalEvent.prototype.renderForRefetch = function(){   
    // console.log("##renderForRefetch",this);
    if(this.isInstant())return;
    if(calendar_events_indexes[this.id]=== undefined){
      // console.debug("#renderForRefetch rendering "+this.id);
      calendar_events.push(this);
      calendar_events_indexes[this.id]=calendar_events.length-1; 
    }
    else{
      // console.debug("#renderForRefetch updating "+this.id);
      calendar_events.splice(calendar_events_indexes[this.id],1,this); 
    } 
    // console.log("",calendar_events[calendar_events_indexes[this.id]]);
};

CalEvent.prototype.removeForRefetch = function(){    
    if(calendar_events_indexes[this.id]!== undefined){
      calendar_events.splice(calendar_events_indexes[this.id],1); 
      for(var i in calendar_events_indexes){
        if(calendar_events_indexes[i]>=calendar_events_indexes[this.id]){
          calendar_events_indexes[i]--;
        }
      }
      delete calendar_events_indexes[this.id] ;
    }
    // console.log(calendar_events);
    // console.log(calendar_events_indexes); 
};


CalEvent.prototype.persist = function(add){ 
  if(this.is_mainconfevent)return;
    var toSend = {
      parent    : this['parent'],
      id        : this['id'],
      allDay    : this['allDay'],
      title     : this['title'],
      parent    : this['parent'],
      end       : this['end'],
      start     : this['start'],
      resource  : this['resource'],
      currentRes: currentRes,
    }  
    $.post(
      add === true ? op.quickAddUrl : op.quickUpdateUrl,
      toSend,
      function(response) {  
        bootstrapAlert("success","event <b>"+toSend['title']+"</b> has been well "+ (add=== true ? "added" : "updated")); 
        // console.log(toSend.id+" persisted",toSend); 
        if(response.mainConfEvent){
            EventCollection.updateMainConfEvent(response.mainConfEvent.start,response.mainConfEvent.end);
            EventCollection.refetchEvents(); 
        }
      },
      'json'
    );
    bootstrapAlert("info","update request sent ","Info : ","<i class='fa-2x fa fa-spinner fa-spin'></i>");
};


/**
 * add events to EventCollection.eventsToComputeBroCountRangeIndexes
 * @param CalEvent event to add
 * @param Object   opt   :
 *                  allBrosInDay=true : add all brothers too 
 *                    
 *                    * add toppest non allday of the day in the cases that the event itself or its parent is an allDay event 
 */
CalEvent.prototype.computeCountRange = function(opt){
        console.log("#ComputeCountRange allBrosInDay "+this.id);
        if(!opt)opt={}
          var bros; 
        if(opt.allBrosInDay || this.allDay){
          bros = calendar_events;
          // var bros = event.getBros();
          var dayToRender = {
            start:moment(this.start).startOf('day')
            ,end:moment(this.end).endOf('day')
          }; 
          console.log("#ComputeCountRange allBrosInDay "+this.id,dayToRender);
          if(Events[this.parent.id] && Events[this.parent.id].allDay){
            bros = this.getNonAllDayBros();
            console.log("#ComputeCountRange parent is allDay",bros);
            for(var i in bros){
              var bro = bros[i]; 
              if(!bro.isOutOf(dayToRender) || !bro.isOutOf(dayToRender) ){ 
              addEvent(bro.id); 
              }
            }
          }else{
            for(var i in bros){
              var bro = bros[i]; 
              if(!bro.isOutOf(dayToRender) || !bro.isOutOf(dayToRender) ){
                if(opt.allBrosInDay !== true || this.isBroOf(bro) ){
                  addEvent(bro.id);
                }
              }
            }
          }
        } 
        addEvent(this.id)
          function addEvent(id){
            // var e = Events[id];
            // if(e.allDay){
            //   for(var i in e.children ){
            //     var childId = e.children[i].id; 
            //     // alert("added "+childId+" when computeCountRange of "+e.id)
            //     // alert(Events[e.children[i].id])
            //     addEvent(childId)
            //   }

            // }else{
              addNonAllDayEvent(id);
            // }
            function addNonAllDayEvent(id){

              if($.inArray(id, EventCollection.eventsToComputeBroCountRangeIndexes) === -1 && !Events[id].allDay) { 
                EventCollection.eventsToComputeBroCountRangeIndexes.push(id);
                EventCollection.broCountRange[id] = {count:1,range:0};
                console.debug("#ComputeCountRange added "+id);
              }
              else{ 
                console.debug("#ComputeCountRange didn't add event "+id);
              }
            }
          } 
    },






/**
 * child date has changed, update parent's one to fit
 * @param  {obj} event db event (with start,end,allDay,title...)
 */
CalEvent.prototype.updateParentDate = function(){

    updateParentDate(this);

    function updateParentDate(event){
        //check if the event has been dropped out of the parent 
        
        var parent = EventCollection.find(event.parent.id,{noSidebar:true});

        if(!parent)return;  

        // //make main conf get a special treatment
        // //to make it fit to its children date
        if(parent.is_mainconfevent){ 
          return;
        }
        if(event.isInsideOf(parent))return;  

        //event is out of parent 
        var Eduration = moment(event['end']).diff(event['start']);  
        var changed = false,
            oldStart = event['start'],
            oldEnd = event['end']
            ;
          console.log(event.id+" is not inside of "+parent.id)
          // console.log("oldStart : "+oldStart)
          // console.log("oldEnd : "+oldEnd)
        //event start is before parent start
        if(moment(event['start']).isBefore(parent['start'])){
          parent['start'] = event['start'];

          event['end'] = moment(event['start']).add(Eduration).format();
          changed = true;
        }
        //event end is after parent end
        if(moment(event['end']).isAfter(parent['end'])){
          // event['end'] = parent['end']; 
          parent['end'] = event['end'];

          event['start'] = moment(event['end']).subtract(Eduration).format();
          changed = true;
        } 

        if(changed){ 
          // console.log("event['start'] : "+event['start'])
          // console.log("event['end'] : "+event['end'])

          // EventCollection.eventToRender = {id:parent["id"],oldStart:oldStart,oldEnd:oldEnd}; 
          updateParentDate(parent); 

          parent.computeCountRange({allBrosInDay:true});   
          parent.renderForRefetch();
          parent.persist(); 
        }
    }
};

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
          var changed =false;
          //TODO check if not less than 30mn  
          if(child.isInsideOf(event))continue; 
            var childStart = child['start'],
                childEnd = child['end'];
            //child start is before event start 
            if(moment(child['start']).isBefore(event['start']))
            {
              // event['start'] = parent['start'];
              childStart = event['start'];

              childEnd = moment(childStart).add(Cduration).format();
              changed=true;
            }
            //child end is after child start 
            if(moment(child['end']).isAfter(event['end']))
            {
              // event['start'] = parent['start'];
              childEnd = event['end']; 

              childStart = moment(childEnd).subtract(Cduration).format(); 
              changed=true;
            }  

            if(Cduration>Eduration){
              childStart = event['start'];
              childEnd = event['end'];
              changed=true;
            }
            if(changed){ 
              child['start'] = childStart;
              child['end'] = childEnd;
              updateChildrenDate(child,{allBrosInDay:true});
              child.computeCountRange({allBrosInDay:true});   
              child.renderForRefetch();
              child.persist(); 
            }
        }
    }
};


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
        children[i].elem.remove();
        setRecurChildDate(children[i]); 
      } 
    }else{
        
        lastMoment = lastMoment.add("hours",1);
      } 
    lastMoment = lastMoment.add("minutes",30);
    this['end']  = lastMoment.format();  
   
  
      function setRecurChildDate(child){ 
        child['start']  = lastMoment.format();
        // lastMoment = lastMoment.add("minutes",30);
        lastMoment = lastMoment.add("minutes",30);

        if(child.subChildren && child.subChildren.length > 0){
          for(var i in child.subChildren){
            child.subChildren[i].elem.remove();
            setRecurChildDate(child.subChildren[i]); 
          }
        }else{
          
          lastMoment = lastMoment.add("hours",1);
        }

        lastMoment = lastMoment.add("minutes",30);

        child['end']  = moment(lastMoment).format();    
        // child.elem.remove();  

        child.renderForRefetch();
        child.persist();
      } 
};

CalEvent.prototype.fitToDay = function (oldStart,oldEnd){
  var duration = moment(this.end).diff(moment(this.start));
  var midnightLimit = moment(this.end).startOf("day");
  if(duration >= moment().add("d",1).diff(moment())){
    this.allDay = true;
    return;
  } 
  if(oldStart.isSame(midnightLimit) ||oldEnd.isSame(midnightLimit)){
    this.start = oldStart.format();
    this.end = oldEnd.format();
    return false;
  }
  if(moment(this.start).diff(midnightLimit) > midnightLimit.diff(this.end)){
  // if(this.start < oldStart){
    
    //we put the event to the next day
    this.start = midnightLimit.format();
    this.end = moment(this.start).add(duration).format(); 

  }else{
    //we put the event to the previous day
    this.end = midnightLimit.format();
    // this.end = moment(midnightLimit).subtract("s",1).format();
    this.start = moment(this.end).subtract(duration).format(); 
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
    this.renderForRefetch();
    parent.renderForRefetch();
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

CalEvent.prototype.isBroOf = function (bro){  
    if(this.id == mainConfEvent.id)return false; 
    var brosOfBroId = bro.getBrosId(); 
    for(var i in brosOfBroId){ 
      if(brosOfBroId[i] === this.id)
        return true
    }
    return false;
};

CalEvent.prototype.getBros = function (){ 
    if(this.id == mainConfEvent.id)
        return []; 

    return EventCollection.getChildren(Events[this.parent.id], {recursive:false,concat:false, onlyEvent:true, noSidebar : true})
};
CalEvent.prototype.getBrosId = function (){ 
    if(this.id == mainConfEvent.id)
        return []; 
    var id = this.id;
    // console.log("children of"+this.id,Events[this.parent.id].children)
    return $(Events[this.parent.id].children).map(function(key,value){ if(value && value.id!=id){return value.id;}})

    // return $(Events[this.parent.id].children).map(function(key,value){return value.id!=this.id?value.id:undefined;})
};
CalEvent.prototype.getNonAllDayBrosId = function (){ 
    if(this.id == mainConfEvent.id)
        return [];  
    var parent = Events[this.parent.id];
    var rtn = [];
    //add toppest non all days
    if(parent.allDay){
      // alert("add toppest non all days");
      for (var i in Events){
        if(Events[i].id==this.id)continue;
        if(!Events[i].allDay && Events[Events[i].parent.id].allDay)
          rtn.push(Events[i].id);
      } 
      return rtn
    }
    var bros = parent.children;
    for (var i in bros){ 
      if(bros[i].id==this.id)continue;
      if(!Events[bros[i].id].allDay){
        rtn.push(bros[i].id);
        continue;
      }
    } 
    return rtn; 
};
CalEvent.prototype.getNonAllDayBros = function (){ 
    if(this.id == mainConfEvent.id)
        return [];   
    var brosId = this.getNonAllDayBrosId();
    var rtn = [];
    for(var i in brosId){ 
      rtn.push(Events[brosId[i]]);
    }
    return rtn; 
};


/**
 * construct popOverContent html
 * @return {html string} (to be appended to bootstrap popover )
 */
CalEvent.prototype.getPopoverContent = function(){
    var categories = "no categories";
    if(this.categories && this.categories[0] && this.categories[0].name!==""){
        categories = "<ul>";
        for (var i=0;i<this.categories.length;i++){
          categories += "<li><span style='color:"+this.categories[i].color+";'>"+this.categories[i].name+"</span></li>";
        }
        categories += "</ul>";
    }
    var topics = "no topics"
    if(this.topics && this.topics[0] && this.topics[0].name!==""){
        topics = "<ul>";
        for (var i=0;i<this.topics.length;i++){
            topics += "<li>"+this.topics[i].name+"</li>";
        }
        topics += "</ul>";
    }
    var roles = "no roles"
    if(this.roles && this.roles[0] && this.roles[0].id!==""){
        roles = "<ul>";
        for (var i=0;i<this.roles.length;i++){
            roles += "<li><b>"+this.roles[i].type+" : </b>"+this.roles[i].person.name+"</li>";
        }
        roles += "</ul>";
    }
    return "<ul class='event-popover' >\
              <li><b>duration : </b>"+moment.duration(moment(this.end).diff(this.start)).humanize()+"</li>\
              <li><b>location : </b>"+(this.location && this.location.name && this.location.name!=="" ? this.location.name :  "no location")+"</li>\
              <li class='description'><b>description : </b>"+(this.description || "no description")+"</li>\
              <li><b>categories : </b>"+categories+"\
              <li><b>topics : </b>"+topics+"\
              <li><b>roles : </b>"+roles+"\
              </li>\
            </ul>"
};


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
    var rtn ; 
    if(event.allDay){
      rtn = ( (moment(this['end']).subtract("s",1).endOf("day").isAfter(moment(event['end']).endOf("day"))) ||
              (moment(this['start']).isBefore(moment(event['start']).startOf("day")) )
              ); 
    }else{

      rtn = ( moment(this['end']).isBefore(event['start']) ||
              moment(this['start']).isAfter(event['end']));
    }
    if(same ===true) rtn = rtn || moment(this['end']).isSame(event['start'])
                               || moment(this['start']).isSame(event['end']); 
    return  rtn;
};
CalEvent.prototype.isInsideOf = function(event){  
    return (moment(this['start']).isAfter(event['start']) &&
            moment(this['end']).isBefore(event['end'])
    );
};

CalEvent.prototype.isInstant = function(){
    var diff =moment(this["start"]).diff(this["end"]);
    return (diff  === 0 ) || (diff  === 1 );   
};

CalEvent.prototype.isOneDayLong = function(){ 
    return (moment(this["start"]).dayOfYear() == moment(this["end"]).dayOfYear()); 
};

CalEvent.prototype.formatDate = function () {  
    this['start'] = moment(this['start']).format();
    this['end'] = moment(this['end']).format();
};

CalEvent.prototype.duration = function () {  
    return moment(this.end).diff(this.start);
};

//set lighter/darker
function ColorLuminance(hex, lum) {

  // validate hex string
  hex = String(hex).replace(/[^0-9a-f]/gi, '');
  if (hex.length < 6) {
    hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
  }
  lum = lum || 0;

  // convert to decimal and change luminosity
  var rgb = "#", c, i;
  for (i = 0; i < 3; i++) {
    c = parseInt(hex.substr(i*2,2), 16);
    c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
    rgb += ("00"+c).substr(c.length);
  }

  return rgb;
}