

var conference,
    events,
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
    relations,
    roles;

var notImportedLog,
    importedLog;

var mappingConfig,
    objectMap;

function run(url,callback,fallback){ 
    var completeConfRdfURL =  url;    
    $.ajax({
        url: completeConfRdfURL,   
        cache:false,
        dataType:"xml",
        success:function(completeConfRdf){
             
            console.log(completeConfRdf);
            if(completeConfRdf==undefined )
            {
                if(fallback!=undefined)fallback("Empty response"); 
                return;
            } 

            var confName ;
            events         = [],
            locations      = [],
            xproperties    = [],
            relations      = [],
            categories     = [],
            proceedings    = [],
            persons        = [],
            themes         = [],
            proceedings    = [],
            keywords       = [],
            organizations  = [],
            roles          = [],
            conference     = {},
            notImportedLog = {},
            importedLog    = {},
            objectMap      = {};
            
            var defaultDate='now';

            //add custom config here (default : rdf)
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
            mappingConfig =  format !== undefined 
                                    ? formatConfig[format] 
                                    : rdfConfig;

            var rootNode = mappingConfig.getRootNode(completeConfRdf);
            var parseItemOrder =  mappingConfig.getParseItemOrder();
            console.log(rootNode)

            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////  pre processing  /////////////////////////////
            //////////////////////////////////////////////////////////////////////////  
            if(mappingConfig.action!==undefined){
                mappingConfig.action(rootNode);
            }



            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////  items Processing  ///////////////////////////
            //////////////////////////////////////////////////////////////////////////
 
            for (var i in parseItemOrder){ 
                var itemMapping = mappingConfig[i];
                var addArray = parseItemOrder[i];
                rootNode.children().each(function(index,node){
                    var n = mappingConfig.getNodeName(node);
                    if(n && n.indexOf(itemMapping.nodeName)!= -1){
                        add(addArray,itemMapping,this,{name:n}); 
                    }
                }); 
            }

            //////////////////////////////////////////////////////////////////////////
            /////////////////////////////  relationships  ////////////////////////////
            //////////////////////////////////////////////////////////////////////////
            
            var j=0;
            rootNode.children().each(function(index,node){ 
                var n = mappingConfig.getNodeName(node); 
                if(n && n.indexOf(mappingConfig.relationMapping.nodeName)!= -1){  
                    add(relations,mappingConfig.relationMapping,this,{name:n,eventId:j});  
                    j++;
                }
            });
            // $(completeConfRdf).children().children().each(function(index,node){ 
            //     if( node.nodeName=="NamedIndividual" ) {
            //         var n = mappingConfig.getNodeName(node);
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
            //         var n = mappingConfig.getNodeName(node);
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
            
            // setRecursiveDates();
            //allDay events
            for(var i=0;i<events.length;i++){
                var event = events[i];
                if(event['setStartAt']!=undefined){
                    
                    //allDay events
                    if(moment(event['setStartAt']).dayOfYear() != moment(event['setEndAt']).dayOfYear()){
                        event['setStartAt'] = moment(event['setStartAt']).hour(0).minute(0).second(0).millisecond(0).format('YYYY-MM-DDTHH:mm:ss Z');
                        event['setEndAt'] = moment(event['setEndAt']).hour(0).minute(0).second(0).millisecond(0).add('d', 1).format('YYYY-MM-DDTHH:mm:ss Z');
                    }

                }
                // else
                // {
                //     event['setEndAt'] = event['setStartAt'] = defaultDate;
                // } 
                else{
                    //try to get children date
                    var childrenDate = getChildrenDate(i);
                    if(childrenDate)
                    {
                        event['setEndAt']   = childrenDate.end; 
                        event['setStartAt'] = childrenDate.start; 
                    }else
                    {
                        delete event['setParent'];
                        event['setEndAt'] = event['setStartAt'] = defaultDate;
                    } 
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
            dataArray['conference']=conference;  
            dataArray['locations']=locations;  
            dataArray['categories']=categories;
            dataArray['persons']=persons;   
            dataArray['themes']=themes;   
            dataArray['events']=events; 
            // dataArray['xproperties']=xproperties; 
            dataArray['keywords']=keywords; 
            dataArray['proceedings']=proceedings; 
            dataArray['organizations']=organizations; 
            console.log('---------finished---------' );
            console.log(dataArray);
            console.log(roles)

            var empty = true;
            for (var i in dataArray){
                if(empty == true && dataArray[i] && dataArray[i].length>0)empty = false;
            }
            if(empty == true){
                if(fallback!=undefined)fallback("nothing found... please check your file !"); 
                return;
            }

            if(callback!=undefined)callback(dataArray,confName);   

            //log not imported properties

            console.log("Imported properties : ");
            console.log(importedLog);

            console.log("not imported properties : ");
            console.log(notImportedLog);
       },
       //get import file ajax fallback
       error:function(a,b,c){
            console.log(a)
            console.log(b)
            console.log(c)
            if(fallback)fallback('Request failed giving this error <b>"'+c+'"</b> ');
       }
   }); // end ajax
} // end run()




/**  function add() : process node given the config file 
 *
 * @param {array} addArray      the array to populate
 * @param {object} mapping      mapping object (defined in config files)
 * @param {dom elem} node       the xml dom element from the import file
 * @param {object} arg          arg for overide function
 */
function add(addArray,mapping,node,arg){

    //to override this method, write an "overide : function(){...}" in the mapping file of the function. 
    if(mapping.overide!==undefined){
        return mapping.overide(node,addArray,arg);
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
                            set(mapping,nodeName,this,arg); 
                        });
                    }else{
                        set(mapping,nodeName,this,arg); 
                    }
                }
            }else{ 
                var mappingLake = arg.name+"/"+ this.nodeName;
                if(!notImportedLog[mappingLake])
                    notImportedLog[mappingLake] = undefined
            }
        });
             
        if(mapping.action){
            mapping.action(node,rtnArray); 
        }
        if(Object.size(rtnArray) > 0){
            objectMap[key] = rtnArray; 
            addArray.push( rtnArray );
        }

        function set(mapping,nodeName,node,arg){
            var mappingStr = arg.name+"/"+ nodeName;
            if(!importedLog[mappingStr])
                importedLog[mappingStr] = undefined
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

// utils function to get arrays index
function getCategoryIdFromName(name){
    name = str_format(name);

    for (var i=0;i<categories.length;i++){
        //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
        if(categories[i]['setName']==name){
            return i; 
        }
    }
    return undefined;
}

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
    locationName = str_format(locationName);
    for (var i=0;i<locations.length;i++){
        //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
        // alert(locationName+" == "+locations[i]['setName']+"\n"+(locations[i]['setName']==locationName ?"true":"false"));
        if(locations[i]['setName']==locationName){
            return i; 
        }
    }
    return -1;
}

