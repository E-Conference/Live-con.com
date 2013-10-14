

var events,
    locations,
    xproperties,
    relations,
    categories,
    proceedings,
    persons,
    themes,
    proceedings,
    keywords,
    organizations,
    relations;

var mappingConfig,
    objectMap;

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
            var confName ;
            events        = [],
            locations     = [],
            xproperties   = [],
            relations     = [],
            categories    = [],
            proceedings   = [],
            persons       = [],
            themes        = [],
            proceedings   = [],
            keywords      = [],
            organizations = [],
            objectMap     = {};
            
            var defaultDate='now';

            //map of   Uri (string) ,Object 


            //format config

            var formatConfig = { 
                'ocs': ocsConfig,
            }

 
            //check format (default : rdf)
            var format = undefined;   


            for (var i in formatConfig){
                if(formatConfig[i].checkFormat(completeConfRdf) === true){
                    console.log("format found ! :" + i);
                    format = i;
                }
            } 

            //set format and config 
            var rootNode = format !== undefined ? formatConfig[format].getRootNode(completeConfRdf) : rdfConfig.getRootNode(completeConfRdf);
            mappingConfig =  format !== undefined ? formatConfig[format] : rdfConfig;

            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////  pre processing  /////////////////////////////
            ////////////////////////////////////////////////////////////////////////// 

            if(mappingConfig.action!==undefined){
                mappingConfig.action(rootNode);
            } 
            //////////////////////////////////////////////////////////////////////////
            ///////////////////////  first round for locations  //////////////////////
            ////////////rootNode////////////////////////////////////////////////////////////// 
            console.log(rootNode)
            rootNode.children().each(function(index,node){ 
                    var n = getNodeName(node); 
                    if(n && n.indexOf(mappingConfig.locationMapping.nodeName)!= -1){  
                        add(locations,mappingConfig.locationMapping,this); 
                    }
            }); 
 
            //////////////////////////////////////////////////////////////////////////
            ////////////////////////////  Organization  //////////////////////////////
            //////////////////////////////////////////////////////////////////////////
             
            rootNode.children().each(function(index,node){ 
                    var n = getNodeName(node); 
                    if(n && n.indexOf(mappingConfig.organizationMapping.nodeName)!= -1){  
                        add(organizations,mappingConfig.organizationMapping,this); 
                    }
            });
         
            //////////////////////////////////////////////////////////////////////////
            //////////////////////////////////  Person  //////////////////////////////
            //////////////////////////////////////////////////////////////////////////
             
            rootNode.children().each(function(index,node){ 
                    var n = getNodeName(node); 
                    if(n && n.indexOf(mappingConfig.personMapping.nodeName)!= -1){  
                        add(persons,mappingConfig.personMapping,this); 
                    }
            });


            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////////  publication  ////////////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            rootNode.children().each(function(index,node){ 
                    var n = getNodeName(node); 
                    if(n && n.indexOf(mappingConfig.proceedingMapping.nodeName)!= -1){  
                        add(proceedings,mappingConfig.proceedingMapping,this);  
                    }
            }); 
 
            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////////////  Event  //////////////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            rootNode.children().each(function(index,node){
                    var n = getNodeName(node); 
                    if(n && n.indexOf(mappingConfig.eventMapping.nodeName)!= -1){
                        add(events,mappingConfig.eventMapping,this); 

                    }
            });  
            
            //////////////////////////////////////////////////////////////////////////
            /////////////////////////////  relationships  ////////////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            var j=0;
            rootNode.children().each(function(index,node){ 
                    var n = getNodeName(node); 
                    if(n && n.indexOf(mappingConfig.relationMapping.nodeName)!= -1){  
                        add(relations,mappingConfig.relationMapping,this,{eventId:j});  
                        j++;
                }
            });
            // $(completeConfRdf).children().children().each(function(index,node){ 
            //     if( node.nodeName=="NamedIndividual" ) {
            //         var n = getNodeName(node);
            //         if(n && n.indexOf("Event")!= -1){
            //             // j is supposed to be the event index inside the events array
            //             addRelation(node,j);
            //             j++;
            //         }
            //     }
            // });
              
            
            // $(completeConfRdf).children().children().each(function(index,node){ 
            //     if( node.nodeName=="NamedIndividual" ) 
            //     {
            //         var n = getNodeName(node);
            //         if(n && n.indexOf("InProceedings")!= -1)
            //         {   
            //             var eventUri;
            //             $(node).children().each(function()
            //             {
            //                 if(this.nodeName=="swc:relatedToEvent"  )
            //                 {
            //                     eventUri = $(this).attr("rdf:resource");
            //                 }
                            
            //             });
                        
            //             if(eventUri){ 
            //                 eventId = getEventIdFromURI(eventUri);

            //                 //if we find a related event 
            //                 var xproperty= {}; 
            //                 xproperty['setCalendarEntity']=eventId;
            //                 xproperty['setXNamespace']="publication_uri";
            //                 xproperty['setXValue']=$(node).attr('rdf:about');
                            
            //                 //we look for the title
            //                 $(node).children().each(function(){
            //                     if(this.nodeName=="dce:title" || this.nodeName=="rdfs:label"  || this.nodeName=="dc:title" )
            //                     {
            //                         //to finally store it in the setXKey !
            //                         xproperty.setXKey=str_format($(node).text());
            //                     }
            //                 });
            //                 xproperties.push(xproperty);
            //             } 
            //         }
            //     }
            // }); 
             
            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////// startAt less  //////////////////////////////
            //////////////////////////////////////////////////////////////////////////
             for(var i=0;i<events.length;i++){
                if(events[i]['setStartAt']!=undefined){
                    
                    if(moment(events[i]['setStartAt']).dayOfYear() != moment(events[i]['setEndAt']).dayOfYear()){
                        events[i]['setStartAt'] = moment(events[i]['setStartAt']).hour(0).minute(0).second(0).millisecond(0).format('YYYY-MM-DDTHH:mm:ss Z');
                        events[i]['setEndAt'] = moment(events[i]['setEndAt']).hour(0).minute(0).second(0).millisecond(0).add('d', 1).format('YYYY-MM-DDTHH:mm:ss Z');
                    }
                    events[i]['setStartAt']= events[i]['setStartAt'] ;
                    events[i]['setEndAt']=events[i]['setEndAt'] ;
                }else{
                    
                    //startat less get the 'now' date
                    delete events[i]['setParent'];
                    events[i]['setEndAt'] = events[i]['setStartAt'] = defaultDate;
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
            dataArray['keywords']=keywords; 
            dataArray['proceedings']=proceedings; 
            dataArray['organizations']=organizations; 
            console.log('---------finished---------' );
            console.log(dataArray); 
            if(events.length<1 && xproperties.length<1 && relations.length<1 && locations.length<1 && persons.length<1&& themes.length<1)
            {
                if(fallback!=undefined)fallback("bad format"); 
                return;
            }
            if(callback!=undefined)callback(dataArray,confName);   
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




/**  function add() parse config files
 *
 * @param {array} addArray      the array to populate
 * @param {object} mapping      mapping object (defined in config files)
 * @param {dom elem} node       the xml dom element from the import file
 * @param {object} arg          arg for overide function
 */
function add(addArray,mapping,node,arg){

    //to override this method, write an "overide : function(){...}" in the mapping file of the function. 
    if(mapping.overide!==undefined){
        return mapping.overide(node,arg);
    }
    //unwrapped if needed
    if(mapping.wrapped===true){ 
        $(node).children().each(function(){
            process(addArray,mapping,this,arg);
        });
    }else{
        process(addArray,mapping,node,arg); 
    }

    function process(addArray,mapping,node,arg){
        var rtnArray = {};
        var key = mappingConfig.getNodeKey(node);
        console.log("processing : "+key);
        $(node).children().each(function(){ 
            if(mapping.label[this.nodeName]!== undefined){
                
                if(mapping.label[this.nodeName].setter){
                    var nodeName = this.nodeName;
                    //unwrapped if needed
                    if(mapping.label[this.nodeName].wrapped === true){
                        $(this).children().each(function(){ 
                            set(mapping,nodeName,this); 
                        });
                    }else{
                        set(mapping,nodeName,this); 
                    }
                }
            }else{ 
                console.warn("no mapping for : "+node.nodeName+"/"+ this.nodeName);
            }
        });
             
        if(mapping.action){
            mapping.action(node,rtnArray); 
        }
        if(Object.size(rtnArray) > 0){
            objectMap[key] = rtnArray;
            addArray.push( rtnArray );
        }

        function set(mapping,nodeName,node){
            console.log("set : "+nodeName+", "+node.nodeName);
            var val = node.textContent;
                
            // pre processing
            if(mapping.label[nodeName].action){
                mapping.label[nodeName].action(node,rtnArray); 
            }

            if(mapping.label[nodeName].format){   
                val = mapping.label[nodeName].format(node);
            }
            val = mapping.label[nodeName].setter === false ? val : typeof val === 'string' ? str_format(val) : val ;
            if(mapping.label[nodeName].multiple === true){
                if(!rtnArray[mapping.label[nodeName].setter])
                    rtnArray[mapping.label[nodeName].setter]={};
                var index = Object.size(rtnArray[mapping.label[nodeName].setter]);
                rtnArray[mapping.label[nodeName].setter][index] = val;
            }else{
                rtnArray[mapping.label[nodeName].setter]= val;
            }

        }
    }
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
        if(themes[i]['setLibelle']==themeName){
            return i; 
        }
    }
    return -1;
}

function getKeywordIdFromName(keywordName,debug){
    
    for (var i=0;i<keywords.length;i++){
        //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
        if(keywords[i]['setLibelle']==keywordName){
            return i; 
        }
    }
    if (debug){

        console.log(keywords);
        alert("keyword "+keywordName+" not found");
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
        return node.nodeName;
    }
    else if($.inArray('KeynoteTalk', uri) > -1)
    { 
        return 'KeynoteEvent';
    }
    return undefined;
        
}

function str_format(string){
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