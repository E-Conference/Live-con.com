Mapper = {


	initialize : function (file){
		Mapper["nodeTab"] = [];
		Mapper["file"] = file;
	},

    //read a file and load it in Mapper["data"];
	read : function(el){
	    var reader = new FileReader();
     
        reader.onload = function(e) { 
        	Mapper["data"] = $(e.target.result);
        	Mapper["el"] = $(el);
            $(Mapper).trigger("fileRead",[Mapper["data"]]);
        } 
        reader.readAsText(Mapper["file"]);  
	},
    getSerialisedDatas : function(){
        return Mapper["serialisedDatas"];
    },
    setSerialisedDatas : function(datas){
        Mapper["serialisedDatas"] = datas;
    },

	map : function($data){
		 
        if (!$data)$data = Mapper["data"];
        var nodePath = "root";
        var html = Mapper.getPanelHtml("Found datas",{panelClass:"panel-primary","node-path":nodePath});

        console.log($data);
        Mapper.dataLinks= {};
        Mapper.knownNodes = {};
        Mapper.knownCollection = {};
        generateHtml($data,nodePath,Mapper.knownNodes); 
        function generateHtml($node,nodePath,knownNodes){

            html+= Mapper.getAttributesHtml($node,nodePath); 

            if($node.children().length != 0 ){
                var childTags = [];
                $node.children().each(function(index,child){

                    if(!Mapper.isNodeKnown(nodePath+ "/"+getNodeName(child))){
                        childTags.push(getNodeName(child));
                        html+= Mapper.getPanelHtml(child.tagName,{panelClass:"panel-success",margin:true,"node-path":nodePath+ "/"+getNodeName(child)});
                        generateHtml($(child),nodePath+ "/"+getNodeName(child),knownNodes)
                        html+= Mapper.getClosingPanelHtml(); 
                    } else{
                        //already mapped
                    } 
                    generateHtml($(child),nodePath+ "/"+getNodeName(child),knownNodes);

                });
                if(childTags.length==1)addMappingCollection(nodePath,childTags); 
            }else{
                html+= Mapper.generateNode($node,nodePath); 
            }

            function addMappingCollection(nodePath,childTags){
                console.log("childTags of "+ nodePath, childTags);
                Mapper.knownCollection[nodePath] = {};
            }
            function getNodeName(node){
                return node.tagName.toLowerCase();
            }
        }


        html+= Mapper.getClosingPanelHtml();
        Mapper["el"].html(html);
        //             .children(".collapsible").click(function(){console.log(this);alert("lol");
        //                 $(this).siblings("ul").collapsible("toggle")
        //             })
        

        //collection
        $('#datafile-form .panel').each(function(){
            var nodePath = $(this).data("node-path"); 
            if(Mapper.knownCollection[nodePath]){
                Mapper.knownCollection[nodePath] = $(this);
                var collectionNodeName = $(this).find("> .panel-heading").text();
                $(this).find("> .panel-heading").remove();
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

            Mapper.knownNodes[nodePath]["div"] = $(this);

            //popover
            var samples = Mapper.knownNodes[nodePath].samples;
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

    generateMappingFile : function(){
        console.log("############### generateMappingFile starts")
        var mappingConfig = {
            rootNode : {
                format : [{
                    nodeUtils : "node",
                    arg : ["conference"],
                }]
            },
            getNodeKey : {
                format : [{
                    nodeUtils : "attr",
                    arg : ["id"],
                }]
            },
            getNodeName : {
                format : [{
                    nodeUtils : "localName",
                }]
            }, 
        };
        //loop only on validated mapping
        $("#model-form .panel-success").each(function(iPanel,panel){
            var modelName = $(panel).data("model-path");
            // get the corresponding model mapping config
            $(panel).find(".list-group-item-success").each(function(){ 
                var leftEntityMapping;
                for (var i in Mapper.dataLinks){
                    if(i == $(this).data("model-path")){
                        leftEntityMapping = Mapper.dataLinks[i];
                    }
                }
                console.log("leftEntityMapping:",leftEntityMapping)

                var leftCollectionPath = getLeftCollectionPath(leftEntityMapping.nodePath);


                var modelSetter = Model.getSetter(modelName,$(this).data("model-path").split("/")[1])
                
                
                //check if this is the conference mapping
                if(modelName=="Conference"){
                    //the conference mapping has a different mapping object
                    var mappingObj = getOrCreateConferenceMappingObj(modelName);
                    mappingObj[modelSetter]={};
                    mappingObj[modelSetter]["format"] = extractMappingFormat(leftEntityMapping.nodePath);
                    
                }else{ 
                    var nodePtyPath = leftEntityMapping.nodePath.split(leftCollectionPath).join(""); 
                    var nodeName = nodePtyPath.split("/")[0] != "" ?nodePtyPath.split("/")[0] : nodePtyPath.split("/")[1];

                    var mappingObj = getOrCreateMappingObjFromFormat(extractMappingFormat(leftCollectionPath,true),modelName); 

                    mappingObj.label[nodeName]={setter:modelSetter};
                    mappingObj.label[nodeName]["format"] = extractMappingFormat(nodePtyPath.split(nodeName).join(""));

                    //TODO add another mapping object in case of a new nodeName
                    //TODO add another mapping object in case of a new nodeName
                    // mappingObj["nodeName"] = leftEntityMapping.nodePath.split("/")[1];
                } 
            }) 
        });
        console.log("############### generateMappingFile ended, returning : ",mappingConfig)
        return mappingConfig;
        
        /**
         * Get or create the conference mapping object  
         * @return {[type]}            [description]
         */
        function getOrCreateConferenceMappingObj(){  
            if(!mappingConfig['parseConference'])
                mappingConfig['parseConference']={} 
            return mappingConfig['parseConference']
        }
        
        /**
         * Get or create the mapping in the "under generating mappingConfig file" phase corresponding to the array key  
         * @param  {object} format  the format to find
         * @param  {string} array   the array to link with in case of a not found format
         * @return {[type]}         the existing or new mapping
         */
        function getOrCreateMappingObjFromFormat(format,array){  
                if(!mappingConfig['mappings'])
                    mappingConfig['mappings']=[];
   
                 //look if its already registered
                    console.log("getOrCreateMappingObjFromFormat ",format)
                for(var i in mappingConfig['mappings']){
                    console.log("getOrCreateMappingObjFromFormat ",mappingConfig['mappings'][i].format)
                    alert(mappingConfig['mappings'][i].format ==  format)
                    if(mappingConfig['mappings'][i].format ==  format){
                        return mappingConfig['mappings'][i];
                    }
                }

                //add if not found
                var newMapping = {array: array,label:{},format:format};
                mappingConfig['mappings'].push(newMapping);
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
                        nodeUtils : "text"
                    })
                }else if(collectionMapping){
                    var label = splittedEntityMapping[i]
                    format.push({
                        nodeUtils : "children",
                        arg : [label],
                    })
                }else {
                    var label = splittedEntityMapping[i]
                    format.push({
                        nodeUtils : "child",
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
            if(Mapper.knownCollection[nodePath])
                return nodePath;
            var $node = Mapper.knownNodes[nodePath].div;
            if($node){
                var found = false;
                while(true){
                    var parent = $node.parent();
                    if(parent.length==0){
                        return false; //stop loop if no more parent
                    }else{
                        var parentPath = parent.data("node-path");
                        if(parentPath && Mapper.knownCollection[parentPath])
                            return nodePath;
                        $node = parent;
                        nodePath = parentPath || nodePath;
                    }

                }
            }

        }
    },


    isNodeKnown : function(nodePath,sample){
        if(!Mapper.knownNodes[nodePath]){ 
            console.log("adding "+nodePath);
            Mapper.knownNodes[nodePath] = {samples:[]};
            addSample(nodePath,sample);
            return false;
        }
        addSample(nodePath,sample);
        
        return true;

        function addSample(nodePath,sample){
            if(sample && $.inArray(sample, Mapper.knownNodes[nodePath].samples) === -1){
                Mapper.knownNodes[nodePath].samples.push(sample);  
            } 

        }
    },

    getEntityMapping : function(nodePath){ 
        console.log("getEntityMapping",nodePath,Mapper.dataLinks)
        var rtn = {};
        for(var i in Mapper.dataLinks){
            var link = Mapper.dataLinks[i];
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
        var rtn = "";

        if(!Mapper.isNodeKnown(nodePath + "/text",node.text())){ 
            rtn += '<li data-node-path="'+nodePath+'/text" class="map-node list-group-item list-group-item-warning">text</li>';
        } 
        return rtn;
    },

    getAttributesHtml : function(node,nodePath){
        var rtn = "";  
        if(!node[0].attributes || node[0].attributes.length==0)return rtn;
        $.each(node[0].attributes, function() { 

            if(!Mapper.isNodeKnown(nodePath + "/@" + this.name.toLowerCase(),this.value)){ 
                rtn += '<li data-node-path="'+nodePath+ "/@" + this.name+'" class="map-node list-group-item list-group-item-warning">@'+this.name+"</li>";
            }
        }); 
        return rtn;
    },
    
    getPanelHtml : function(content,op){
        if(!op)op={};
        return '<div class="panel '+
                    (op.panelClass || "panel-default")+'"'+
                    (op["model-path"]?' data-model-path="'+op["model-path"]+'"':"")+
                    (op["node-path"]?' data-node-path="'+op["node-path"]+'"':"")+
                    (op.margin===true?' style="margin:15px;"':'')+
                    '>\
                  <!-- Default panel contents -->\
                  <div class="panel-heading"><h3 class="panel-title">'+content+'</h3></div>\
                  <ul class="'+(op.padding===true?"panel-body ":"")+'list-group"> ';
    },

    getClosingPanelHtml : function(){
        return ' </ul></div>';
    },

}