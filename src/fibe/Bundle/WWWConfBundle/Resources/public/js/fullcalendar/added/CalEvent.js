
var CalEvent = function(event){ //constructor

    this["id"]          = event.id; 
    this["description"] = event.description;
    this["topics"]      = event.topics;
    this["roles"]       = event.roles;
    this["location"]    = event.location; 
    this["title"]       = this["text"] = event.name || event.title; 
    this["start"]       = event.start || event.start_at;
    this["end"]         = event.end || event.end_at; 
    // this["children"]   = event.children;
    this["categories"] = event.categories || [];
    this["allDay"]      = (event.allDay === true || event.allDay === false) ?
                                event.allDay : 
                                event.is_allday ? 
                                        event.is_allday ==="true" : 
                                        event.allDay ==="true" ;
    this["is_mainconfevent"]     = (event.is_mainconfevent === true || event.is_mainconfevent === false) ?
                                event.is_mainconfevent : 
                                event.is_mainconfevent ==="true" ;
    if( this["is_mainconfevent"])
    {
      mainConfEvent = this;
      this["parent"] = "#";
    }

    if(event.categories[0].name == "Conference event"){
      this["type"]="Conference event";
    }
    else if(event.categories[0].name == "Session event"){
      this["type"]="Session event";
    }else if(event.categories[0].name == "Track event"){
      console.log(event.categories[0]);
      this["type"]="Track event";
    }else{
      this["type"]="Talk event";
    }
    if(event.parent.id != "")
    {
      this["parent"] = event.parent.id;
    }

    if( event.categories && event.categories.length > 0 && event.categories[0].color)
      this["color"] = event.categories[0].color;  
    
    this["borderColor"] = colorLuminance(this["color"] ? this["color"] : "#3a87ad", -0.35);

    //resources 
    if (event["resourceId"]){
      this["resourceId"]  = event.resourceId;
    }
    else
    {
      if (this.location && this.location.id!= ""){
        this["resourceId"] = this.location.id;
      }
      else
      {
        // set the resource as not defined
        this["resourceId"] = "0";
      }
    }

//    this.renderForRefetch();

//    Events[this["id"]] = this;
 
}; 
 
//CalEvent.prototype.persist = function(add){ //persist at server side
//    if(this.is_mainconfevent)return;
//    var toSend = {
//      id        : this['id'],
//      allDay    : this['allDay'],
//      title     : this['title'],
//      parent    : this['parent'],
//      end       : this['end'],
//      start     : this['start']
//    }
//    if( this.resource){
//      toSend['currentRes'] = currentRes;
//      toSend['resourceId'] = this.resource.id;
//    }
//    $.post(
//      add === true ? op.quickAddUrl : op.quickUpdateUrl,
//      toSend,
//      function(response) {
//        if(EventCollection.isLoginPage(response))return;
//        bootstrapAlert("success","event <b>"+toSend['title']+"</b> has been well "+ (add=== true ? "added" : "updated"));
//        // console.log(toSend.id+" persisted",toSend);
//        if(response.mainConfEvent){
//            EventCollection.updateMainConfEvent(response.mainConfEvent.start,response.mainConfEvent.end);
//            EventCollection.refetchEvents();
//        }
//      },
//      'json'
//    ).fail(function(a,b,c) {
//        bootstrapAlert("warning","Could not have been able to update the event.",c+" : ");
//    });
//    bootstrapAlert("info","update request sent ","Info : ","<i class='fa-2x fa fa-spinner fa-spin'></i>");
//};

/*--------------------------------------------------------------------------------------------------*/
/*------------------------------------- rendering functions ----------------------------------------*/
/*--------------------------------------------------------------------------------------------------*/

//CalEvent.prototype.renderForRefetch = function(){
//    // console.log("##renderForRefetch",this);
//    // this["parent"]     = event.parent;
//    if(this.isInstant())return;
//    if(this.categories[0])
//    {
//
//    }
//    if(calendar_events_indexes[this.id]=== undefined){
//      console.debug("#renderForRefetch rendering "+this.id);
//      calendar_events.push(this);
//      calendar_events_indexes[this.id]=calendar_events.length-1;
//    }
//    else{
//      console.debug("#renderForRefetch updating "+this.id);
//      calendar_events.splice(calendar_events_indexes[this.id],1,this);
//    }
//    // console.log("",calendar_events[calendar_events_indexes[this.id]]);
//};

