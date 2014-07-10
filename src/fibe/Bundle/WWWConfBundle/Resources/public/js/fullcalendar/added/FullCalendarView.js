/**
 * Created by benoitddlp on 26/06/14.
 */

/**
 *
 * @constructor
 */
function FullCalendarView()
{
  this.render = render;
  this.on     = on; //listen to event

  var config   = getConfig(),
      eventArr = [],
      eventCallback = {} //register event with on
    ;

  $calendar.fullCalendar(config);
  $calendar.fullCalendar('changeView', "agendaWeek");

  // additional schedule controls : first day and refresh
  $('.fc-header-left')
    .append(
      $('<span class="fc-header-space"></span><button class="fc-button fc-state-default fc-corner-left fc-corner-right"> First day</button>')
        .click(function (e)
        {
          $calendar.fullCalendar('gotoDate', firstDay.year(), firstDay.month(), firstDay.date());
//          EventCollection.refetchEvents();
        })
//    ).append(
//    $('<span class="fc-header-space"></span><button class="fc-button fc-state-default fc-corner-left fc-corner-right"><span class="fa fa-refresh"></span></button>')
//      .click(function (e)
//      {
//        EventCollection.resetEvents();
//      })
    );

  function render(events)
  {
    eventArr = events;
    $calendar.fullCalendar('refetchEvents');
  }

  function on(eventName,callBack)
  {
    if (!eventCallback[eventName])
    {
      eventCallback[eventName]=[callBack];
    }
    else
    {
      eventCallback[eventName].push(callBack);
    }
  }
  function trigger(eventName,param)
  {
    if(!eventCallback[eventName])
    {
      console.log("no listener for event "+ eventName);
      return;
    }
    for (var i in eventCallback[eventName])
    {
      eventCallback[eventName][i](param);
    }
  }

  /**
   * fullcalendar config object
   */
  function getConfig(){
    return {
      header:
      {
        left: "prev,next today",
        center: "title",
        right: "month agendaWeek agendaDay,resourceDay"
      },
      //    buttonText: {resourceDay: currentRes},
      aspectRatio: 1.6,
      firstDay: 1,
      year: firstDay.year(),
      month: firstDay.month(),
      date: firstDay.date(),
      events: function (start, end, callback)
      { //get events
        callback(eventArr);
      },
      eventAfterAllRender: function ()
      {
        trigger("rendered");
      },
      eventAfterRender : function (event, element, view)
      {
        event.addFCUi(element);
      },

      //editing function requiring right
      editable: true,
      eventClick: function (calEvent, jsEvent, view)
      {
        trigger("edit_detail", calEvent);
      },
      selectable: true,
      selectHelper: true,
      select: function (start, end, allDay,ev,resourceObj)
      { //new event
        var e = {
          parent: {id: ""},
          start: start,
          end: moment(start).isSame(moment(end)) ? moment(start).add("hours", 1).format() : end,
          allDay: allDay
        };
        if (resourceObj){
          tmp['currentRes'] = currentRes;
          tmp['resourceId'] = resourceObj.id;
        }
        trigger("add", e);
      },
      eventResize: function (event, dayDelta, minuteDelta, revertFunc)
      {
        trigger("update", event);
      },
      droppable: true, // allows things to be dropped onto the calendar
      eventDrop: function (event)
      { //drop from calendar
        trigger("update", event);
      },
      eventDragStart: function (event, jsEvent, ui, view)
      {
        dragged = [ ui.helper[0], event ];
      },
//      eventDragStop: function (event, jsEvent, ui, view)
//      {
//        trigger("update", event);
//      }
    };
  };
}