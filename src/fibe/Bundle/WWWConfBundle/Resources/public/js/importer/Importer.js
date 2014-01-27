
var objects = {};

var notImportedLog,
    importedLog;

var mappingConfig,
    objectMap;

var defaultDate = 'now';

function run(file,callback,fallback){ 
   
             
            // console.log(file);
            if(file==undefined )
            {
                if(fallback!=undefined)fallback("Empty response"); 
                return;
            } 


            var confName ;

            objects["events"]         = [],
            objects["locations"]      = [],
            objects["xproperties"]    = [],
            objects["relations"]      = [],
            objects["categories"]     = [],
            objects["proceedings"]    = [],
            objects["persons"]        = [],
            objects["topics"]         = [],
            objects["proceedings"]    = [],
            objects["organizations"]  = [],
            objects["roles"]          = [], 
            objects["conference"]     = {},

            notImportedLog = {},
            importedLog    = {},
            objectMap      = {}; 

            //add custom config here (default : rdf)
            var formatConfig = { 
                'ocs': ocsConfig,
            }
 
            //check format (default : rdf)
            var format = undefined;    
            for (var i in formatConfig){

                try
                {
                    if(formatConfig[i].checkFormat(file) === true){
                        console.log("format found ! :" + i);
                        format = i;
                    }
                }
                catch(err)
                {
                    if(fallback!=undefined)fallback("bad format"); 
                    return;
                }
            } 

            //set format and config 
            mappingConfig =  format !== undefined 
                                    ? formatConfig[format] 
                                    : rdfConfig;

            var rootNode = file; 
            if(mappingConfig.rootNode){ 
                rootNode = doFormat(file,mappingConfig.rootNode.format); 
            } 

            //////////////////////////////////////////////////////////////////////////
            ////////////////////  pre process the root node  /////////////////////////
            //////////////////////////////////////////////////////////////////////////  
            if(mappingConfig.parseConference !== undefined){

                for ( var i in mappingConfig.parseConference){
                    var confInfoMapping = mappingConfig.parseConference[i];
                    var node = rootNode;
                    if(confInfoMapping.format){ 
                        node = doFormat(node,confInfoMapping.format);  
                    }
                    objects.conference[i] = node;
                }
            }



            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////  items Processing  ///////////////////////////
            //////////////////////////////////////////////////////////////////////////
 
            for (var i in mappingConfig.parseItemOrder){ 
                var itemMapping = mappingConfig[i];
                var addArray = objects[mappingConfig.parseItemOrder[i]];
                rootNode.children().each(function(index,node){
                    // var n = NodeUtils[mappingConfig.getNodeName](node);  
                    var n = doFormat(node,mappingConfig.getNodeName.format);     

                    if(n && n.toLowerCase().indexOf(itemMapping.nodeName)!= -1){
                        add(addArray,itemMapping,this,{name:n});   
                    }
                }); 
            }

            //////////////////////////////////////////////////////////////////////////
            ///////////////////////////// startAt less  //////////////////////////////
            //////////////////////////////////////////////////////////////////////////
             
            var earliestStart = moment('6000-10-10');
            var latestEnd = moment('1000-10-10');
            for(var i=0;i<objects.events.length;i++){
                var event = objects.events[i]; 
                if(event['setStartAt']){
                    
                    //allDay events
                    if(moment(event['setStartAt']).dayOfYear() != moment(event['setEndAt']).dayOfYear()){
                        event['setStartAt'] = moment(event['setStartAt']).startOf("day").format('YYYY-MM-DDTHH:mm:ss Z');
                        event['setEndAt'] = moment(event['setStartAt']).endOf("day").format('YYYY-MM-DDTHH:mm:ss Z');
                    }

                }
                //if no startAt
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

                        if(event['mainConferenceEvent'] ){
                            event['setEndAt'] = moment().hour(0).minute(0).second(0).millisecond(0).add('d', 1).format('YYYY-MM-DDTHH:mm:ss Z');
                        }
                    }
                }
                if(moment(event['setStartAt']).isBefore(earliestStart))
                    earliestStart = moment(event['setStartAt']);
                if(moment(event['setEndAt']).isAfter(latestEnd))
                    latestEnd = moment(event['setEndAt']);
            }
            if(earliestStart != moment('6000-10-10') && latestEnd != moment('1000-10-10')){ 
                objects["conference"]['setStartAt'] = earliestStart; 
                objects["conference"]['setEndAt']   = latestEnd; 
            }

            
            
            // SEND TO IMPORT PHP SCRIPT 
            for (var i=0;i<objects.locations.length;i++)
            {
                delete objects.locations[i]["uri"];
            }
            var dataArray={}; 
            dataArray['conference']=objects.conference;  
            dataArray['persons']=objects.persons;   
            dataArray['events']=objects.events; 
            dataArray['proceedings']=objects.proceedings; 
            dataArray['organizations']=objects.organizations; 
            dataArray['topics']=objects.topics;   
            dataArray['locations']=objects.locations;  
            dataArray['categories']=objects.categories;

            console.log('---------finished---------' );
            console.log(dataArray);
            console.log(objects.roles)

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
       
 
} // end run()




