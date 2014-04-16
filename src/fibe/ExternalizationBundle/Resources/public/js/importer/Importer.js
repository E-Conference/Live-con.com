
 

function Importer() {
    if (Importer._instance) {
        return Importer._instance;
    }
    //if the function wasn't called as a constructor,
    //call it as a constructor and return the result
    if (!(this instanceof Importer)) {
        return new Importer();
    }
    Importer._instance = this;
 


    
    this.mappingConfig = {};
    this.objects = objects = {};

    this.setMappingConfig = setMappingConfig;
    this.setUtils = setUtils;
    this.run = run;

    this.doFormat = doFormat;
    this.getNodeName = getNodeName;
    this.getArrayId = getArrayId;


    var self = this,
        utils,      //data manipulation functions used by doFormat
        notImportedLog,
        importedLog,
        objectMap,
        objectsIndexes,
        fkMap,
        fkMapIndexes,
        defaultDate = 'now',
        utils       = {};
    

    function setMappingConfig(mappingConfig){
        this.mappingConfig = mappingConfig; 
    }
    function setUtils(u){
        utils = u; 
    }
    function getUtils(){
        return utils; 
    }

    function run(file,op,callback,fallback){ 
        
        if(!file)
        {
            if(fallback!=undefined)fallback("Error with the file."); 
            return;
        }
        var mappingConfig = self.mappingConfig; 
        if(!mappingConfig)
        {
            if(fallback!=undefined)fallback("Error with the mapping config."); 
            return;
        }   
        this.setMappingConfig(mappingConfig)
        console.log("mapping with : ",mappingConfig)


        var confName ;
        var objectMapIndex = 0;

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

        notImportedLog = [],
        importedLog    = [],
        objectMap      = {}, 
        objectsIndexes = {}, 
        fkMap          = [], 
        fkMapIndexes   = []; 


        var rootNode = file; 
        // if(mappingConfig.rootNode){
        //     rootNode = Importer().doFormat(file,mappingConfig.rootNode.format); 
        // }
        
        // get the most populated node as rootNode
        var maxChildren = 0;
        $(file).each(function(i,node){
            var nbChildren = $(node).children().length;
            if( nbChildren > maxChildren){
                rootNode=$(node);
                maxChildren = nbChildren
            }
        });

        //check root node
        if(!rootNode){
            if(fallback)fallback("Wrong root node"); 
            return;
        }
        var nbRootChild = getNbRootChildren(rootNode);
        console.log("rootNode contains "+nbRootChild+" children",rootNode)
        if(nbRootChild ==0){
            if(fallback)fallback("Empty root node"); 
            return;
        }


        //////////////////////////////////////////////////////////////////////////
        ////////////////////  pre process the root node  /////////////////////////
        //////////////////////////////////////////////////////////////////////////  
        
        if(mappingConfig.parseConference){
            for ( var i in mappingConfig.parseConference){
                var confInfoMapping = mappingConfig.parseConference[i];
                var node = rootNode;
                if(confInfoMapping.format){
                    node = Importer().doFormat(node,confInfoMapping.format);
                }
                objects.conference[i] = node;
            }
        }

        //////////////////////////////////////////////////////////////////////////
        ///////////////////////////  items Processing  ///////////////////////////
        //////////////////////////////////////////////////////////////////////////

        //loop over all mappingConfig.mappings
        for (var i in mappingConfig.mappings){ 
            var itemMapping = mappingConfig.mappings[i];  
            var collectionNode = Importer().doFormat(rootNode,mappingConfig.mappings[i].format); 
            if(collectionNode.length == 0){
                console.warn("couln't not have got nodes for the mapping",mappingConfig.mappings[i])
                console.warn("with rootNode : ",rootNode)
            }else{
                // console.log("mapping a collection",collectionNode)
            }
            $.each(collectionNode,function(index){ 
                add(itemMapping.array,itemMapping,this,index);    
            }); 
        } 

        //////////////////////////////////////////////////////////////////////////
        ///////////////////////////  fk Processing  ///////////////////////////
        //////////////////////////////////////////////////////////////////////////

        //loop over all foreign key map obj
        for (var i in fkMap){ 
            var fk = fkMap[i];
            var fks = objectMap[fk.entity][fk.setter];
            if(typeof fks === 'object'){
                for(var j in fks){
                    computeFk(fks[j],fk, j); 
                }
            }else{
                computeFk(fks, fk)
            }
        }

        //////////////////////////////////////////////////////////////////////////
        ///////////////////////////// startAt less  //////////////////////////////
        //////////////////////////////////////////////////////////////////////////
        
        //compute at the same time the mainConfEvent date
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
                }
            }
            if(moment(event['setStartAt']).isBefore(earliestStart))
                earliestStart = moment(event['setStartAt']);
            if(moment(event['setEndAt']).isAfter(latestEnd))
                latestEnd = moment(event['setEndAt']);
        }
        if(!( earliestStart.isSame(moment('6000-10-10')) || latestEnd.isSame(moment('1000-10-10')) )){
            if(earliestStart.isSame(latestEnd)){
                objects["conference"]['setStartAt'] = moment().hour(0).minute(0).second(0).millisecond(0).format('YYYY-MM-DDTHH:mm:ss Z');
                objects["conference"]['setEndAt'] = moment(objects["conference"]['setStartAt']).add('d', 1).format('YYYY-MM-DDTHH:mm:ss Z');
            }else{
                objects["conference"]['setStartAt'] = earliestStart; 
                objects["conference"]['setEndAt']   = latestEnd; 
            }
        }

        
        
        // SEND TO IMPORT PHP SCRIPT 
        for (var i=0;i<objects.locations.length;i++)
        {
            delete objects.locations[i]["uri"];
        }

        var dataArray={
            conference   : objects.conference,
            persons      : objects.persons,
            events       : objects.events,
            proceedings  : objects.proceedings,
            organizations: objects.organizations,
            topics       : objects.topics,
            locations    : objects.locations,
            categories   : objects.categories
        }; 

        console.log('---------finished---------' );
        console.log(dataArray); 

        var empty = true;
        for (var i in dataArray){
            if(empty == true && dataArray[i] && dataArray[i].length>0)empty = false;
        }
        if(empty == true && !dataArray['conference']['setSummary']){
            if(fallback!=undefined)fallback("nothing found... please check your file !"); 
            return;
        }

        if(callback!=undefined)callback(dataArray,importedLog,notImportedLog);   

        //log not imported properties

        console.log("Imported properties : ");
        console.log(importedLog);

        console.log("Not imported properties : ");
        console.log(notImportedLog);
   
        // workflow run end
        // workflow run end
        
        

        /**  function add() : process node given the config file 
         *
         * @param {array} addArray      the array to populate
         * @param {object} mapping      mapping object (defined in config files)
         * @param {dom elem} node       the xml dom element from the import file 
         * @param {object} arg          arg for override functions 
         */
        function add(addArray,mapping,node,index){

            //to override this method, write an "override : function(){...}" in the mapping file of the function. 
            if(mapping.override!==undefined){
                return mapping.override(node,addArray,index);
            }
            //unwrapped if needed (not used anymore)
            // if(mapping.format){ 
            //     var nodes = Importer().doFormat(node,mapping.format);  
            //     console.log("node",node);   
            //     console.log("nodes",nodes);   
            //     nodes.each(function(){
            //         process(addArray,mapping,this);
            //     });
            // }else{
                process(addArray,mapping,node,index); 
            // }

            function process(addArray,mapping,node,index){
                var rtnArray = {};  
                var key = Importer().doFormat(node,mappingConfig.getNodeKey.format);
                // var children = getChildren(node);
                // $.each(children,function(index){ 
                var nodeName = self.getNodeName(node,index); 
                for(var i in mapping.label){
                    var curMapping = mapping.label[i];
                    var val = Importer().doFormat(node,curMapping.format); 
                    if(curMapping.setter && val && val[0]){

                        //unwrapped if needed 
                        //TODO remove this option and use format instead
                        // if(curMapping.wrapped === true){
                        //     $(this).children().each(function(){ 
                        //         set(curMapping,nodeName,this); 
                        //     });
                        // }else{
                            set(curMapping,nodeName,val);
                        // } 
                    }
                        // });
                    // TODO check mapping lake
                    // }else{ 
                    //     var mappingLake = getMappingPath(mapping)+" : "+nodeName; 
                    //     if($.inArray(mappingLake, notImportedLog) === -1)
                    //         notImportedLog.push(mappingLake); 
                    // } 
                }
                // });
                     
                 //post processing
                if(mapping.postProcess){
                    if(mapping.postProcess(node,rtnArray,self.getNodeName(node,index)) === true){
                        //if it was the main conf event
                        //register in the objectmap index
                        conference = rtnArray;
                        defaultDate = conference['setStartAt'] || defaultDate;

                        addObjectMap(key,rtnArray); 
                        objectsIndexes[key] = {array:"conference"};
                        objects["conference"] = rtnArray;
                        return;
                    } 
                }

                //finally add the object in the addArray and store a reference in the objectMap for faster access
                if(Object.size(rtnArray) > 0){ 
                    //register in the objectmap index
                    addObjectMap(key,rtnArray);
                    addObject(addArray,rtnArray,key); 
                } 

                function set(curMapping,nodeName,node){
                    var mappingStr  = getMappingPath(mapping)+" : "+nodeName
                    if($.inArray(mappingStr, importedLog) === -1)
                        importedLog.push(mappingStr);  
                    var val = (typeof node == 'string' || node instanceof String ) ? node : $(node)[0].textContent;

                    if(curMapping.list){
                        var vals = val.split(curMapping.list.delimiter); 
                        for(var i=0;i<vals.length;i++){  
                            setWithValue(curMapping,nodeName,node,vals[i],true);
                        }
                    }else{
                        setWithValue(curMapping,nodeName,node,val);
                    }


                    function setWithValue(curMapping,nodeName,node,val,splitter){

                        // pre processing
                        if(curMapping.preProcess){
                            curMapping.preProcess(node,rtnArray,val); 
                        }

                        if(curMapping.fk){   

                            var fk = curMapping.fk;
                            var fkKey = !splitter ? Importer().doFormat(node,fk.format) : val; 
                            
                            var pointedEntity;

                            if(fk.create){ 
                                fkKey = fk.array+"-"+fkKey;
                                if(!objectMap[fkKey]){
                                    var entry = {};
                                    entry[fk.create] = val;

                                    addObject(fk.array,entry,fkKey);  
                                    addObjectMap(fkKey,entry);
                                }
                            } 
                            val = fkKey; 
                            if(!fkMapIndexes[key+"-"+curMapping.setter]){
                                fkMapIndexes[key+"-"+curMapping.setter] = "lol";
                                fkMap.push({entity:key,setter:curMapping.setter,fkArray:fk.array});
                            } 
                        }

                        // if(curMapping.format){   
                        //     val = Importer().doFormat(node,curMapping.format) 
                        // }
                        val = curMapping.setter === false ? val : typeof val === 'string' ? str_format(val) : val ;
                        val = val.trim();
                        if(curMapping.multiple === true){
                            //create the object if not found
                            if(!rtnArray[curMapping.setter])
                                rtnArray[curMapping.setter]={};
                            
                            //get object length
                            var index = Object.size(rtnArray[curMapping.setter]);

                            //check if there's no duplicated link
                            var found = false;
                            for ( var j in rtnArray[curMapping.setter]){
                                if(rtnArray[curMapping.setter][j] == val){found = true;}
                            }
                            if(!found)rtnArray[curMapping.setter][index] = val;
                        }else{
                            rtnArray[curMapping.setter]= val;
                        }

                    }
                }
                function addObjectMap(key,rtnArray){ 
                    objectMap[key] = rtnArray; 
                } 
                function addObject(addArray,rtnArray,key) {
                    objects[addArray].push(rtnArray); 
                    objectsIndexes[key] = {array:addArray,index:objects[addArray].length-1}; 
                } 
            }
        }
    } // this.run end

     function computeFk(fkKey, fk, index){
                // console.log("computeFk : "+fk,addArray);
                var objInd = objectsIndexes[fkKey];
                if(!objInd ){
                    console.log("error while retreiving fk "+fk.entity+"-"+fk.setter+" : cannot find "+fkKey);
                    deleteKey(); 
                    return; 
                }else if(objInd.array == "conference"){
                    deleteKey(); 
                    // console.log("parent is mainConfEvent",objectMap[fk.entity][fk.setter]);
                    return;
                } 
                else {
                    if(index){
                        objectMap[fk.entity][fk.setter][index] = objInd.index;
                    }else{
                        objectMap[fk.entity][fk.setter] = objInd.index; 
                    } 
                } 
                function deleteKey(){
                    // if(index!==undefined){
                    //     delete fkKey;
                    // }else{
                        delete objectMap[fk.entity][fk.setter]; 
                    // } 
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


    function getMappingPath(mapping){
        var rtn = [];
        for(var i in mapping.format){
            rtn.push(mapping.format[i].arg[0]) ;
        }
        return rtn.join("/");
    }


    /**
     *  Public function Importer().doFormat 
     *     used to parse a node according to a format object 
     * @param  {Object} node    the thing to parse
     * @param  {Object} format  format instruction object (see ocs_config / rdf_config for examples )
     * @param  {Boolean} log    verbose mode
     * @return {Object|String}  the extracted node | nodeset | value
     */
    function doFormat(node,format,log){
        if(!format)throw " format missing for node";
        var rtn = node;
        if(isFunction(format)){
           return format(rtn); 
        } 
        for (var i in format){
            var currentFormat = format[i];
            rtn = utils[currentFormat.fn](rtn,currentFormat.arg,log)
            if(!rtn)throw "couldn't have proceed "+currentFormat.fn;
        }
        return rtn;
    } 
    function getNbRootChildren(node){  
        return mapper.getNbRootChildren(node)
    }
    function getChildren(node){ 
       return doFormat(node,[{fn:"children"}]);
    }

    function getNodeName(node,i){
        return mapper.getNodeName(node,i)
    };


    function isFunction(functionToCheck) {
        var getType = {};
        return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
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
};//Importer Singleton 

