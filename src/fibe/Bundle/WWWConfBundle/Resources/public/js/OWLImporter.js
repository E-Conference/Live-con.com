
 function run(url,callback,fallback){
     
         
        var completeConfRdfURL =  url;  
        
        
        
        $.ajax({
          url: completeConfRdfURL,   
          cache:false,
          success:function(completeConfRdf){
             
            
            if(completeConfRdf==undefined)
            {
                if(fallback!=undefined)fallback(); 
                return;
            }
            
            //check if it's rdf or owl file
            
            var isRdfFile=true; 
            $(completeConfRdf).children().children().each(function(){ 
                if(this.nodeName=="ObjectProperty" || this.nodeName=="NamedIndividual"){
                    isRdfFile=false;
                }
            }) 
            
            var events= []; 
            var locations= []; 
            var xproperties= [];
            var relations= [];
            var categories= [];
            var proceedings= [];
            var confName ;
             
            var defaultDate='now'; 
            //////////////////////////////////////////////////////////////////////////
            ///////////////////////  first round for locations  //////////////////////
            //////////////////////////////////////////////////////////////////////////
            console.log($(completeConfRdf).children().children());
            if(isRdfFile){
                $(completeConfRdf).children().children().each(function(index){
                    if(this.nodeName=="swc:MeetingRoomPlace"){
                      /////////////////////         LOCATION NODE         //////////////////
                      
                        var location={setDescription:""};
                        
                        $(this).children().each(function(){ 
                        
                            if(this.nodeName=="rdfs:comment"){
                            
                              location['setDescription']= format(location.setDescription+$(this).text())+", ";
                              
                            }else if(this.nodeName=="rdfs:label"){ 
                            
                              location['setName']=format($(this).text());
                              
                            }
                            
                        });
                        
                        location['uri']=$(this).attr('rdf:about');
                        
                        locations.push(location);
                    }
                    
                });
            }
            //console.log('-------locations--------');
            //console.log(locations);
            
            
            
            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////////////  Event  //////////////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            if(isRdfFile){
                $(completeConfRdf).children().children().each(function(index){
                    if( this.nodeName.indexOf("Event")!== -1 ) { 
                    /////////////////////         EVENT NODE         //////////////////
                           
                      doEvent(this); 
                      
                      //addRelation(this);
                      //console.log(events);
                      //console.log(relations);
                    }
                });
            }else
            {
                $(completeConfRdf).children().children().each(function(index,node){ 
                    if( node.nodeName=="NamedIndividual" ) {
                        var n = getNodeName(node);
                        if(n && n.indexOf("Event")!= -1){ 
                            doEvent(this); 
                        }
                    }
                });
            }
            
            //////////////////////////////////////////////////////////////////////////
            //////////////////////////////  relations  ///////////////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            var j=0;
            if(isRdfFile){
                $(completeConfRdf).children().children().each(function(index){
                    if( this.nodeName.indexOf("Event")!== -1 ) { 
                 
                      addRelation(this,j);
                      j++; 
                      //console.log(events);
                      //console.log(relations);
                    }  
                });
            }else //OWL FIX
            {
                $(completeConfRdf).children().children().each(function(index,node){ 
                    if( node.nodeName=="NamedIndividual" ) {
                        var n = getNodeName(node);
                        if(n && n.indexOf("Event")!= -1){ 
                            addRelation(node,j);
                      j++; 
                        }
                    }
                }); 
            } //OWL FIX
            //////////////////////////////////////////////////////////////////////////
            ///////////////////  fourth round for publication  ///////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            if(isRdfFile){
                $(completeConfRdf).children().children().each(function(index){
                    
                    if(this.nodeName=="swrc:InProceedings")
                    {
                        //for each proceeding
                        var uri = $(this).attr('rdf:about'); 
                        for (var i=0;i<xproperties.length;i++)
                        {
                        
                            if(xproperties[i]['setXValue']==uri){
                            // if we find the corresponding xproperty  
                                $(this).children().each(function()
                                {
                                    //we look for the title
                                    if(this.nodeName=="dce:title")
                                    {  
                                      //to finally store it in the setXKey !
                                      xproperties[i]['setXKey']=format($(this).text());
                                    }
                                });
                            }
                        }
                    }
                }); 
            }else //OWL FIX
            {
                $(completeConfRdf).children().children().each(function(index,node){ 
                    if( node.nodeName=="NamedIndividual" ) 
                    {
                        var n = getNodeName(node);
                        if(n && n.indexOf("InProceedings")!= -1)
                        {   
                            var eventUri;
                            $(node).children().each(function()
                            {
                                if(this.nodeName=="swc:relatedToEvent"  )
                                {
                                    eventUri = $(this).attr("rdf:resource");
                                }
                                
                            });
                            
                            if(eventUri){ 
                                eventId = getEventIdFromURI(eventUri);
 
                                //if we find a related event 
                                var xproperty= {}; 
                                xproperty['setCalendarEntity']=eventId;
                                xproperty['setXNamespace']="publication_uri";
                                xproperty['setXValue']=$(node).attr('rdf:about');
                                
                                //we look for the title
                                $(node).children().each(function(){
                                    if(this.nodeName=="dce:title" || this.nodeName=="rdfs:label"  || this.nodeName=="dc:title" )
                                    {
                                        //to finally store it in the setXKey !
                                        xproperty.setXKey=format($(node).text());
                                        console.log(xproperty.setXKey);
                                    }
                                });
                                xproperties.push(xproperty);
                            } 
                        }
                    }
                }); 
            } //OWL FIX
             
            //////////////////////////////////////////////////////////////////////////
            ////////////////////////  startAt less  ///////////////////////////
            //////////////////////////////////////////////////////////////////////////
             for(var i=0;i<events.length;i++){
                if(events[i]['setStartAt']!=undefined){
                
                        //alert(events[i]['setStartAt']);
                        
                        if(moment(events[i]['setStartAt']).dayOfYear() != moment(events[i]['setEndAt']).dayOfYear()){
                            events[i]['setStartAt'] = moment(events[i]['setStartAt']).hour(0).minute(0).second(0).millisecond(0).format('YYYY-MM-DDTHH:mm:ss Z');
                            events[i]['setEndAt'] = moment(events[i]['setEndAt']).hour(0).minute(0).second(0).millisecond(0).add('d', 1).format('YYYY-MM-DDTHH:mm:ss Z');
                        }
                        events[i]['setStartAt']= events[i]['setStartAt'] ;
                        events[i]['setEndAt']=events[i]['setEndAt'] ;
                    }else{
                        
                        events[i]['setStartAt']= defaultDate ;
                        events[i]['setEndAt']=moment().add('h', 1).format('YYYY-MM-DDTHH:mm:ss Z') ;
                    }
                }
            
            //////////////////////////////////////////////////////////////////////////
            ////////////////////////  INHERIT Child DATE  ///////////////////////////
            //////////////////////////////////////////////////////////////////////////
            /*var maxCllStck=1000;
            var ctn;
                    alert(events.length);
            for(var i=0;i<events.length;i++){
                if(events[i]['setStartAt']==undefined){ 
                    ctn=0;
                    var childDate= getChildDate(i);
                    if(childDate!=undefined){
                        
                        //multiple days events
                        if(moment(childDate.start).dayOfYear() != moment(childDate.end).dayOfYear()){
                            childDate.start = moment(childDate.start).hour(0).minute(0).second(0).millisecond(0);
                            childDate.end = moment(childDate.end).hour(0).minute(0).second(0).millisecond(0).add('d', 1);
                        }
                        //alert(moment(childDate.start)+", "+moment(childDate.end));
                        events[i]['setStartAt']=childDate.start;
                        events[i]['setEndAt']=childDate.end;
                    }else{
                    
                        var parentStartAt= getParentProp(i,'setStartAt');
                        //alert(parentStartAt);
                        if(parentStartAt){
                            events[i]['setStartAt']=parentStartAt;
                            events[i]['setEndAt']=getParentProp(i,'setEndAt');
                        }else{
                            events[i]['setStartAt']=events[0]['setStartAt'];
                            events[i]['setEndAt']=events[0]['setEndAt'];
                            console.log("no date found for : ")
                            console.log(events[i]);
                        } 
                    }
                }
            }  */
            
            send(); 
            //////////////////////////////////////////////////////////////////////////
            ////////////////////////  run() workflow end  ////////////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            
            //EVENT PARSE : CATCH START, END AT, LOCATION
            // THEN ADD 2 XPROPERTIES RELATED TO ITS EVENT AND PUBLICATION
            function doEvent(event){
                var rtnArray={}; 
                $(event).children().each(function(){
                
                    if(this.nodeName=="rdfs:label"){   // LABEL 
                        // replace & caractere ... 
                        rtnArray['setSummary']=format(this.textContent);
                        
                        
                    }else if(this.nodeName=="icaltzd:dtstart"){ // START AT 
                        rtnArray['setStartAt']=$(this).text(); 
                    }else if(this.nodeName=="ical:dtstart"){
                        $(this).children().each(function(){
                            if(this.nodeName=="ical:date"){
                                console.log(  this );
                                rtnArray['setStartAt']=$(this).text(); 
                            }
                        });
                        if(!rtnArray['setStartAt'])rtnArray['setStartAt']=$(this).text();
                        
                        
                    }else if(this.nodeName=="icaltzd:dtend"){   // END AT
                    
                        rtnArray['setEndAt']=$(this).text(); 
                        
                    }else if(this.nodeName=="ical:dtend"){   
                    
                        $(this).children().each(function(){
                            if(this.nodeName=="ical:date"){
                                rtnArray['setEndAt']=$(this).text(); 
                            }
                        });
                        if(!rtnArray['setEndAt'])rtnArray['setEndAt']=$(this).text();
                        
                    }else if(this.nodeName=="dce:description"){ // DESCRIPTION
                    
                        rtnArray['setDescription']=format(this.textContent);
                        
                    }else if(this.nodeName=="swrc:abstract"){ // ABSTRACT
                    
                        rtnArray['setComment']=format(this.textContent);
                        
                    }else if(this.nodeName=="swc:hasRelatedDocument"){ // RELATED PUBLICATION XPROP
                    
                        var xproperty= {}; 
                        xproperty['setCalendarEntity']=events.length;
                        xproperty['setXNamespace']="publication_uri";
                        xproperty['setXValue']=$(this).attr('rdf:resource');
                        xproperties.push(xproperty);  
                        
                    }else if(this.nodeName=="swc:hasLocation"){ // LOCATION //OWL fix
                        var locationName= $(this).attr('rdf:resource').replace('http://data.semanticweb.org/conference/eswc/2013/place/','');
                        
                        if(getLocationIdFromName(locationName)== -1 ){ locations.push({setDescription:"",setName:locationName});  }  
                        rtnArray.setLocation=getLocationIdFromName(locationName);
                        
                    }else if(this.nodeName=="icaltzd:location"){   // LOCATION
                     
                        var locationId = getLocationIdFromUri($(this).attr('rdf:resource'));
                        rtnArray['setLocation']=locationId;
                        
                    } else if(this.nodeName=="foaf:homepage"){   // url
                        
                        rtnArray['setUrl']=format($(this).attr('rdf:resource')); 
                    } 
                    
                });  
                
                  // EVENT CAT
                var catName = event.nodeName.split("swc:").join("").split("event:").join("");
                if(catName=="NamedIndividual")catName= getNodeName(event);
                var tmp=catName;
                if(tmp.split("Event").join("")!="")
                {
                    catName=tmp;
                }else //OWL fix
                {
                    catName = getNodeName(event).split("swc:").join("").split("event:").join("") ;
                    tmp=catName;
                    if(tmp.split("Event").join("")!="")
                    {
                        catName=tmp;
                        
                    }
                }  //OWL fix
                var catId = getCategoryIdFromName(catName);
                if(catId==undefined){ 
                  var category= {}; 
                  category['setName']=catName;
                  if(catName == "ConferenceEvent") confName = rtnArray['setSummary'];
                  categories.push(category);
                  catId = categories.length-1;
                }
                rtnArray['addCategorie']=catId;
                 
                events.push( rtnArray );
                
                  // EVENT XPROP
                var xproperty= {}; 
                xproperty['setCalendarEntity']=events.length-1;
                xproperty['setXNamespace']="event_uri";
                xproperty['setXValue']=$(event).attr('rdf:about');
                xproperties.push(xproperty);
                
                 
             }
             
            //ADD BOTH PARENT AND CHILD RELATION BETWEEN 2 EVENTS
            function addRelation(event,currentEventId){ 
                var found=false;
                $(event).children().each(function(){
                    if(this.nodeName=="swc:isSubEventOf"||this.nodeName=="swc:isSuperEventOf"){ 
                        var relatedToEventId=getEventIdFromURI($(this).attr('rdf:resource'));
                        if(relatedToEventId!=undefined && events[relatedToEventId]!=undefined ){
                        
                            var relationId = getRelationIdFromCalendarEntityId(currentEventId,relatedToEventId);
                            if(!relations[relationId]){
                              var relationType = this.nodeName.indexOf("swc:isSubEventOf")!== -1?"PARENT":"CHILD";
                              var relation= {}; 
                              relation['setCalendarEntity']=parseInt(relatedToEventId); 
                              relation['setRelationType']=relationType;
                              relation['setRelatedTo']=parseInt(currentEventId);
                              //console.log("----------   PUSHED    -----------");
                              //console.log(relation);
                              relations.push(relation);
                              
                              var relationType = (relationType=="PARENT"?"CHILD":"PARENT");
                              var relation= {};
                              relation['setCalendarEntity']=parseInt(currentEventId);
                              relation['setRelationType']=relationType;
                              relation['setRelatedTo']=parseInt(relatedToEventId);
                              //console.log(relation);
                              relations.push(relation); 
                              found=true;
                            } 
                        }else{
                          //console.log( event['setSummary']+", "+$(this).attr('rdf:resource'));
                          //console.log("Unknown parent");
                          
                        }
                    }  
                }); 
            }
            
            // GET THE INDEX OF A CATEGORY GIVEN ITS NAME
            function getCategoryIdFromName(name){
            
                for (var i=0;i<categories.length;i++){
                    //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
                    if(categories[i]['setName']==name){
                        return i; 
                    }
                }
                return undefined;
            }
            
            // GET THE INDEX OF A LOCATION GIVEN ITS URI
            function getLocationIdFromUri(uri){
            
                for (var i=0;i<locations.length;i++){
                    //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
                    if(locations[i]['uri']==uri){
                        return i; 
                    }
                }
                return undefined;
            } 
            
            function getLocationIdFromName(locationName){
                
                for (var i=0;i<locations.length;i++){
                    //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
                    if(locations[i]['setName']==locationName){
                        return i; 
                    }
                }
                return -1;
            }
            
            // GET THE INDEX OF AN EVENT GIVEN ITS URI
            function getEventIdFromURI(uri,show){ 
            
                for (var i=0;i<xproperties.length;i++){
                    //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
                    if(show)alert(xproperties[i]['setXValue']+" =?= "+uri);
                    if(xproperties[i]['setXValue']==uri){
                        return xproperties[i]['setCalendarEntity'];
                    }
                }
                return undefined;
            }
            
            // GET THE INDEX OF AN EVENT GIVEN ITS CHILD INDEX
            function getParentIndex(eventIndex){ 
                for(var i=0;i<relations.length;i++){  
                     if(relations[i]['setRelatedTo'] == eventIndex  && relations[i]['setRelationType'].indexOf("PARENT")!=-1 ){ 
                        return relations[i]['setCalendarEntity'];
                     }
                } 
                console.log("event "+ eventIndex +" has no parent");
                console.log(events[eventIndex]);
                return undefined;
            }
             
            
            // GET THE INDEX OF AN EVENT GIVEN ITS PARENT INDEX
            function getChildIndex(eventIndex){
                var rtrnArray=[];
                for(var i=0;i<relations.length;i++){  
                     if(relations[i]['setRelatedTo'] == eventIndex  && relations[i]['setRelationType'].indexOf("CHILD")!=-1 ){ 
                        rtrnArray.push( relations[i]['setCalendarEntity']);
                     }
                } 
                return rtrnArray.length==0 ? undefined : rtrnArray;
            }
             
            
            
            function getRelationIdFromCalendarEntityId(eventId,relatedToEventId){
                for(var i=0;i<relations.length;i++){  
                     
                     if(relations[i]['setCalendarEntity'] === eventId && relations[i]['setRelatedTo'] === relatedToEventId){  
                        return i;
                     }
                } 
                return undefined;
                
            }
            /*
            function getTrackEventFromInProceedingsUri(ProceedingsUri){
                
                for(var trackEvent in proceedings){  
                     
                    for(var j=0;j<proceedings[trackEvent].length;j++){  
                         
                         if(proceedings[trackEvent][j] === ProceedingsUri){  
                            return trackEvent;
                         }
                    } 
                } 
                return undefined;
                
            }
            */
            
            // GET RECURSIVELY PARENT PROP
            
            function getParentProp(eventIndex,parentProp)
            { 
                //ctn++;
                if(ctn>maxCllStck)
                    return undefined;
                
                var parentIndex=getParentIndex(eventIndex); 
                
                if( parentIndex == undefined )
                    return undefined;
                
                console.log("getParentProp(eventIndex,parentProp)");
                console.log(eventIndex);
                console.log(parentProp);
                console.log(events[parentIndex][parentProp]);
                if( events[parentIndex][parentProp])
                    return events[parentIndex][parentProp];
                    
                return getParentProp(parentIndex,parentProp);
            }
            
            
            // GET RECURSIVELY CHILD PROP
            
            function getChildDate(eventIndex){
                //ctn++;
                if(ctn>maxCllStck)
                    return undefined;
                
                var childIndexArr=getChildIndex(eventIndex);
                
                if( !childIndexArr )
                    return undefined;
                
                var start = moment('5010-10-20'); 
                var end = moment(defaultDate);
                for (var i=0;i<childIndexArr.length;i++){
                
                    var childId = childIndexArr[i];
                    if( events[childId]['setStartAt'] && moment(events[childId]['setStartAt']).isBefore(start)){
                      start = events[childId]['setStartAt'];
                    }
                    if( events[childId]['setEndAt'] && moment(events[childId]['setEndAt']).isAfter(end)){
                      end = events[childId]['setEndAt'];
                    }
                      
                }
                if(!moment(start).isSame('5010-10-20') && !moment(end).isSame(defaultDate)){
                  return {start:start,end:end};
                   
                
                } 
                
                return getChildrenDate(childIndexArr);
            }
            
            function getChildrenDate(childIndexArr){ 
                //ctn++;
                if(ctn>maxCllStck)
                    return undefined;
                     
                var start = moment('5010-10-20'); 
                var end = moment(defaultDate);
                for (var i=0;i<childIndexArr.length;i++){ 
                
                    var childId = childIndexArr[i];
                    var date = getChildDate(childId);
                    
                    if( date.start && moment(date.start).isBefore(start)){
                      start = date.start;
                    }
                    if( date.end && moment(date.end).isAfter(end)){
                      end = date.end;
                    }
                      
                }
                
                if(!moment(start).isSame('5010-10-20') && !moment(end).isSame(defaultDate)){
                  return {start:start,end:end};
                } 
                    
                    return undefined;
            }
            
            
            // SEND TO IMPORT PHP SCRIPT
            function send()
            {

                for (var i=0;i<locations.length;i++)
                {
                    delete locations[i]["uri"];
                }
                var dataArray={}; 
                dataArray['categories']=categories;
                dataArray['events']=events;
                dataArray['xproperties']=xproperties;
                dataArray['relations']=relations;  
                dataArray['locations']=locations;  
                    
                if(events.length<1 && xproperties.length<1 && relations.length<1 && locations.length<1)
                {
                    alert('nothing imported');
                    return;
                }
                console.log('---------finished---------' );
                console.log(dataArray);
                if(callback!=undefined)callback(dataArray,confName); 
           }
           
           
            function getNodeName(node){
                var uri=[]; 
                $(node).children().each(function(){ 
                    if(this.nodeName.indexOf("rdf:type")!== -1 ){    
                        uri.push($(this).attr('rdf:resource').split('#')[1]); 
                    } 
                });
                if(uri.length==1)
                {
                    return uri[0];
                }
                else if(uri.length==0)
                {
                    return undefined;
                }
                else if($.inArray('KeynoteTalk', uri) > -1)
                { 
                    return 'KeynoteEvent';
                }
                return uri;
                    
            }
           
       },
       error:function(){
                if(fallback)fallback();
       }
   });
}









function format(string){
    return string.replace(/(\r\n|\n|\r)/gm," ")//line break
                 .replace(/\s+/g," ")//spaces
                 .split(/\x26/).join("%26").split(/\x3D/).join("%3D")// & caract
                 ;
}





