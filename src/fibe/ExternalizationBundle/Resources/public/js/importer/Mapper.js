Mapper = {


	initialize : function (file){
		this.nodeTab = [];
		this.file = file;
	},


	read : function(el){
	    var reader = new FileReader();
     
        reader.onload = function(e) {
        	debugger;
        	var $data = $(e.target.result);
        	var $el = $(el);
            Mapper.map( $data, $el );
        }

        reader.readAsText(this.file); 

	},

	map : function($data, $el){
		
		var precedingNodeName = "";
		$data.children().each(function(index,node){ 
			if(node.tagName != precedingNodeName ){
	       		var newNode = Mapper.generateNode(node);
	       		$el.append(newNode);
	       		if($(node).children().length != 0){
	       			Mapper.map($(node), newNode);
	       		}
	       	}
	       	precedingNodeName = node.tagName;
        });


	},

	generateNode : function(node){
		return $("<div class='well'>"+node.tagName+"</div>");

	}






}