//CalEvent.prototype.removeForRefetch = function(){
//    if(calendar_events_indexes[this.id]!== undefined){
//      for(var i in calendar_events_indexes){
//        if(calendar_events_indexes[i]>calendar_events_indexes[this.id]){
//          calendar_events_indexes[i] = calendar_events_indexes[i]-1;
//        }
//      }
//      calendar_events.splice(calendar_events_indexes[this.id],1);
//      delete calendar_events_indexes[this.id] ;
//    }
//    $calendar.fullCalendar('removeEvents',this.id);
//    // console.log("removeForRefetch",calendar_events_indexes);
//    // console.log("removeForRefetch",calendar_events);
//};

// drop TO sidebar!!
//CalEvent.prototype.dropFromSidebar = function(){
//
//    //set as instant event
//    this['end'] = moment(this['start']);
//    this.formatDate();
//    // remove event from calendar
//    // this.computeCountRange({allBrosInDay:true});
//    this.removeForRefetch();
//    // $calendar.fullCalendar('removeEvents',this.id);
//
//    //affect children
//    // var children = EventCollection.getChildren(this, {concat:true,onlyEvent:true} );
//    // $.each(children,function(i,child){
//    //     //set as instant this
//    //     child['end'] = moment(child['start']);
//    //     child.formatDate();
//    //     child.removeForRefetch();
//    //     sidebar.setSidebarEvent(child,true);
//    //     child.persist();
//    // });
//    //set as sidebar draggable
//    sidebar.setSidebarEvent(this,true);
//    EventCollection.refetchEvents();
//    this.persist();
//};


/**
 * set dateless date when they are dropped from the sidebar  
 */
//CalEvent.prototype.SetRecurDate = function(){
//    var lastMoment;
//
//    lastMoment = moment(this['start']);
//    this['start']  = lastMoment.format();
//    lastMoment = lastMoment.add("minutes",30);
//    this['end']  = lastMoment.format();
//    // var children = EventCollection.getChildren(this, {concat:false,onlyEvent:true});
//    // if(!this.subChildren ||  children.length > 0) {
//    //   for(var i in children){
//    //     children[i].getElem().remove();
//    //     setRecurChildDate(children[i]);
//    //   }
//    // }else{
//
//    //     lastMoment = lastMoment.add("hours",1);
//    //   }
//    lastMoment = lastMoment.add("minutes",30);
//    this['end']  = lastMoment.format();
//
//
//      // function setRecurChildDate(child){
//      //   child['start']  = lastMoment.format();
//      //   // lastMoment = lastMoment.add("minutes",30);
//      //   lastMoment = lastMoment.add("minutes",30);
//
//      //   if(child.subChildren && child.subChildren.length > 0){
//      //     for(var i in child.subChildren){
//      //       child.subChildren[i].getElem().remove();
//      //       setRecurChildDate(child.subChildren[i]);
//      //     }
//      //   }else{
//
//      //     lastMoment = lastMoment.add("hours",1);
//      //   }
//
//      //   lastMoment = lastMoment.add("minutes",30);
//
//      //   child['end']  = moment(lastMoment).format();
//      //   // child.getElem().remove();
//      //   child.computeCountRange()
//      //   child.renderForRefetch();
//      //   child.persist();
//      // }
//};

// remove relation with old parent if exists
// and update relation with new parent (render event but dont persist changes to db)
CalEvent.prototype.setParent = function (parent){

//     this.deleteParent();
     //check if this is going to do a loop in the tree
     // if(this.isChild(parent)){
     //   bootstrapAlert("warning","cannot set this <b>"+this.title+"</b> as child of <b>"+parent.title+"</b> because this is going to do a loop in the event tree","Circular reference : ");
     //   return;
     // }
     //affect parent to child
     this.parent = parent;
     //add child to parent
     // parent.children.push( { "id": this.id});

     //update parentDate
//     this.renderForRefetch();
//     parent.renderForRefetch();
};

