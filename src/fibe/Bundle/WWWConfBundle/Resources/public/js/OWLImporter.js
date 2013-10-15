
function run(url,callback,fallback){ 
    var completeConfRdfURL =  url;    
    $.ajax({
        url: completeConfRdfURL,   
        cache:false,
        success:function(completeConfRdf){
             
            console.log(completeConfRdf);
            if(completeConfRdf==undefined )
            {
                if(fallback!=undefined)fallback("Empty response"); 
                return;
            } 
            
            var events= []; 
            var locations= []; 
            var xproperties= [];
            var relations= [];
            var categories= [];
            var proceedings= [];
            var persons= [];
            var themes= [];
            var confName ;
            
            var defaultDate='now';

            //map of   Uri (string) ,Object
            var objectMap = {};


            //format config

            var formatConfig = {
                'rdf': rdfConfig,
            }

 
            //check format (default : rdf)
            var format = 'rdf'; 








            var rootNode = formatConfig[format].getRootNode(completeConfRdf);
            var mappingConfig = formatConfig[format]; 

            //////////////////////////////////////////////////////////////////////////
            ///////////////////////  first round for locations  //////////////////////
            ////////////////////////////////////////////////////////////////////////// 

            rootNode.children().each(function(index,node){
                if( node.nodeName=="NamedIndividual" ) {
                    var n = getNodeName(node); 
                    if(n && n.indexOf(locationMapping.nodeName)!= -1){  
                        add(locations,mappingConfig.locationMapping,this); 
                    }
                }
            }); 
 
            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////////////  Event  //////////////////////////////
            //////////////////////////////////////////////////////////////////////////
         
            rootNode.children().each(function(index,node){
                if( node.nodeName=="NamedIndividual" ){
                    var n = getNodeName(node);
                    if(n && n.indexOf(eventMapping.nodeName)!= -1){
                        add(events,mappingConfig.eventMapping,this); 
                    }
                }
            });  
         
            //////////////////////////////////////////////////////////////////////////
            //////////////////////////////////  Person  //////////////////////////////
            //////////////////////////////////////////////////////////////////////////
             
            rootNode.children().each(function(index,node){ 
                if( node.nodeName=="NamedIndividual" ) {
                    var n = getNodeName(node); 
                    if(n && n.indexOf(personMapping.nodeName)!= -1){  
                        add(persons,mappingConfig.personMapping,this); 
                    }
                }
            }); 
            
            //////////////////////////////////////////////////////////////////////////
            //////////////////////////////  relations  ///////////////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            var j=0;
            $(completeConfRdf).children().children().each(function(index,node){ 
                if( node.nodeName=="NamedIndividual" ) {
                    var n = getNodeName(node);
                    if(n && n.indexOf("Event")!= -1){ 
                        addRelation(node,j);
                  j++; 
                    }
                }
            }); 
            //////////////////////////////////////////////////////////////////////////
            ///////////////////  fourth round for publication  ///////////////////////
            //////////////////////////////////////////////////////////////////////////
            
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
                                }
                            });
                            xproperties.push(xproperty);
                        } 
                    }
                }
            }); 
             
            //////////////////////////////////////////////////////////////////////////
            //////////////////////// fifth round startAt less  ///////////////////////
            //////////////////////////////////////////////////////////////////////////
            //startat less get the 'now' date
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
                    delete events[i]['setParent'];
                    events[i]['setEndAt']=moment().format('YYYY-MM-DDTHH:mm:ss Z') ;
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
            
            
            // SEND TO IMPORT PHP SCRIPT 
            for (var i=0;i<locations.length;i++)
            {
                delete locations[i]["uri"];
            }
            var dataArray={}; 
            dataArray['locations']=locations;  
            dataArray['categories']=categories;
            dataArray['persons']=persons;   
            dataArray['themes']=themes;   
            dataArray['events']=events;
            dataArray['xproperties']=xproperties; 
            console.log('---------finished---------' );
            console.log(dataArray); 
            if(events.length<1 && xproperties.length<1 && relations.length<1 && locations.length<1 && persons.length<1&& themes.length<1)
            {
                if(fallback!=undefined)fallback("bad format"); 
                return;
            }
            if(callback!=undefined)callback(dataArray,confName);  

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////  run() workflow end  //////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            /**
             *          add
             *          
             * parse config files
             * @param {array} addArray      the array to populate
             * @param {object} mapping      mapping object (defined in config files)
             * @param {dom elem} node       the xml dom element from the import file
             */
            function add(addArray,mapping,node){
                var rtnArray = {};
                var key = $(node).attr("rdf:about");
                $(node).children().each(function(){ 
                    if(mapping.label[this.nodeName]){
                        if(mapping.label[this.nodeName].action){ 
                            mapping.label[this.nodeName].action(this);
                        }
                        if(mapping.label[this.nodeName].setter){
                            var val = this.textContent;
                            if(mapping.label[this.nodeName].format){   
                                val = mapping.label[this.nodeName].format(this);
                            }
                            val = mapping.label[this.nodeName].setter === false ? val : typeof val === 'string' ? format(val) : val ;
                            if(mapping.label[this.nodeName].multiple === true){
                                if(!rtnArray[mapping.label[this.nodeName].setter])
                                    rtnArray[mapping.label[this.nodeName].setter]={};
                                var index = Object.size(rtnArray[mapping.label[this.nodeName].setter]);
                                rtnArray[mapping.label[this.nodeName].setter][index] = val;
                            }else{
                                rtnArray[mapping.label[this.nodeName].setter]= val;
                            }
                        } 
                    }
                });
                if(mapping.action){
                    mapping.action(node,rtnArray); 
                }
                if(Object.size(rtnArray) > 0){
                    objectMap[key] = rtnArray;
                    addArray.push( rtnArray );
                }
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
                              events[currentEventId]['setParent'] = parseInt(relatedToEventId);
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
            
            // GET THE INDEX OF A CATEGORY GIVEN BY ITS NAME
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
            
            function getThemeIdFromName(themeName){
                
                for (var i=0;i<themes.length;i++){
                    //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
                    if(themes[i]['setname']==themeName){
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
                // console.log("event "+ eventIndex +" has no parent");
                // console.log(events[eventIndex]);
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
                
                // console.log("getParentProp(eventIndex,parentProp)");
                // console.log(eventIndex);
                // console.log(parentProp);
                // console.log(events[parentIndex][parentProp]);
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
            
            
           
           
            function getNodeName(node){
                var uri=[]; 
                $(node).children().each(function(){ 
                    if(this.nodeName.indexOf("rdf:type")!== -1 ){
                        if($(this).attr('rdf:resource').indexOf("#")!== -1 ){ 
                            uri.push($(this).attr('rdf:resource').split('#')[1]); 
                        }else{
                            var nodeName = $(this).attr('rdf:resource').split('/'); 
                            uri.push(nodeName[nodeName.length-1]);  
                        }
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
       //get import file ajax fallback
       error:function(a,b,c){
            console.log(a)
            console.log(b)
            console.log(c)
            if(fallback)fallback('Request failed giving this error <b>"'+c+'"</b> ');
       }
   });
} // end run()



function format(string){
    // return string ;
    // return $('<div/>').text(string).html();
        // return unescape(encodeURIComponent(string));  

    // console.log("format:",string)
    return string.split(/(\r\n|\n|\r)/gm).join(" ")//line break
                 .split(/\s+/g).join(" ")//spaces
                 .split(/\x26/).join("%26")//spaces
                 .split(/\x3D/).join("%3D")// & caract
                 .split(/\ue00E9/).join("e")// & caract
                 ;
    // return string.replace(/(\r\n|\n|\r)/gm," ")//line break
    //              .replace(/\s+/g," ")//spaces
    //              .split(/\x26/).join("%26")//spaces
    //              .split(/\x3D/).join("%3D")// & caract
    //              .split(/\ue00E9/).join("e")// & caract
    //              ;
}


 Object.size = function(obj) {
    var size = 0, key;
    for (key in obj)if (obj.hasOwnProperty(key)) size++;
    return size;
};