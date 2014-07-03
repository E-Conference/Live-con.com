//var calendarOption =  function(){
//  return {
//        header: {
//            left: 'prev,next today',
//            center: 'title',
//            right: 'month,agendaWeek,agendaDay'
//        },
//        editable: true,
//        year: firstDay.year(),
//        month: firstDay.month(),
//        date: firstDay.date(),
//        //get events
//        eventClick: function(calEvent, jsEvent, view) {
//
//                $.ajax({
//                    url: op.updateUrl+"?id="+calEvent.id,
//                    success: function(doc) {
//                        $modal.modal("show");
//                        $modalBody.html(doc);
//                        bootstrapAlert("stop");
//
//                            // rerender if changed
//                        $modalBody.find("#eventForm,#delete-event-form").submit(function(){
//                          $modal.off('hidden.bs.modal').on('hidden.bs.modal', function () {
//                            $calendar.fullCalendar( 'refetchEvents' )
//                          })
//                        })
//                        // $modal.on('hidden.bs.modal', function () {
//                        //   // rerender if changed
//
//                        // })
//                    }
//                });
//                bootstrapAlert("info","edit <b>"+(calEvent['title'] || calEvent['name'])+"</b> request sent","Info : ","<i class='fa-2x fa fa-spinner fa-spin'></i>");
//
//        },
//        selectable: true,
//        selectHelper: true,
//        select: function(start, end, allDay) { //new event
//            $modalNewEvent.modal("show");
//
//            $modalNewEvent.find("form").off("submit").submit(function(e){
//              var title = $(this).find("#name").val();
//              if (title && parent) {
//                  var e = {
//                      title: title,
//                      parent: {id:""},
//                      children: [],
//                      start: start,
//                      end: moment(start).isSame(moment(end)) ? moment(start).add("hours",1).format() : end,
//                      allDay: allDay
//                  };
//                  $.post(
//                          op.quickAddUrl,
//                          formatDate($.extend( {} , e )),
//                          function(response) {
//                              bootstrapAlert("success","event <b>"+event['title']+"</b> has been well added");
//                              e.id=response.id;
//                              Events[e.id] = e;
//                              render(e);
//                              // $calendar.fullCalendar('renderEvent',e); // 3rd arg make the event "stick"
//                          },
//                          'json'
//                  );
//                  bootstrapAlert("info","add request sent","Info : ","<i class='fa-2x fa fa-spinner fa-spin'></i>");
//              }
//              $modalNewEvent.modal("hide");
//              return false;
//            });
//            $calendar.fullCalendar('unselect');
//        },
//        eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
//                updateParentDate(event);
//                updateChildrenDate(event);
//                render(event);
//                updateEventInDB(event);
//        },
//        eventDataTransform: function(data){return data;},
//        droppable: true, // this allows things to be dropped onto the calendar !!!
//        eventDrop : function(event){ //drop from calendar
//                updateEventInDB(event);
//                // render(event);
//        },
//        eventAfterAllRender: function( view ) {
//        //avoid repeating this function 10 times...
//        clearTimeout(repositionTimeout);
//        repositionTimeout=setTimeout(function(){
//            //update Event array
//
//            updateEventArray();
//            reposition(view);
//        },repositionTimeout);
//
//        },
//        drop: function(date, allDay) { //drop from sidebar
//
//            // retrieve the dropped element's stored Event Object
//            var originalEventObject = $(this).data('eventObject');
//
//            // we need to copy it, so that multiple events don't have a reference to the same object
//            var copiedEventObject = $.extend({}, originalEventObject);
//
//            // assign it the date that was reported
//            copiedEventObject.allDay = allDay;
//            copiedEventObject['start'] = date;
//            formatDate(copiedEventObject);
//
//            SetRecurDate(copiedEventObject);
//            deleteParent(copiedEventObject);
//
//            updateEventInDB(copiedEventObject );
//            // render the event on the calendar
//            // the third `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
//            render(copiedEventObject);
//
//            $(this).remove();
//        },
//        eventDragStart: function( event, jsEvent, ui, view ) {
//          dragged = [ ui.helper[0], event ];
//          setTimeout(function(){ //bug... event isn't yet updated
//            dragChildren(event, jsEvent, ui, view);
//          },1);//bug... event isn't yet updated
//
//        },
//        eventDragStop: function( event, jsEvent, ui, view ) {
//            dragged = [ ui.helper[0], event ];
//            //save children to db
//          setTimeout(function(){ //bug... event isn't yet updated
//            endDrag(event,ui.helper[0] );
//          },1);//bug... event isn't yet updated
//        },
//        eventRender : function( event, element, view ) {
//          setTooltip(element,event);
//
//        }
//    };
//}