/* query simpleSchedule
 * @param :  add event POST url controller
 * @return : createdEventId;
 */
function FormEventAdd(uri,eventForm){
    var id="";
    $.ajax({
        type: "POST",
        url:uri,
        async: false,
        data: eventForm.serialize(),
        success:function(data, textStatus, jqXHR) { 
            $(data).find('tr').each(function(){
                if($(this).children("th").text()=="Id"){ 
                    id = $(this).children("td").text();
            }})
        ;}
    });
    return id; 
} 

function FormXpropertyAdd(uri,xPropertyForm){  
	 $.post(uri, xPropertyForm.serialize());
} 
