xmlMapper = {

 
 

    readFile : function(file,reader){   
        reader.onload = function(e) { 
            $(xmlMapper).trigger("fileRead",[e.target.result]);  
        }
        reader.readAsText(file);
    },

    defaultMapping : {
        util : "xmlUtil",
        getNodeKey : {
            format : [{
                fn : "attr",
                arg : ["rdf:about"]
            }]
        },
        getNodeName : {
            format : [{
                fn : "rdfNodeName"
            }] 
        }
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
                    var childNodeName = xmlMapper.getNodeName(child);
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
 
        }
	},  

    // *required by Importer internal* 
    getNodeName : function(node){
                var nodeName = Importer().doFormat(node,Importer().mappingConfig.getNodeName.format);
                
                return (nodeName ? nodeName.toLowerCase() : console.log("undefined nodename for",node));
    },

    // *required by Importer internal* 
    getNbRootChildren : function(node){  
                return $(node).children().length
    },

     //data manipulation functions used by Importer().doFormat
    utils : {  
        /*********************  node manipulation : return a string *******************/
        //get a rdf nodeName
        rdfNodeName : function(node,arg){
            var node = $(node)[0];
            if(!node.localName)return;//comment
            var uri=[];
            var rtn;

            //first, look for  <rdf:type> child(ren)
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
        text : function(node){
            return $(node).text();
        },
        localName : function(node){
            return node.localName;
        },
        // get a specific attr for the given node
        //arg[0] must contain the wanted attr
        attr : function(node,arg){
            return $(node).attr(arg[0]);
        },

        /********************* nodeSet && node manipulation : return jquery Node or NodeSet *******************/
        
        // get a specific node in a nodeSet
        //arg[0] must contain the wanted nodeName
        node : function(nodes,arg){
            var rtnNode;
            var seekedNodeName = arg[0].toLowerCase();
            $(nodes).each(function(){ 
                var nodeName = xmlMapper.getNodeName(this);  
                if(nodeName && nodeName.toLowerCase() === seekedNodeName){
                    rtnNode = $(this);
                }
            })
            return rtnNode;
        }, 
        // get specific children in a nodeSet ( case sensitive )
        //arg[0] string : contains the seeked children nodeName. if undefined returns all
        //arg[1] bool   : option to match with substring containment
        children : function(node,arg){
            if(!arg)return $(node).children();
            var rtnNodeSet= [],
                seekAllChar = '*',
                seekedChildNodeName = arg[0] 
                                        ? arg[0].toLowerCase() 
                                        : seekAllChar,
                matchTest =  arg[1] === true 
                                ? function(a,b){return a.indexOf(b) > -1} 
                                : function(a,b){ return a === b};

            $.each(node,function(){
                $(this).children().each(function(){
                    var childNodeName = xmlMapper.getNodeName(this); 
                    if(childNodeName && matchTest(childNodeName,seekedChildNodeName)){ 
                        rtnNodeSet.push($(this));
                    } 
                })
            })
            return $(rtnNodeSet);
        }, 
        nbChildren : function(node){ 
            return $(node).children().length;
        },

        /********************* nodeSet && node manipulation : return jquery Node *******************/
        
        // get the first specific child in a nodeSet ( case insensitive )
        //arg[0] must contain the wanted child nodeName 
        child : function(node,arg){
            // return $(node).children(childNodeName);
            var rtnNode;
            var seekedChildNodeName = arg[0].toLowerCase();
            $(node).children().each(function(){
                if(rtnNode)return;
                var childNodeName = xmlMapper.getNodeName(this);
                if(childNodeName && childNodeName === seekedChildNodeName){
                    rtnNode = $(this);
                }
            })
            return rtnNode;
        }
    }

}