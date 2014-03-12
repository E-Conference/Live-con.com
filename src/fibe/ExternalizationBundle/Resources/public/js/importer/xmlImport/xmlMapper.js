xmlMapper = {

 
 

    readFile : function(file,reader){   
        reader.readAsText(file); 
    },

    defaultMapping : {
        util : "xmlUtil",
        getNodeKey : {
            format : [{
                fn : "attr",
                arg : ["rdf:about"],
            }] 
        }, 
        getNodeName : 
{            format : [{
                fn : "rdfNodeName", 
            }] 
        },  
    },
	map : function(data,nodePath,$el,nodeCallBack,entryCallBack){
        var $data = $(data);
        console.log("mapping : ",$data); 
           
        generateHtml($data,nodePath,$el); 
        
        $(xmlMapper).trigger("mapEnd"); 

        function generateHtml($node,nodePath,$el){
  

            if($node.children().length > 1){
                var childrenNodePath = [];

                $node.children().each(function(index,child){
                    // $(xmlMapper).trigger("entry",[nodePath+ "/"+getNodeName(child),currentLine[tab]]);
                    var childNodeName = getNodeName(child);
                    var childNodePath = nodePath+ "/"+childNodeName; 
                    var panelTmp = nodeCallBack(childNodePath,$el,childNodeName);
                    if(panelTmp != $el){
                        childrenNodePath.push(childNodePath);
                        generateHtml($(child),childNodePath,panelTmp);
                    } else{
                        //already mapped
                        // mapper.addMappingCollection(nodePath); 
                    } 
                    generateHtml($(child),childNodePath,$el);

                });
                mapper.checkIfMappingCollection(nodePath,childrenNodePath);
            }else{
                if($node.text() && $node.text() != "")
                    entryCallBack(nodePath,$el,$node.text()); 
            }

            function getNodeName(node){
                
                var nodeName = Importer().doFormat(node,xmlMapper.mappingConfig.getNodeName.format);
                
                return (nodeName ? nodeName.toLowerCase() : console.log("undefined nodename for",node));
            }
        } 

        
	},  
}