function getThemeIdFromName(themeName){
    themeName = str_format(themeName);
    
    for (var i=0;i<themes.length;i++){
        //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
        if(themes[i]['setName']==themeName){
            return i; 
        }
    }
    return -1;
}

function getKeywordIdFromName(keywordName,debug){
    keywordName = str_format(keywordName);
    
    for (var i=0;i<keywords.length;i++){
        //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
        if(keywords[i]['setName']==keywordName){
            return i; 
        }
    }
    if (debug){

        console.log(keywords);
        alert("keyword "+keywordName+" not found");
    }
    return -1;
}

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

// function getParentIndex(eventIndex){
//     for(var i=0;i<relations.length;i++){
//          if(relations[i]['setRelatedTo'] == eventIndex  && relations[i]['setRelationType'].indexOf("PARENT")!=-1 ){
//             return relations[i]['setCalendarEntity'];
//          }
//     }
//     // console.log("event "+ eventIndex +" has no parent");
//     // console.log(events[eventIndex]);
//     return undefined;
// }
 

// function getChildIndex(eventIndex){
//     var rtrnArray=[];
//     for(var i=0;i<relations.length;i++){  
//          if(relations[i]['setRelatedTo'] == eventIndex  && relations[i]['setRelationType'].indexOf("CHILD")!=-1 ){ 
//             rtrnArray.push( relations[i]['setCalendarEntity']);
//          }
//     } 
//     return rtrnArray.length==0 ? undefined : rtrnArray;
// }
 


// function getRelationIdFromCalendarEntityId(eventId,relatedToEventId){
//     for(var i=0;i<relations.length;i++){  
         
//          if(relations[i]['setCalendarEntity'] === eventId && relations[i]['setRelatedTo'] === relatedToEventId){  
//             return i;
//          }
//     } 
//     return undefined;
    
// } 
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

// function getParentProp(eventIndex,parentProp)
// { 
//     //ctn++;
//     if(ctn>maxCllStck)
//         return undefined;
    
//     var parentIndex=getParentIndex(eventIndex); 
    
//     if( parentIndex == undefined )
//         return undefined;
    
//     // console.log("getParentProp(eventIndex,parentProp)");
//     // console.log(eventIndex);
//     // console.log(parentProp);
//     // console.log(events[parentIndex][parentProp]);
//     if( events[parentIndex][parentProp])
//         return events[parentIndex][parentProp];
        