//CalEvent.prototype.deleteParent = function (){
//  if (!this) return;
//
//  var parent = Events[this.parent];
//  if (!parent) return;
//  this.parent = "";
////     for( var i in parent.children){
////       if(parent.children[i].id === this.id && this.id !== ""){
////         parent.children[i] = null;
////         delete parent.children[i];
////
////         return;
////       }
////     }
//};

//CalEvent.prototype.hasChild = function (){
//  for(var i in Events)
//  {
//    if(Events[i].parent == this.id)
//      return true;
//  }
//  return false;
//};

//CalEvent.prototype.getElem = function(){
//    return $('.fc-event[data-id="'+this.id+'"]');
//};

CalEvent.prototype.hideElem = function(){
    if(!this.getElem())return;
    this.getElem().stop( true, true )
      .animate(
        { opacity: 0, "margin-top": "-10px" },
        {
          duration:'slow',
          queue   :false,
          complete:function(){
            $(this).hide()}
        });
    this["hide"] = true;
};
CalEvent.prototype.showElem = function(){
  this["hide"] = false;
  if(!this.getElem())return;
    this.getElem().css("margin-top", "-10px");
    this.getElem().stop( true, true ).show().animate({ opacity: 1, "margin-top": "0px" },
                                                     {duration:'slow',queue:false}); 
};

/**
 * construct popOverContent html
 * @return {html string} (to be appended to bootstrap popover )
 */
CalEvent.prototype.getPopoverContent = function(){
    var categories = "no categories",
      i;
    if(this.categories && this.categories[0] && this.categories[0].name!==""){
        categories = "<ul>";
        for (i=0; i<this.categories.length; i++){
          categories += "<li><span style='color:"+this.categories[i].color+";'>"+this.categories[i].name+"</span></li>";
        }
        categories += "</ul>";
    }
    var topics = "no topics";
    if(this.topics && this.topics[0] && this.topics[0].name!==""){
        topics = "<ul>";
        for (i=0; i<this.topics.length; i++){
            topics += "<li>"+this.topics[i].name+"</li>";
        }
        topics += "</ul>";
    }
    var roles = "no roles";
    if(this.roles && this.roles[0] && this.roles[0].id!==""){
        roles = "<ul>";
        for (i=0; i<this.roles.length; i++){
            roles += "<li><b>"+this.roles[i].type+" : </b>"+this.roles[i].person.name+"</li>";
        }
        roles += "</ul>";
    }
  return "<ul class='event-popover' > " +
            "<li><b>duration : </b>" + moment.duration(moment(this.end).diff(this.start)).humanize() + "</li> "  +
            "<li><b>location : </b>" + (this.location && this.location.name && this.location.name!=="" ? this.location.name :  "no location") + "</li> "  +
            "<li class='description'><b>description : </b>" + (this.description || "no description") + "</li> "  +
            "<li><b>categories : </b>" + categories +  "</li> "  +
            "<li><b>topics : </b>" + topics +  "</li> "  +
            "<li><b>roles : </b>" + roles +  "</li> "  +
            "</li> "  +
          "</ul>";
};


/**
 *  add UI (popover, border color etc...)
 *  just after to the fullcalendar "all event render" function
 */
