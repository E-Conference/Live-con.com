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

    getImportedLog : function(){
        return Mapper["importedLog"];
    },
    setImportedLog : function(importedLog){
        Mapper["importedLog"] = importedLog;
    },

    getNotImportedLog : function(){
        return Mapper["notImportedLog"];
    },
    setNotImportedLog : function(notImportedLog){
        Mapper["notImportedLog"] = notImportedLog;
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
        Mapper["mappingConfig"] = baseConfig || Mapper.defaultNodeReadingConfig;
		Importer().setMappingConfig(Mapper["mappingConfig"]);
        if (!$data)$data = Mapper["data"];
        console.log("mapping : ",$data);
        Mapper.dataLinks= {};
        Mapper.knownNodes = {};
        Mapper.knownCollection = {};
        
        var nodePath = "root";
        var html = Mapper.getPanelHtml("Found data ",{panelClass:"panel-primary"});

        generateHtml($data,nodePath,Mapper.knownNodes); 
        function generateHtml($node,nodePath,knownNodes){

            html+= Mapper.getAttributesHtml($node,nodePath); 

            if($node.children().length != 0 ){
                var childTags = [];
                $node.children().each(function(index,child){

                    if(!Mapper.isNodeKnown(nodePath+ "/"+getNodeName(child))){
                        childTags.push(getNodeName(child));
                        html+= Mapper.getPanelHtml(getNodeName(child),{panelClass:"panel-success",margin:true,"node-path":nodePath+ "/"+getNodeName(child),collapsible:true,collapsed:true});
                        generateHtml($(child),nodePath+ "/"+getNodeName(child),knownNodes)
                        html+= Mapper.getClosingPanelHtml(); 
                    } else{
                        //already mapped
                        // addMappingCollection(nodePath); 
                    } 
                    generateHtml($(child),nodePath+ "/"+getNodeName(child),knownNodes);

                });
                //TODO : review the to find collections
                //TODO : review the to find collections
                //TODO : review the to find collections
                if(childTags.length==1)addMappingCollection(nodePath,childTags); 
            }else{
                html+= Mapper.generateNode($node,nodePath); 
            }

            function addMappingCollection(nodePath,childTags){
                console.log("childTags of "+ nodePath, childTags);
                Mapper.knownCollection[nodePath] = {};
            }
            function getNodeName(node){
                
                var nodeName = Importer().doFormat(node,Mapper.mappingConfig.getNodeName.format);
                
                return (nodeName ? nodeName.toLowerCase() : console.log("undefined nodename for",node));
            }
        }


        html+= Mapper.getClosingPanelHtml();
        Mapper["el"].html(html); 
        

        // option popover
        // var $nodeNameSelect = $('<select><option selected="selected" value="localname">Node tag</option>\
        //                                  <option value="rdfNodeName">Rdf tag</option></select>')
        //                             .select2()
        //                             .on("change",function(){
        //                                 if(localname == "localname"){
        //                                     Mapper.map(); format = [{
        //                                         nodeUtils : "localName", 
        //                                     }] 
        //                                 }
        //                             });
        // var clickablePopover = Mapper.addClickablePopover($(" <i class='fa fa-cog'> </i> "),"CACA")
        // $('#datafile-form > .panel-primary > .panel-heading > .panel-title').append(clickablePopover);
        

        //collection
        $('#datafile-form .panel').each(function(){
            var nodePath = $(this).data("node-path"); 
            if(Mapper.knownCollection[nodePath]){
                Mapper.knownCollection[nodePath] = $(this);
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

    //loop over the model panels  to build a mapping file
    generateMappingFile : function(){
        console.log("############### generateMappingFile starts")
        
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

                } 
            }) 
        });
        console.log("############### generateMappingFile ended, returning : ",Mapper.mappingConfig)
        return Mapper.mappingConfig;
        
        /**
         * Get or create the conference mapping object  
         * @return {[type]}            [description]
         */
        function getOrCreateConferenceMappingObj(){  
            if(!Mapper.mappingConfig['parseConference'])
                Mapper.mappingConfig['parseConference']={} 
            return Mapper.mappingConfig['parseConference']
        }
        
        /**
         * Get or create the mapping in the "under generating Mapper.mappingConfig file" phase corresponding to the array key  
         * @param  {object} format  the format to find
         * @param  {string} array   the array to link with in case of a not found format
         * @return {[type]}         the existing or new mapping
         */
        function getOrCreateMappingObjFromFormat(format,array){  
                if(!Mapper.mappingConfig['mappings'])
                    Mapper.mappingConfig['mappings']=[];
   
                 //look if its already registered
                    console.log("getOrCreateMappingObjFromFormat ",format)
                for(var i in Mapper.mappingConfig['mappings']){
                    console.log("getOrCreateMappingObjFromFormat ",Mapper.mappingConfig['mappings'][i].format) 
                    if(isSameFormat(Mapper.mappingConfig['mappings'][i].format,format)){ 
                        return Mapper.mappingConfig['mappings'][i];
                    }
                }

                //add if not found
                var newMapping = {array: array,label:{},format:format};
                Mapper.mappingConfig['mappings'].push(newMapping);
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
                }else if(splittedEntityMapping[i].charAt(0) == "@"){
                    var label = splittedEntityMapping[i] 
                    format.push({
                        nodeUtils : "attr",
                        arg : [label.substring(1)],
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
        if(!node.text() || node.text() == "")return"";
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
                    (op.collapsible===true?' ':'')+
                    '>'+ 
                  '<!-- Default panel contents -->'+
                  '<div class="panel-heading" '+
                  (op.collapsible===true?
                        (op.collapsed===true?'data-collapsed="true"':'data-collapsed="false"')
                        +'style="cursor: pointer;" onclick="(!$(this).data(\'collapsed\') ? $(this).find(\'> .panel-title i\').removeClass(\'fa-chevron-down\').addClass(\'fa-chevron-up\').parent().parent().siblings(\'ul\').hide(\'slow\')\
                                                                  : $(this).find(\'> .panel-title i\').removeClass(\'fa-chevron-up\').addClass(\'fa-chevron-down\').parent().parent().siblings(\'ul\').show(\'slow\'));$(this).data(\'collapsed\',!$(this).data(\'collapsed\'));"> ':'')+
                  
                  '<h3 class="panel-title">'+
                        content+
                        (op.collapsible===true?' <i class="fa '+(op.collapsed===true?'fa-chevron-up':'fa-chevron-down')+'"/> ':'')+
                    '</h3></div>'+
                  '<ul class="'+(op.padding===true?"panel-body ":"")+'list-group" '+(op.collapsed===true?'style="display:none;"':'')+'> ';
    },

    getClosingPanelHtml : function(){
        return ' </ul></div>';
    }, 
    addClickablePopover : function($div,htmlContent){
        //popover qui reste tant que le curseur ne quitte pas la zone bouton+popover (par defaut => disparait quand entre dans le popover...)
        return $div.popover({
            trigger: 'manual', 
            placement:"right",
            html:"true",
            title:"<i class='fa fa-cog'></i> file reader options",
            content:"<p class='preview-popover-content'>The preview may not be up to date because it isn't saved.</p>",
            template: '<div style="color:#333;" class="popover" onmouseover="clearTimeout(timeoutObj);$(this).mouseleave(function() {$(this).hide();});"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
            }).mouseenter(function(e) {
                $(this).popover('show');
                $div.siblings(".popover").find(".preview-popover-content").html(htmlContent);
            }).mouseleave(function(e) {
                var ref = $(this);
                timeoutObj = setTimeout(function(){
                    ref.popover('hide');
                }, 50);
            });
    }
}