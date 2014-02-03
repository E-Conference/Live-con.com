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

        var html = Mapper.getPanelHtml("Found datas",{panelClass:"panel-primary"});

        console.log($data);
        Mapper.knownNodes = {};
        generateHtml($data,"root",Mapper.knownNodes); 
        function generateHtml($node,nodePath,knownNodes){

            html+= Mapper.getAttributesHtml($node,nodePath); 

            if($node.children().length != 0 ){
                $node.children().each(function(index,child){ 

                    if(!Mapper.isNodeKnown(nodePath+ "/"+child.tagName.toLowerCase())){
                        html+= Mapper.getPanelHtml(child.tagName,{panelClass:"panel-success",margin:true});
                        generateHtml($(child),nodePath+ "/"+child.tagName.toLowerCase(),knownNodes)
                        html+= Mapper.getClosingPanelHtml(); 
                    } else{
                        //already mapped
                    } 
                    generateHtml($(child),nodePath+ "/"+child.tagName.toLowerCase(),knownNodes);

                });
            }else{
                html+= Mapper.generateNode($node,nodePath); 
            }
        }


        html+= Mapper.getClosingPanelHtml();
        Mapper["el"].html(html)
        //             .children(".collapsible").click(function(){console.log(this);alert("lol");
        //                 $(this).siblings("ul").collapsible("toggle")
        //             })
        $('.map-node').each(function(){
            var nodePath = $(this).data("node-path");
            var samples = Mapper.knownNodes[nodePath];
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
            $(this).draggable({
                    zIndex: 999,
                    revert: true,      // will cause the event to go back to its
                    revertDuration: 0,  //  original position after the drag
                    helper: 'clone',
                    start : function (ev,ui){
                        // $(this).hide();   
                        // dragged = [ ui.helper[0], event ];
                        // setTimeout(function(){ //bug... event isn't yet updated  
                        //   $(self).trigger("drag",[event]); 
                        // },1);//event isn't yet updated   
                    },
                    stop: function(a,b,c){   
                        // // setTimeout(function(){ //bug... event isn't yet updated   
                        //   if(calendar_events_indexes[event.id] === undefined){
                        //     $(this).show()
                        //   }else{
                        //     // $(this).hide()
                        //   } 
                        // // },1);//event isn't yet updated   
                    } 
                  }) ;
        })
	},
    dataLinks:{},

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
            var entityName = $(panel).data("model-path");
            var entityMappingObj = getEntityMappingObj(entityName);
            // get the corresponding model mapping config
            $(panel).find(".list-group-item-success").each(function(){ 
                var entityMapping;
                for (var i in Mapper.dataLinks){
                    if(i == $(this).data("model-path")){
                        entityMapping = Mapper.dataLinks[i];
                    }
                } 
                // if(!entityMapping){
                //     //not mapped
                //     // console.log("mappingPath "+mappingPath+" not found in : ",Mapper.dataLinks) 
                //     return;
                // }
                console.log("entityMapping:",entityMapping)
                var setter = Model.getSetter(entityName,$(this).data("model-path").split("/")[1])
                //check if this is the conference mapping
                
                if(entityName=="Conference"){ 

                    var parseConferenceSetter = entityMappingObj[setter]={
                        format : [], 
                    }
                    extractFormat(parseConferenceSetter.format,entityMapping);
                    
                }else{ 
                     entityMappingObj.label[setter]={
                        format : [], 
                    };
                    extractFormat(entityMappingObj.label[setter].format,entityMapping);

                    entityMappingObj["nodeName"] = entityMapping.to.split("/")[1];
                    //TODO investigate for "wrapped"
                    // if(entityMapping.wrapped==true)alert("wrapped")
                } 
            }) 
        });
        console.log("############### generateMappingFile ended, returning : ",mappingConfig)
        return mappingConfig;
        
        function getEntityMappingObj(entityName){
            if(entityName=="Conference"){
                if(!mappingConfig['parseConference'])
                    mappingConfig['parseConference']={} 
                return mappingConfig['parseConference']
            }else{
                if(!mappingConfig['mappings'])
                    mappingConfig['mappings']=[];
                for(var i in mappingConfig['mappings']){
                    if(mappingConfig['mappings'][i].array == entityName.toLowerCase() ){
                        return mappingConfig['mappings'][i];
                    }
                }
                mappingConfig['mappings'].push({array:entityName.toLowerCase(),label:{}});
                return mappingConfig['mappings'][mappingConfig['mappings'].length-1];
            }
        }
        
        function extractFormat(format,mapping){
            var splittedEntityMapping = mapping.to.split("/");
            for(var i in splittedEntityMapping){
                if(i==0)continue;
                if(splittedEntityMapping[i]=="text"){
                    format.push({
                        nodeUtils : "text"
                    })
                }else{
                    var label = splittedEntityMapping[i]
                    format.push({
                        nodeUtils : "child",
                        arg : [label],
                    })
                }

            }
        }
    },


    isNodeKnown : function(nodePath,sample){
        if(!Mapper.knownNodes[nodePath]){ 
            console.log("adding "+nodePath);
            Mapper.knownNodes[nodePath] = [];
            addSample(nodePath,sample);
            return false;
        }
        addSample(nodePath,sample);
        
        return true;

        function addSample(nodePath,sample){
            if(sample && $.inArray(sample, Mapper.knownNodes[nodePath]) === -1){
                Mapper.knownNodes[nodePath].push(sample);  
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