CalEvent.prototype.addFCUi = function (element)
{
  var popoverWidth = 276,
    self = this;

  $(element).each(function (i,element){
    element = $(element);
    //action on hovered by another dragged this
    element.data("border-color",element.css("border-color"))
      // .data("background-color",element.css("background-color"))
      .data("prop",getProp(element));


//    if($.inArray(this.id, calendarEventsIds) !== -1 ){ //stylize only event in the calendar

      //popover
      element.popover({
        trigger : 'hover',
        html : true,
        placement : function ( context,source){
          var popoverProp = getProp($(context));
          var thisProp = getProp(source);
          var calendarProp = getProp($calendar);
          // console.log(popoverProp,thisProp,calendarProp)
          if(thisProp.x + thisProp.w + popoverWidth < calendarProp.x + calendarProp.w )
            return "right";
          if(thisProp.x - popoverWidth > calendarProp.x)
            return "left";
          return "bottom";
        },
        title : ' <b><span class="muted">#'+self.id+'</span> '+self.title+'</b>',
        content : self.getPopoverContent()
      });

//      if (authorized) {
//
//        //the main conf self isnt resizable
//        if(self.id==mainConfEvent.id){
//          element.find(".ui-resizable-handle").remove();
//        }
//      }//end if authorized
//    }//end stylize only this in the calendar

    //change border color on hover
    element.hover(function (){
      $(this).animate({"border-color":"#3F3F3F"},{queue:false});
    },function (){
      $(this).animate({"border-color":$(this).data("border-color")},{queue:false});
    });
  })
};


/*--------------------------------------------------------------------------------------------------*/
/*-------------------------------- date utils functions --------------------------------------------*/
/*--------------------------------------------------------------------------------------------------*/

//CalEvent.prototype.isInstant = function(){
//  var diff =moment(this["start"]).diff(this["end"]);
//  return (diff  === 0 ) || (diff  === 1 );
//};

CalEvent.prototype.formatDate = function () {
  this['start'] = moment(this['start']).format();
  this['end'] = moment(this['end']).format();
};

CalEvent.prototype.duration = function () {
  return moment(this.end).diff(this.start);
};

/*--------------------------------------------------------------------------------------------------*/
/*------------------------------------- global utils function --------------------------------------*/
/*--------------------------------------------------------------------------------------------------*/