//     return getParentProp(parentIndex,parentProp);
// }


function setRecursiveDates(){


    var done = [];
    for(var i=0;i<events.length;i++)
    {
        var event = events[i]; 
        var parentId = event.setParent;
        if(parentId && $.inArray( parentId, done ) ){
            var parent = events[parentId];
            if(!parent.mainConferenceEvent)
            {
                var childrenDate = getChildrenDate(parentId); 
                parent["setStartAt"] = childrenDate.start;
                parent["setEndAt"] = childrenDate.end; 
            }
        }
    }

    // var lowestChildren = getLowestChildren();
    // console.log(lowestChildren);
    // var done = [];
    // for(var i=0;i<lowestChildren.length;i++)
    // {
    //     var event = lowestChildren[i]; 
    //     var parentId = event.setParent;
    //     if(parentId && $.inArray( parentId, done ) ){
    //         var parent = events[parentId];
    //         if(!parent.mainConferenceEvent)
    //         {
    //             var childrenDate = getChildrenDate(parentId); 
    //             parent["setStartAt"] = childrenDate.start;
    //             parent["setEndAt"] = childrenDate.end;
    //             done.push(parentId);
    //         }
    //     }
    // }
}

function getLowestChildren(){

    var rtn = [];
    for(var i=0;i<events.length;i++)
    {
        var event = events[i];
        var children = getChildren(i);
        if(children.length == 0)
        { 
            rtn.push(event)
        } 
    }
    return rtn;
}

function getChildren(eventIndex)
{
    var rtn = [];
    for (var i in events)
    {
        var child = events[i];
        if(child.setParent == eventIndex)
        { 
            rtn.push({event:child,id:i});
        } 
    }
    return rtn;
}

function getChildrenDate(eventIndex)
{
    var start = moment('5010-10-20'); 
    var end   = moment('1900-10-20');
    for (var i in events)
    {
        var child = events[i];
        if(child.setParent == eventIndex)
        { 
            if( child['setStartAt'] && moment(child['setStartAt']).isBefore(start)){
              start = moment(child['setStartAt']);
            }
            if( child['setEndAt'] && moment(child['setEndAt']).isAfter(end)){
              end = moment(child['setEndAt']);
            }
        } 
    }
    if(start.isSame(moment('5010-10-20')) || end.isSame(moment('1900-10-20')) )return undefined;
    return {start:start.format(),end:end.format()}
}
// function getChildDate(eventIndex){ 

//     var childIndexArr=getChildIndex(eventIndex);
    
//     if( !childIndexArr )
//         return undefined;
    
//     var start = moment('5010-10-20'); 
//     var end = moment(defaultDate);
//     for (var i=0;i<childIndexArr.length;i++){
    
//         var childId = childIndexArr[i];
//         if( events[childId]['setStartAt'] && moment(events[childId]['setStartAt']).isBefore(start)){
//           start = events[childId]['setStartAt'];
//         }
//         if( events[childId]['setEndAt'] && moment(events[childId]['setEndAt']).isAfter(end)){
//           end = events[childId]['setEndAt'];
//         }
          
//     }
//     if(!moment(start).isSame('5010-10-20') && !moment(end).isSame(defaultDate)){
//       return {start:start,end:end};
       
    
//     }
    
//     return getChildrenDate(childIndexArr);
// }

// function getChildrenDate(childIndexArr){  
//     var start = moment('5010-10-20'); 
//     var end = moment(defaultDate);
//     for (var i=0;i<childIndexArr.length;i++){ 
    
//         var childId = childIndexArr[i];
//         var date = getChildDate(childId);
        
//         if( date.start && moment(date.start).isBefore(start)){
//           start = date.start;
//         }
//         if( date.end && moment(date.end).isAfter(end)){
//           end = date.end;
//         }
          
//     }
    
//     if(!moment(start).isSame('5010-10-20') && !moment(end).isSame(defaultDate)){
//       return {start:start,end:end};
//     } 
        
//         return undefined;
// }
 

function str_format(string){
    // return string ;
    // return $('<div/>').text(string).html();
        // return unescape(encodeURIComponent(string));  

    // console.log("format:",string)
    return string
                // .split(/(\r\n|\n|\r)/gm).join(" ")//line break
                // .split(/\s+/g).join(" ")//spaces
                // .split(/\x26/).join("%26")//spaces
                // .split(/\x3D/).join("%3D")// & caract
                // .split(/\ue00E9/).join("e")// & caract
                // ;
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