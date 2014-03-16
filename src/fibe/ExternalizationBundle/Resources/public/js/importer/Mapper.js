
var Mapper = function(){
    var self = this,
        mapper,
        mapping,
        data,
        file,
        serialisedDatas,
        notImportedLog,
        importedLog,
        dataLinks,
        knownNodes,
        knownCollection;
 
    var knownFormatConfig = { 
        'swc': {mapping:rdfConfig,mapper:xmlMapper},
        'ocs': {mapping:ocsConfig,mapper:xmlMapper}
    }


    this.setFile = function(f){
        file = f; 
    }
    this.readFile = function(){ 
        var reader = new FileReader();
     
        $(mapper).on("fileRead",function(ev,d){
            data = d;
            $(self).trigger("fileRead",[data]);
        })
        mapper.readFile(file,reader);
 
    }
    this.generateMappingFile = function(){
        mapping = generateMappingFile();
    }


    this.getUtils = function(){
        return mapper.utils; 
    }

    this.map = function($ctn){
        mapper["mappingConfig"] = mapping = mapping || mapper.defaultMapping; 
        Importer().setMappingConfig(mapping);

        dataLinks       = {},
        knownNodes      = {},
        knownCollection = {}; 

        var basePath = "root";
        var globalPanel = Pager.getPanelHtml("Found data",{panelClass:"panel-primary",margin:false,collapsible:false,collapsed:false})
                                    .appendTo($ctn);


        console.log("mapping : ",data); 
          

        function nodeCallBack(nodePath,$el,nodeName,panelOp,htmlOnly){
            if(htmlOnly){
                return doPanel();
            }
            if(!self.isNodeKnown(nodePath)){
                return doPanel();
            }
            return $el

            function doPanel(){ 
                if(!panelOp)panelOp={panelClass:"panel-success",margin:true,collapsible:true,collapsed:true};
                panelOp["node-path"] = nodePath;
                var tempPanel = Pager.getPanelHtml(nodePath,panelOp);  
                $el.append(tempPanel);
                return tempPanel.find("> ul"); 
            }

                    // if(!mapper.isNodeKnown(childNodePath)){
                    //     childTags.push(getNodeName(child));
                    //     tempPanel = Pager.getPanelHtml(getNodeName(child),{panelClass:"panel-success",margin:true,"node-path":childNodePath,collapsible:true,collapsed:true});
                    //     $el.append(tempPanel);
                    //     generateHtml($(child),childNodePath,tempPanel);
                    // } else{
                    //     //already mapped
                    //     // mapper.addMappingCollection(nodePath); 
                    // } 


        };
        function entryCallBack(nodePath,$el,value){   
                $el.append(self.generateNode(nodePath,value)); 
        };
        // $(mapper).off("entry").on("entry",function(ev,nodePath,value){ 
        //     if(!self.isNodeKnown(nodePath,value,callback)){ 
        //         var panel = Pager.getPanelHtml(nodePath,{panelClass:"panel-success",margin:true,collapsible:false,collapsed:false});
        //         var node = $(xlsxMapper.generateNode(nodePath));
        //         panel.append(node);
        //         globalPanel.append(panel);
        //         if(callback)callback();



        //         tempPanel = Pager.getPanelHtml(getNodeName(child),{panelClass:"panel-success",margin:true,"node-path":nodePath+ "/"+getNodeName(child),collapsible:true,collapsed:true});
        //         $el.append(tempPanel);
        //         childTags.push(getNodeName(child));
        //         generateHtml($(child),nodePath+ "/"+getNodeName(child),tempPanel);
        //     }
        // });
        $(mapper).off("mapEnd").on("mapEnd",function(ev,$html){ 
            // $html.appendTo($el); 
            initUi();
        });
        mapper.map(data,basePath,globalPanel.find("> ul"),nodeCallBack,entryCallBack);

    }
    this.setMapper = function(m){
        mapper = m;
    }
    this.getNodeName = function(node,i){
        return mapper.getNodeName(node,i)
    };
    this.getNbRootChildren = function(node){  
        return mapper.getNbRootChildren(node)
    } 

    this.setKnownMapping = function(formatName){  
        if(!knownFormatConfig[formatName])return console.warn("unknown formatName : "+formatName); 
        mapping = knownFormatConfig[formatName].mapping;
        mapper = knownFormatConfig[formatName].mapper;
        return self;
    }
    this.getMapping = function(){
        return mapping;
    }
    this.getData = function(){
        return data;
    }

    this.isNodeKnown = function(nodePath,sample){
        if(!knownNodes[nodePath]){
            console.log("adding "+nodePath);
            knownNodes[nodePath] = {samples:[]};
            addSample(nodePath,sample);
            return false;
        } 

        addSample(nodePath,sample);
        
        return true;

        function addSample(nodePath,sample){
            if(sample && $.inArray(sample, knownNodes[nodePath].samples) === -1){
                knownNodes[nodePath].samples.push(sample);  
            } 

        }
    }
    this.checkIfMappingCollection = function(nodePath,childrenNodePath){
        //TODO review how to get collection
        //TODO review how to get collection
        //TODO review how to get collection 
        if(childrenNodePath.length==1){
            console.log("new Collection of "+ nodePath);
            knownCollection[nodePath] = {};
        }
    }

    this.getDataLinks = function(){ 
        return dataLinks;
    }
    this.addDataLink = function(modelPath,filePath){
        dataLinks[modelPath] = {nodePath : filePath};
    }
    this.removeDataLink = function(modelPath){
        delete dataLinks[modelPath];
    }
    this.getEntityMapping = function(nodePath){ 
        console.log("getEntityMapping",nodePath,dataLinks)
        var rtn = {};
        for(var i in dataLinks){
            var link = dataLinks[i];
            var test = i;
            i.split(nodePath).join("");
            console.log(test,nodePath,i);
            if(link != i){
                rtn.push(link)
            }
        }
        return rtn;
    }
 

    this.generateNode = function(nodePath,value,label){
        var rtn = ""; 
        if(!self.isNodeKnown(nodePath + "/text",value)){ 
            rtn += '<li data-node-path="'+nodePath+'/text" class="map-node list-group-item list-group-item-warning">'+(label || 'text')+'</li>';
        } 
        return rtn;
    }
 

    // this.generateNode = function(node,nodePath){
    //     if(!node.text() || node.text() == "")return"";
    //     var rtn = "";

    //     if(!self.isNodeKnown(nodePath + "/text",node.text())){ 
    //         rtn += '<li data-node-path="'+nodePath+'/text" class="map-node list-group-item list-group-item-warning">text</li>';
    //     } 
    //     return rtn;
    // }


    this.getAttributesHtml = function(node,nodePath){
        var rtn = "";  
        if(!node[0].attributes || node[0].attributes.length==0)return rtn;
        $.each(node[0].attributes, function() { 

            if(!self.isNodeKnown(nodePath + "/@" + this.name.toLowerCase(),this.value)){ 
                rtn += '<li data-node-path="'+nodePath+ "/@" + this.name+'" class="map-node list-group-item list-group-item-warning">@'+this.name+"</li>";
            }
        }); 
        return rtn;
    }


    var initUi = function (){
        //collection
        $('#datafile-form .panel').each(function(){
            var nodePath = $(this).data("node-path"); 
            if(knownCollection[nodePath]){
                knownCollection[nodePath] = $(this);
                var collectionNodeName = $(this).find("> .panel-heading").text();
                // $(this).find("> .panel-heading").remove();
                var childPanel = $(this).find("> .list-group > .panel-success ")
                childPanel.data("collection",nodePath)
                          // .insertBefore($(this))
                          .find("> .panel-heading > .panel-title")
                             .prepend('<i title=" collection node of '+collectionNodeName+' " class="fa fa-bars"></i> ');
                // $(this).remove();
            }
        })

        $('.map-node').each(function(){
            var nodePath = $(this).data("node-path");

            knownNodes[nodePath]["div"] = $(this);

            //popover
            var samples = knownNodes[nodePath].samples;
            var content = "<ul>";
            for(var i = 0; i < samples.length && i < 10;i++){
                content += "<li>"+samples[i]+"</li>";
            }
            content += "</ul>";
            $(this).popover({
                trigger : 'hover',
                html : true,
                placement : "right",
                title : ' <b>'+nodePath+'</b>',
                content : content
            });

            //draggable
            $(this).draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0,  //  original position after the drag
                helper: 'clone'
            });
        })
    }



    //loop over the model panels  to build a mapping file
    function generateMappingFile(){
        console.log("############### generateMappingFile starts")
        
        //loop only on validated mapping
        $("#model-form .panel-success").each(function(iPanel,panel){
            var modelName = $(panel).data("model-path");
            // get the corresponding model mapping config
            $(panel).find(".list-group-item-success").each(function(){ 
                var leftEntityMapping;
                for (var i in dataLinks){
                    if(i == $(this).data("model-path")){
                        leftEntityMapping = dataLinks[i];
                    }
                }
                console.log("leftEntityMapping:",leftEntityMapping)

                var leftCollectionPath = getClosestCollectionPath(leftEntityMapping.nodePath);


                var modelSetter = Model.getSetter(modelName,$(this).data("model-path").split("/")[1])
                
                
                if(modelName=="Conference"){
                    //the conference mapping has a different mapping object
                    var mappingObj = getOrCreateParseConference(modelName);
                    mappingObj[modelSetter]={};
                    mappingObj[modelSetter]["format"] = extractMappingFormat(leftEntityMapping.nodePath);
                    
                }else{ 
                    var nodePtyPath = leftEntityMapping.nodePath.split(leftCollectionPath).join(""); 
                    var nodeName = nodePtyPath.split("/")[0] != "" ?nodePtyPath.split("/")[0] : nodePtyPath.split("/")[1];

                    var mappingObj = getOrCreateMappingObjFromFormat(extractMappingFormat(leftCollectionPath,true),modelName); 

                    mappingObj.label[nodeName]={setter:modelSetter};
                    mappingObj.label[nodeName]["format"] = extractMappingFormat(nodePtyPath.split(nodeName).join(""));

                } 
            }) 
        });
        console.log("############### generateMappingFile ended, returning : ",mapping)
        return mapping;
        
        /**
         * Get or create the conference mapping object  
         * @return {[type]}            [description]
         */
        function getOrCreateParseConference(){  
            if(!mapping['parseConference'])
                mapping['parseConference']={} 
            return mapping['parseConference']
        }
        
        /**
         * Get or create the mapping in the "under generating mapping file" phase corresponding to the array key  
         * @param  {object} format  the format to find
         * @param  {string} array   the array to link with in case of a not found format
         * @return {[type]}         the existing or new mapping
         */
        function getOrCreateMappingObjFromFormat(format,array){  
                if(!mapping['mappings'])
                    mapping['mappings']=[];
   
                 //look if its already registered
                    console.log("getOrCreateMappingObjFromFormat ",format)
                for(var i in mapping['mappings']){ 
                    if(isSameFormat(mapping['mappings'][i].format,format)){ 
                        return mapping['mappings'][i];
                    }
                }

                //add if not found
                var newMapping = {array: array,label:{},format:format};
                mapping['mappings'].push(newMapping);
                return newMapping;
        }
        
        /**
         * split mapping path to generate a format object (ignore "root" and "" values)
         * @param  {String} mapping           the mapping path to parse
         * @param  {bool} collectionMapping   is it a collection ?
         * 
         * @return {object} format            the generated format
         */
        function extractMappingFormat(mapping,collectionMapping){
            var format = [];
            var splittedEntityMapping = mapping.split("/"); 
            for(var i in splittedEntityMapping){
                if(splittedEntityMapping[i]=="root" || splittedEntityMapping[i]=="")continue;//don't add rootNode
                if(splittedEntityMapping[i]=="text"){
                    format.push({
                        fn : "text"
                    })
                }else if(collectionMapping){
                    var label = splittedEntityMapping[i]
                    format.push({
                        fn : "children",
                        arg : [label]
                    })
                }else if(splittedEntityMapping[i].charAt(0) == "@"){
                    var label = splittedEntityMapping[i] 
                    format.push({
                        fn : "attr",
                        arg : [label.substring(1)]
                    })
                }else {
                    var label = splittedEntityMapping[i]
                    format.push({
                        fn : "child",
                        arg : [label]
                    })
                } 
            }
            return format;
        }

        /**
         * loop recursively over parents to find the closest collection node 
         * @param  {String} nodePath                   node path
         * @return {String} collectionNodePath         the closest parent collection node path
         */
        function getClosestCollectionPath(nodePath){
            if(knownCollection[nodePath])
                return nodePath;
            var $node = knownNodes[nodePath].div;
            if($node){
                var found = false;
                while(true){
                    var parent = $node.parent();
                    if(parent.length==0){
                        return false; //stop loop if no more parent
                    }else{
                        var parentPath = parent.data("node-path");
                        if(parentPath && knownCollection[parentPath])
                            return parentPath;
                        $node = parent;
                        nodePath = parentPath || nodePath;
                    }

                }
            }

        }

        function isSameFormat(f1,f2){
            for(var i in f1){
                if(f1[i].nodeUtils != f2[i].nodeUtils)return false;
                for (var j in f1[i].arg){
                    if(f1[i].arg[j] != f2[i].arg[j])return false;
                }
            }
            return true;
        }
    }
}