//set lighter/darker
function colorLuminance(hex, lum) {

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

jQuery.fn.cssNumber = function(prop){
    var v = parseInt(this.css(prop),10);
    return isNaN(v) ? 0 : v;
};

//get css positionning properties from an event $div (see EventCollection.getDivById)
function getProp(elem){
  rtn={
    x: $(elem).cssNumber("left"),
    y: $(elem).cssNumber("top"),
    w: $(elem).cssNumber("width"),
    h: $(elem).cssNumber("height")
  };
  return rtn;
}









/* before refacto schedule with tree view 26/06/2014 */



// CalEvent.prototype.fitToDay = function (oldStart,oldEnd){
//   oldStart = moment(oldStart),
//   oldEnd   = moment(oldEnd);
//   var duration = moment(this.end).diff(moment(this.start));
//   var midnightLimit = moment(this.end).startOf("day");
//   if(duration >= moment().add("d",1).diff(moment())){
//     this.allDay = true;
//     return;
//   }
//   if(oldStart.isSame(midnightLimit) ||oldEnd.isSame(midnightLimit)){
//     this.start = oldStart.format();
//     this.end = oldEnd.format();
//     return false;
//   }
//   if(moment(this.start).diff(midnightLimit) > midnightLimit.diff(this.end)){
//   // if(this.start < oldStart){

//     //we put the event to the next day
//     this.start = midnightLimit.format();
//     this.end = moment(this.start).add(duration).format();

//   }else{
//     //we put the event to the previous day
//     this.end = midnightLimit.format();
//     // this.end = moment(midnightLimit).subtract("s",1).format();
//     this.start = moment(this.end).subtract(duration).format();
//   }
// }

/**
 * whether the event is out of the given event
 * @param  {CalEvent}  event    to be compared with
 * @param  {Boolean}   same     (default false) if set to true, will exclude the case event are concatened.
 * @return {Boolean}
 */
// CalEvent.prototype.isOutOf = function(event,same){
//     var rtn ;
//     if(event.allDay){
//       rtn = ( (moment(this['end']).subtract("s",1).endOf("day").isAfter(moment(event['end']).endOf("day"))) ||
//               (moment(this['start']).isBefore(moment(event['start']).startOf("day")) )
//               );
//     }else{

//       rtn = ( moment(this['end']).isBefore(event['start']) ||
//               moment(this['start']).isAfter(event['end']));
//     }
//     if(same ===true) rtn = rtn || moment(this['end']).isSame(event['start'])
//                                || moment(this['start']).isSame(event['end']);
//     return  rtn;
// };
// CalEvent.prototype.isInsideOf = function(event){
//     return (moment(this['start']).isAfter(event['start']) &&
//             moment(this['end']).isBefore(event['end'])
//     );
// };

// CalEvent.prototype.isOneDayLong = function(){
//     return (moment(this["start"]).dayOfYear() == moment(this["end"]).dayOfYear());
// };


// CalEvent.prototype.isChild = function (parent){

//     var children = EventCollection.getChildren(parent, {concat:true,onlyEvent:true});
//     for(var i in children){
//         if(children[i].id === this.id){
//           return true;
//         }
//     }
//     return false;
// };


/**
 * add events to EventCollection.eventsToComputeBroCountRangeIndexes
 * @param CalEvent event to add
 * @param Object   opt   :
 *                  allBrosInDay=true : add all brothers too
 *
 *                    * add toppest non allday of the day in the cases that the event itself or its parent is an allDay event
 */
// CalEvent.prototype.computeCountRange = function(opt){
//     // console.log("#ComputeCountRange allBrosInDay "+this.id);
//     if(!opt)opt={}
//       var bros;
//     if(opt.allBrosInDay || this.allDay){
//       bros = calendar_events;
//       // var bros = event.getBros();
//       var dayToRender = {
//         start:moment(this.start).startOf('day')
//         ,end:moment(this.end).endOf('day')
//       };
//       // console.log("#ComputeCountRange allBrosInDay "+this.id,dayToRender);
//       if(Events[this.parent] && Events[this.parent].allDay){
//         bros = this.getNonAllDayBros();
//         // console.log("#ComputeCountRange parent is allDay",bros);
//         for(var i in bros){
//           var bro = bros[i];
//           if(!bro.isOutOf(dayToRender) || !bro.isOutOf(dayToRender) ){
//           addEvent(bro.id);
//           }
//         }
//       }else{
//         for(var i in bros){
//           var bro = bros[i];
//           if(!bro.isOutOf(dayToRender) || !bro.isOutOf(dayToRender) ){
//             if(opt.allBrosInDay !== true || this.isBroOf(bro) ){
//               addEvent(bro.id);
//             }
//           }
//         }
//       }
//     }
//     addEvent(this.id)
//     function addEvent(id){

//       if($.inArray(id, EventCollection.eventsToComputeBroCountRangeIndexes) === -1 && !Events[id].allDay && !Events[id].isInstant()) {
//         EventCollection.eventsToComputeBroCountRangeIndexes.push(id);
//         EventCollection.broCountRange[id] = {count:1,range:0,resCount:1,resRange:0};
//         // console.debug("#ComputeCountRange added "+id);
//       }
//       else{
//         // console.debug("#ComputeCountRange didn't add event "+id);
//       }
//     }
// }


// CalEvent.prototype.calculateWidth = function(seg, leftmost, availWidth, outerWidth, levelI, bottom, top, forward, dis,rtl){
//     var width = availWidth + (($calendar.fullCalendar('getView').getColWidth()/20));
//                 // + ( $calendar.fullCalendar('getView').name == "agendaDay" ? $calendar.fullCalendar('getView').getColWidth()/20 : 10 );
//     var height = bottom - top;
//     var left = leftmost;
//     var zindex = 8;

//     var Hmargin = 5;
//     var Wmargin = 2;

//     var isResView = $calendar.fullCalendar('getView')["name"] == "resourceDay";
//     try{

//         //go to the parent place
//         var parentId = this.parent,
//             count    = !isResView ? EventCollection.broCountRange[this.id].count : EventCollection.broCountRange[this.id].resCount,
//             range    = !isResView ? EventCollection.broCountRange[this.id].range : EventCollection.broCountRange[this.id].resRange
//             ;

//         while(!Events[parentId].allDay){
//             var parentCount   = !isResView ? EventCollection.broCountRange[parentId].count : EventCollection.broCountRange[parentId].resCount,
//                 parentRange   = !isResView ? EventCollection.broCountRange[parentId].range : EventCollection.broCountRange[parentId].resRange;

//             range += (parentRange*count);
//             count *= parentCount;

//             // width-=Wmargin*2;
//             left+=Wmargin;

//             if(moment(Events[parentId].start).isSame(moment(this.start))){
//               top    += Hmargin/2;
//               height -= Hmargin/2;
//             }
//             if(moment(Events[parentId].end).isSame(moment(this.end))){
//               height -= Hmargin;
//             }
//             // height -= (2*Hmargin);

//             zindex +=100

//             parentId = Events[parentId].parent;
//         }


//         width = width/count;
//         left = left+(width*range);


//         seg.outerWidth = width;
//         seg.left = left;
//         seg.outerHeight = height;
//         seg.top = top;
//         EventCollection.broCountRange[this.id].zindex = zindex;
//     }catch(e){
//       console.warn("broCountRange not computed for "+this.id)
//     }
// };

/*--------------------------------------------------------------------------------------------------*/
/*------------------------------------- hierarchy function -----------------------------------------*/
/*--------------------------------------------------------------------------------------------------*/


// CalEvent.prototype.dragChildren = function(){
//     var children = EventCollection.getChildren(this,{concat:true}),
//         draggedStart = moment(this['start']),
//         draggedProp = getProp(this.getElem()),
//         newdraggedProp,
//         diff,
//         childProp;

//     //update helper
//     $(this.getElem()).mousemove( function(ev){
//       newdraggedProp = getProp(this);
//       diff = {
//         x : (draggedProp.x - newdraggedProp.x) ,
//         y : (draggedProp.y - newdraggedProp.y)
//       };
//       if(diff.x !== 0  || diff.y !== 0 ){
//         draggedProp =  newdraggedProp;

//         $.each(children,function(i,child){
//           childProp = getProp(child.getElem());
//           $(child.getElem()).css("left",childProp.x-diff.x+"px")
//                        .css("top" ,childProp.y-diff.y+"px");
//         });
//       }
//     });
//     $(this.getElem()).mouseup(function(){
//       $(this).off("mousemove").off("mouseup")
//     })
// }

/**
 * child date has changed, update parent's one to fit
 * @param  {obj} event db event (with start,end,allDay,title...)
 */
// CalEvent.prototype.updateParentDate = function(){

//     updateParentDate(this);

//     function updateParentDate(event){
//         //check if the event has been dropped out of the parent

//         var parent = EventCollection.find(event.parent,{noSidebar:true});

//         if(!parent)return;

//         // //make main conf get a special treatment
//         // //to make it fit to its children date
//         if(parent.is_mainconfevent){
//           var newStart = moment(parent.start),
//               newEnd   = moment(parent.end);
//           if(moment(event.start).startOf("day").isBefore(moment(parent.start).startOf("day"))){
//             newStart = moment(event['start']) ;
//           }
//           if(moment(event.end).startOf("day").isAfter(moment(parent.end).startOf("day"))){
//             newEnd = moment(event['end']) ;
//           }
//           return EventCollection.updateMainConfEvent(newStart,newEnd);
//         }
//         if(event.isInsideOf(parent))return;

//         //event is out of parent
//         var Eduration = moment(event['end']).diff(event['start']);
//         var changed = false,
//             oldStart = event['start'],
//             oldEnd = event['end']
//             ;
//           console.log(event.id+" is not inside of "+parent)
//           // console.log("oldStart : "+oldStart)
//           // console.log("oldEnd : "+oldEnd)
//         //event start is before parent start
//         if(moment(event['start']).isBefore(parent['start'])){
//           parent['start'] = event['start'];

//           event['end'] = moment(event['start']).add(Eduration).format();
//           changed = true;
//         }
//         //event end is after parent end
//         if(moment(event['end']).isAfter(parent['end'])){
//           // event['end'] = parent['end'];
//           parent['end'] = event['end'];

//           event['start'] = moment(event['end']).subtract(Eduration).format();
//           changed = true;
//         }

//         if(changed){
//           // console.log("event['start'] : "+event['start'])
//           // console.log("event['end'] : "+event['end'])

//           // EventCollection.eventToRender = {id:parent["id"],oldStart:oldStart,oldEnd:oldEnd};
//           updateParentDate(parent);

//           parent.computeCountRange({allBrosInDay:true});
//           parent.renderForRefetch();
//           parent.persist();
//         }
//     }
// };

// when resized, affect recursively child date
// CalEvent.prototype.updateChildrenDate = function(){

//     updateChildrenDate(this);

//     function updateChildrenDate(event){
//         var children = EventCollection.getChildren(event, {concat:false,recursive:false,onlyEvent:true});
//         var Eduration = moment(event.end).diff(event.start);
//         for(var i in children)
//         {
//           var child = children[i];
//           var Cduration = moment(child.end).diff(child.start);
//           var changed =false;
//           if(child.isInsideOf(event))continue;
//             var childStart = child['start'],
//                 childEnd = child['end'];
//             //child start is before event start
//             if(moment(child['start']).isBefore(event['start']))
//             {
//               // event['start'] = parent['start'];
//               childStart = event['start'];

//               childEnd = moment(childStart).add(Cduration).format();
//               changed=true;
//             }
//             //child end is after child start
//             if(moment(child['end']).isAfter(event['end']))
//             {
//               // event['start'] = parent['start'];
//               childEnd = event['end'];

//               childStart = moment(childEnd).subtract(Cduration).format();
//               changed=true;
//             }

//             if(Cduration>Eduration){
//               childStart = event['start'];
//               childEnd = event['end'];
//               changed=true;
//             }
//             if(changed){
//               child['start'] = childStart;
//               child['end'] = childEnd;
//               updateChildrenDate(child,{allBrosInDay:true});
//               child.computeCountRange({allBrosInDay:true});
//               child.renderForRefetch();
//               child.persist();
//             }
//         }
//     }
// };

// CalEvent.prototype.isBroOf = function (bro){
//     if(this.id == mainConfEvent.id)return false;
//     var brosOfBroId = bro.getBrosId();
//     for(var i in brosOfBroId){
//       if(brosOfBroId[i] === this.id)
//         return true
//     }
//     return false;
// };

// CalEvent.prototype.getBros = function (){
//     if(this.id == mainConfEvent.id)
//         return [];

//     return EventCollection.getChildren(Events[this.parent], {recursive:false,concat:false, onlyEvent:true, noSidebar : true})
// };
// CalEvent.prototype.getBrosId = function (){
//     if(this.id == mainConfEvent.id)
//         return [];
//     var id = this.id;
//     // console.log("children of"+this.id,Events[this.parent].children)
//     return $(Events[this.parent].children).map(function(key,value){ if(value && value.id!=id){return value.id;}})

//     // return $(Events[this.parent].children).map(function(key,value){return value.id!=this.id?value.id:undefined;})
// };
// CalEvent.prototype.getNonAllDayBrosId = function (){
//     if(this.id == mainConfEvent.id)
//         return [];
//     var parent = Events[this.parent];
//     var rtn = [];
//     //add toppest non all days
//     if(parent.allDay){
//       // alert("add toppest non all days");
//       for (var i in Events){
//         if(Events[i].id==this.id || !Events[Events[i].parent])continue;
//         if(!Events[i].allDay && Events[Events[i].parent].allDay)
//           rtn.push(Events[i].id);
//       }
//       return rtn
//     }
//     var bros = parent.children;
//     for (var i in bros){
//       if(bros[i].id==this.id || !Events[bros[i].id])continue;
//       if(!Events[bros[i].id].allDay){
//         rtn.push(bros[i].id);
//         continue;
//       }
//     }
//     return rtn;
// };
// CalEvent.prototype.getNonAllDayBros = function (){
//     if(this.id == mainConfEvent.id)
//         return [];
//     var brosId = this.getNonAllDayBrosId();
//     var rtn = [];
//     for(var i in brosId){
//       rtn.push(Events[brosId[i]]);
//     }
//     return rtn;
// };