/**  function add() : process node given the config file 
 *
 * @param {array} addArray      the array to populate
 * @param {object} mapping      mapping object (defined in config files)
 * @param {dom elem} node       the xml dom element from the import file
 * @param {object} arg          arg for the override function
 */
function add(addArray,mapping,node,arg){

    //to override this method, write an "override : function(){...}" in the mapping file of the function. 
    if(mapping.override!==undefined){
        return mapping.override(node,addArray,arg);
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
        var key = doFormat(node,mappingConfig.getNodeKey.format);     

        $(node).children().each(function(){ 
            if(mapping.label[this.localName]!== undefined){
                
                if(mapping.label[this.localName].setter){
                    var nodeName = this.localName;

                    //unwrapped if needed
                    if(mapping.label[this.localName].wrapped === true){
                        $(this).children().each(function(){ 
                            set(mapping,nodeName,this,arg); 
                        });
                    }else{
                        set(mapping,nodeName,this,arg); 
                    }
                }
            }else{ 
                var mappingLake = arg.name+"/"+ this.localName;
                if(!notImportedLog[mappingLake])
                    notImportedLog[mappingLake] = undefined
            }
        });
             
         //post processing
        if(mapping.postProcess){
            if(mapping.postProcess(node,rtnArray) === true){
                //if it was the main conf event
                // console.log(rtnArray)
                // alert("main conf event found")
                objectMap[key] = rtnArray; 
                conference = rtnArray;

                return;
            } 
        }

        //finally add the object in the addArray and store a reference in the objectMap for faster access
        if(Object.size(rtnArray) > 0){
            objectMap[key] = rtnArray; 
            addArray.push( rtnArray );
        } 

        function set(mapping,nodeName,node,arg){
            var mappingStr = arg.name+"/"+ nodeName;
            if(!importedLog[mappingStr])
                importedLog[mappingStr] = undefined
            var val = node.textContent;

            if(mapping.label[nodeName].list){
                var vals = val.split(mapping.label[nodeName].list.delimiter);
                for(var i=0;i<vals.length;i++){
                    console.log(vals[i])
                    setWithValue(mapping,nodeName,node,arg,vals[i]);
                }
            }else{
                setWithValue(mapping,nodeName,node,arg,val);    
            }


            function setWithValue(mapping,nodeName,node,arg,val){

                // pre processing
                if(mapping.label[nodeName].preProcess){
                    mapping.label[nodeName].preProcess(node,rtnArray,val); 
                }

                if(mapping.label[nodeName].fk){   
                    // var key = NodeUtils[mapping.label[nodeName].fk.key](node);

                    var key = doFormat(node,mapping.label[nodeName].fk.format); 
                    // node = NodeUtils["child"](node,confInfoMapping.child)
                    
                    //pointed entity isn't a concrete node in this format and thus, don't contains any required unique index 
                    //so we must retrieve an index with getArrayId instead of objectMap 
                    //i.e. keywords in ocs format
                    var pointedEntity;
                    if(mapping.label[nodeName].fk.findInArrayWith){
                        pointedEntity = objects[ getArrayId(mapping.label[nodeName].fk.array,mapping.label[nodeName].fk.findInArrayWith,key) ];
                    }else{
                        pointedEntity = objectMap[key];
                    }

                    if(pointedEntity){
                        val = $.inArray(pointedEntity, objects[mapping.label[nodeName].fk.array]);
                    }else {
                        if(mapping.label[nodeName].fk.create){
                            var entry = {};
                            entry[mapping.label[nodeName].fk.findInArrayWith] = key;
                            objects[mapping.label[nodeName].fk.array].push(entry);  
                            val = objects[mapping.label[nodeName].fk.array].length -1 ;
                        }else{
                            console.warn("error while parsing "+mapping.nodeName+", "+mapping.label[nodeName].setter+" : "+key+" can't be found ");
                            return;
                        }
                    }   
                }

                if(mapping.label[nodeName].format){   
                    val = doFormat(node,mapping.label[nodeName].format) 
                }
                val = mapping.label[nodeName].setter === false ? val : typeof val === 'string' ? str_format(val) : val ;
                if(mapping.label[nodeName].multiple === true){
                    //create the object if not found
                    if(!rtnArray[mapping.label[nodeName].setter])
                        rtnArray[mapping.label[nodeName].setter]={};
                    
                    //get object length
                    var index = Object.size(rtnArray[mapping.label[nodeName].setter]);

                    //check if there's no duplicated link
                    var found = false;
                    for ( var i in rtnArray[mapping.label[nodeName].setter]){
                        if(rtnArray[mapping.label[nodeName].setter][i] == val){found = true;}
                    }
                    if(!found)rtnArray[mapping.label[nodeName].setter][index] = val;
                }else{
                    rtnArray[mapping.label[nodeName].setter]= val;
                }

            }
        }
    }
}

/**
 * utils function to get arrays index
 */

