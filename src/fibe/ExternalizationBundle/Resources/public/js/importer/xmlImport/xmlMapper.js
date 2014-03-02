xmlMapper = {


	initialize : function (file){
		xmlMapper["nodeTab"] = [];
		xmlMapper["file"] = file;
	},

    //read a file and load it in xmlMapper["data"];
	read : function(el){
	    var reader = new FileReader();
     
        reader.onload = function(e) { 
        	xmlMapper["data"] = $(e.target.result);
        	xmlMapper["el"] = $(el);
            $(xmlMapper).trigger("fileRead",[xmlMapper["data"]]);
        } 
        reader.readAsText(xmlMapper["file"]);  
	},

    getSerialisedDatas : function(){
        return xmlMapper["serialisedDatas"];
    },
    setSerialisedDatas : function(datas){
        xmlMapper["serialisedDatas"] = datas;
    },

    getImportedLog : function(){
        return xmlMapper["importedLog"];
    },
    setImportedLog : function(importedLog){
        xmlMapper["importedLog"] = importedLog;
    },

    getNotImportedLog : function(){
        return xmlMapper["notImportedLog"];
    },
    setNotImportedLog : function(notImportedLog){
        xmlMapper["notImportedLog"] = notImportedLog;
    },

    defaultNodeReadingConfig : {
        util : "xmlUtil",
        getNodeKey : {
            format : [{
                fn : "attr",
                arg : ["rdf:about"],
            }] 
        }, 
        getNodeName : {
            format : [{
                fn : "rdfNodeName", 
            }] 
        },  
    },
	map : function($data,baseConfig){

        //TODO make this dynamic
        xmlMapper["mappingConfig"] = baseConfig || xmlMapper.defaultNodeReadingConfig;
		Importer().setMappingConfig(xmlMapper["mappingConfig"]);
        if (!$data)$data = xmlMapper["data"];
        console.log("mapping : ",$data);
        xmlMapper.dataLinks= {};
        xmlMapper.knownNodes = {};
        xmlMapper.knownCollection = {};
        
        var nodePath = "root";
        var globalPanel = Pager.getPanelHtml("Found data ",{panelClass:"panel-primary"}).appendTo(xmlMapper["el"]);
        var tempPanel = null;
        generateHtml($data,nodePath,globalPanel,xmlMapper.knownNodes); 
        function generateHtml($node,nodePath,$el,knownNodes){
 
            $el = $el.find("> ul");

            if($node.children().length != 0 ){
                var childTags = [];
                $node.children().each(function(index,child){

                    if(!xmlMapper.isNodeKnown(nodePath+ "/"+getNodeName(child))){
                        childTags.push(getNodeName(child));
                        tempPanel = Pager.getPanelHtml(getNodeName(child),{panelClass:"panel-success",margin:true,"node-path":nodePath+ "/"+getNodeName(child),collapsible:true,collapsed:true});
                        $el.append(tempPanel);
                        generateHtml($(child),nodePath+ "/"+getNodeName(child),tempPanel,knownNodes) 
                    } else{
                        //already mapped
                        // addMappingCollection(nodePath); 
                    } 
                    generateHtml($(child),nodePath+ "/"+getNodeName(child),$el,knownNodes);

                });
                //TODO : review the to find collections
                //TODO : review the to find collections
                //TODO : review the to find collections
                if(childTags.length==1)addMappingCollection(nodePath,childTags); 
            }else{
                $el.append($(xmlMapper.generateNode($node,nodePath))); 

            }

            function addMappingCollection(nodePath,childTags){
                console.log("childTags of "+ nodePath, childTags);
                xmlMapper.knownCollection[nodePath] = {};
            }
            function getNodeName(node){
                
                var nodeName = Importer().doFormat(node,xmlMapper.mappingConfig.getNodeName.format);
                
                return (nodeName ? nodeName.toLowerCase() : console.log("undefined nodename for",node));
            }
        }



         
        

        // option popover
        // var $nodeNameSelect = $('<select><option selected="selected" value="localname">Node tag</option>\
        //                                  <option value="rdfNodeName">Rdf tag</option></select>')
        //                             .select2()
        //                             .on("change",function(){
        //                                 if(localname == "localname"){
        //                                     xmlMapper.map(); format = [{
        //                                         nodeUtils : "localName", 
        //                                     }] 
        //                                 }
        //                             });
        // var clickablePopover = Pager.addClickablePopover($(" <i class='fa fa-cog'> </i> "),"CACA")
        // $('#datafile-form > .panel-primary > .panel-heading > .panel-title').append(clickablePopover);
        

        //collection
        $('#datafile-form .panel').each(function(){
            var nodePath = $(this).data("node-path"); 
            if(xmlMapper.knownCollection[nodePath]){
                xmlMapper.knownCollection[nodePath] = $(this);
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

            xmlMapper.knownNodes[nodePath]["div"] = $(this);

            //popover
            var samples = xmlMapper.knownNodes[nodePath].samples;
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
                content : content,
            });

            //draggable
            $(this).draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0,  //  original position after the drag
                helper: 'clone'
            });
        })
	},

    //loop over the model panels  to build a mapping file
    generateMappingFile : function(){
        console.log("############### generateMappingFile starts")
        
        //loop only on validated mapping
        $("#model-form .panel-success").each(function(iPanel,panel){
            var modelName = $(panel).data("model-path");
            // get the corresponding model mapping config
            $(panel).find(".list-group-item-success").each(function(){ 
                var leftEntityMapping;
                for (var i in xmlMapper.dataLinks){
                    if(i == $(this).data("model-path")){
                        leftEntityMapping = xmlMapper.dataLinks[i];
                    }
                }
                console.log("leftEntityMapping:",leftEntityMapping)

                var leftCollectionPath = getLeftCollectionPath(leftEntityMapping.nodePath);


                var modelSetter = Model.getSetter(modelName,$(this).data("model-path").split("/")[1])
                
                
                //check if this is the conference mapping
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
        console.log("############### generateMappingFile ended, returning : ",xmlMapper.mappingConfig)
        return xmlMapper.mappingConfig;
        
        /**
         * Get or create the conference mapping object  
         * @return {[type]}            [description]
         */
        function getOrCreateParseConference(){  
            if(!xmlMapper.mappingConfig['parseConference'])
                xmlMapper.mappingConfig['parseConference']={} 
            return xmlMapper.mappingConfig['parseConference']
        }
        
        /**
         * Get or create the mapping in the "under generating xmlMapper.mappingConfig file" phase corresponding to the array key  
         * @param  {object} format  the format to find
         * @param  {string} array   the array to link with in case of a not found format
         * @return {[type]}         the existing or new mapping
         */
        function getOrCreateMappingObjFromFormat(format,array){  
                if(!xmlMapper.mappingConfig['mappings'])
                    xmlMapper.mappingConfig['mappings']=[];
   
                 //look if its already registered
                    console.log("getOrCreateMappingObjFromFormat ",format)
                for(var i in xmlMapper.mappingConfig['mappings']){
                    console.log("getOrCreateMappingObjFromFormat ",xmlMapper.mappingConfig['mappings'][i].format) 
                    if(isSameFormat(xmlMapper.mappingConfig['mappings'][i].format,format)){ 
                        return xmlMapper.mappingConfig['mappings'][i];
                    }
                }

                //add if not found
                var newMapping = {array: array,label:{},format:format};
                xmlMapper.mappingConfig['mappings'].push(newMapping);
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
                        arg : [label],
                    })
                }else if(splittedEntityMapping[i].charAt(0) == "@"){
                    var label = splittedEntityMapping[i] 
                    format.push({
                        fn : "attr",
                        arg : [label.substring(1)],
                    })
                }else {
                    var label = splittedEntityMapping[i]
                    format.push({
                        fn : "child",
                        arg : [label],
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
        function getLeftCollectionPath(nodePath){
            if(xmlMapper.knownCollection[nodePath])
                return nodePath;
            var $node = xmlMapper.knownNodes[nodePath].div;
            if($node){
                var found = false;
                while(true){
                    var parent = $node.parent();
                    if(parent.length==0){
                        return false; //stop loop if no more parent
                    }else{
                        var parentPath = parent.data("node-path");
                        if(parentPath && xmlMapper.knownCollection[parentPath])
                            return nodePath;
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
    },


    isNodeKnown : function(nodePath,sample){
        if(!xmlMapper.knownNodes[nodePath]){ 
            console.log("adding "+nodePath);
            xmlMapper.knownNodes[nodePath] = {samples:[]};
            addSample(nodePath,sample);
            return false;
        }
        addSample(nodePath,sample);
        
        return true;

        function addSample(nodePath,sample){
            if(sample && $.inArray(sample, xmlMapper.knownNodes[nodePath].samples) === -1){
                xmlMapper.knownNodes[nodePath].samples.push(sample);  
            } 

        }
    },

    getEntityMapping : function(nodePath){ 
        console.log("getEntityMapping",nodePath,xmlMapper.dataLinks)
        var rtn = {};
        for(var i in xmlMapper.dataLinks){
            var link = xmlMapper.dataLinks[i];
            var test = i;
            i.split(nodePath).join("");
            console.log(test,nodePath,i);
            if(link != i){
                rtn.push(link)
            }
        }
        return rtn;
    },

    getKnownMapping : function(format){

            var knownFormatConfig = { 
                'swc': rdfConfig,
                'ocs': ocsConfig,
            }

            if(!knownFormatConfig[format])return console.warn("unknown format : "+format);

            return knownFormatConfig[format]; 
    },



    generateNode : function(node,nodePath){
        if(!node.text() || node.text() == "")return"";
        var rtn = "";

        if(!xmlMapper.isNodeKnown(nodePath + "/text",node.text())){ 
            rtn += '<li data-node-path="'+nodePath+'/text" class="map-node list-group-item list-group-item-warning">text</li>';
        } 
        return rtn;
    },

    getAttributesHtml : function(node,nodePath){
        var rtn = "";  
        if(!node[0].attributes || node[0].attributes.length==0)return rtn;
        $.each(node[0].attributes, function() { 

            if(!xmlMapper.isNodeKnown(nodePath + "/@" + this.name.toLowerCase(),this.value)){ 
                rtn += '<li data-node-path="'+nodePath+ "/@" + this.name+'" class="map-node list-group-item list-group-item-warning">@'+this.name+"</li>";
            }
        }); 
        return rtn;
    },
    
   
}