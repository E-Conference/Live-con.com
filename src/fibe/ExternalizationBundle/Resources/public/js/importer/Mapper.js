Mapper = {


	initialize : function (file){
		Mapper["nodeTab"] = [];
		Mapper["file"] = file;
	},


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
		
		var precedingNodeName = "";
        if (!$data)$data = Mapper["data"];
		$data.children().each(function(index,node){ 
			if(node.tagName != precedingNodeName ){
	       		var newNode = Mapper.generateNode(node);
	       		Mapper["el"].append(newNode);
	       		if($(node).children().length != 0){
	       			Mapper.map($(node), newNode);
	       		}
	       	}
	       	precedingNodeName = node.tagName;
        });


	},

	generateNode : function(node){
		return $("<div class='well'>"+node.tagName+"</div>");

	},

    getKnownMapping : function(format){

            var knownFormatConfig = { 
                'swc': rdfConfig,
                'ocs': ocsConfig,
            }

            if(!knownFormatConfig[format])return console.warn("unknown format : "+format);

            return knownFormatConfig[format]; 
    }






}