function getArrayId(arrayName,field,value){
    array = objects[arrayName];
    valueFormatted=str_format(value);
    for (var i=0;i<array.length;i++){ 
        if(array[i][field]==value || array[i][field]==valueFormatted){
            return i; 
        }
    }
    return -1;
}

// /**
//  *  AMENE A DISPARAITRE
//  */
// function getCategoryIdFromName(name){
//     name = str_format(name);

//     for (var i=0;i<objects.categories.length;i++){
//         //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
//         if(objects.categories[i]['setName']==name){
//             return i; 
//         }
//     }
//     return undefined;
// }

// function getLocationIdFromUri(uri){ 

//     for (var i=0;i<objects.locations.length;i++){
//         //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
//         if(objects.locations[i]['uri']==uri){
//             return i; 
//         }
//     }
//     return undefined;
// } 

// function getLocationIdFromName(locationName){
//     locationName = str_format(locationName);
//     for (var i=0;i<objects.locations.length;i++){
//         //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
//         // alert(locationName+" == "+locations[i]['setName']+"\n"+(locations[i]['setName']==locationName ?"true":"false"));
//         if(objects.locations[i]['setName']==locationName){
//             return i; 
//         }
//     }
//     return -1;
// }

// function getTopicIdFromName(topicName){
//     topicName = str_format(topicName);
    
//     for (var i=0;i< objects.topics.length;i++){
//         //console.log(url+"\n"+xproperties[i]['setXValue']+"\n"+(xproperties[i]['setXValue']==url)+"\n"+i);
//         if(objects.topics[i]['setName']==topicName){
//             return i; 
//         }
//     }
//     return -1;
// }

// function getEventIdFromURI(uri){

//     for (var i=0;i<objects.xproperties.length;i++){ 
//         if(objects.xproperties[i]['setXValue']==uri){
//             return objects.xproperties[i]['setCalendarEntity'];
//         }
//     }
//     return undefined;
// }


/**
 * computes concatenation of children's dates given a parent id
 */


function getChildrenDate(eventIndex)
{
    var start = moment('5010-10-20'); 
    var end   = moment('1900-10-20');
    for (var i in objects.events)
    {
        var child = objects.events[i];
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

function str_format(string){
    // return string ;
    // return $('<div/>').text(string).html();
        // return unescape(encodeURIComponent(string));  

    // console.log("format:",string)
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
    return string;
}


 Object.size = function(obj) {
    var size = 0, key;
    for (key in obj)if (obj.hasOwnProperty(key)) size++;
    return size;
};



function doFormat(node,format){ 
    if(isFunction(format)){
       return format(node); 
    } 
    for (var i in format){
        var currentFormat = format[i];
        node = NodeUtils[currentFormat.nodeUtils](node,currentFormat.arg)
    } 
    return node;
}

function isFunction(functionToCheck) {
    var getType = {};
    return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}

NodeUtils = {
    text : function(node){
        return $(node).text();
    },
    localName : function(node){
        return node.localName;
    },
    rdfNodeName : function(node){
        var uri=[];
        var rtn;
        $(node).children().each(function(){ 
            if(this.localName.indexOf("rdf:type")!== -1 ){
                if($(this).attr('rdf:resource').indexOf("#")!== -1 ){ 
                    uri.push($(this).attr('rdf:resource').split('#')[1]); 
                }
                else{
                    var nodeName = $(this).attr('rdf:resource').split('/'); 
                    uri.push(nodeName[nodeName.length-1]);  
                }
            } 
        });
        // console.log("getNodeName",node.localName)
        // console.log("uri",uri)
        for(var i in uri){
            var lc = uri[i].toLowerCase();
            if(lc.indexOf('keynotetalk')>-1){
                rtn = 'KeynoteEvent'; 
            }
        } 
        var lc = node.localName.toLowerCase();
        if(lc.indexOf('keynotetalk')>-1){
            rtn = 'KeynoteEvent'; 
        }
        else if(uri.length==1)
        {
            rtn = uri[0];
        }
        else if(uri.length==0) //rdf
        { 
            rtn = node.localName;
        } 
        return rtn;
    },
    // get a specific attr for the given node
    //arg[0] must contain the wanted attr
    attr : function(node,arg){
        return $(node).attr(arg[0]);
    },
    // get a specific node in a nodeSet
    //arg[0] must contain the wanted nodeName
    node : function(nodes,arg){
        var rtnNode;
        childNodeName = arg[0].toLowerCase();
        $(nodes).each(function(){
            if(this.nodeName.toLowerCase() === childNodeName){
                rtnNode = $(this);
            }
        })
        return rtnNode;
    }, 
    //arg[0] must contain the wanted child nodeName 
    child : function(node,arg){
        // return $(node).children(childNodeName);
        var rtnNode;
        childNodeName = arg[0].toLowerCase();
        $(node).children().each(function(){
            if(this.nodeName.toLowerCase() === childNodeName){
                rtnNode = $(this);
            }
        })
        return rtnNode;
    },
    
}