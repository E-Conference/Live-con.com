xlsxMapper = {

	readFile : function(file,reader){ 

        reader.onload = function(e) { 
            var data = e.target.result
            if(typeof Worker !== 'undefined') { 
               xlsxMapper.xlsxworker(data,process_wb);
            } else {
                    //var wb = XLSX.read(data, {type: 'binary'});
                    var arr = String.fromCharCode.apply(null, new Uint8Array(data));
                    var wb = XLSX.read(btoa(arr), {type: 'base64'});
                    process_wb(wb);
            }

            function process_wb(wb) {
                var data = "";
                var type = "json";

                switch(type) {
                        case "json":
                            data = xlsxMapper.to_json(wb);
                                break;
                        case "form":
                            data = to_formulae(wb);
                            break; 
                        default:
                            data = to_csv(wb); 
                }
                $(xlsxMapper).trigger("fileRead",[data.Sheet1 || data]); 
            } 
        }
        reader.readAsArrayBuffer(file);
    }, 
 
    defaultMapping : {
        util : "xlsUtil",
        getNodeKey : {
            format : [{
                fn : "generate"
            }] 
        }, 
        getNodeName : {
            format : [{
                fn : "index"
            }] 
        }
    },

    map : function(data,nodePath,$el,nodeCallBack,entryCallBack){

        // add root node like as a collection to permit getting 
        //  the nodePtyPath value ( like /name/text ) in mapper.generateMappingFile() 
        mapper.checkIfMappingCollection(nodePath,[""]);
        $el = nodeCallBack(nodePath,$el,tab,{panelClass:"panel-success",margin:true,collapsible:true,collapsed:false});
        
        //Viewing all lines in the json return file  
        for(var i = 0; i < data.length; i++){
            var currentLine = data[i];
            //Viewing all property of a line

            for(var tab in currentLine){

                if(tab != "__rowNum__"){ 
                    var childNodePath = nodePath+ "/"+tab; 
                    var $panel = nodeCallBack(childNodePath,$el,tab,{panelClass:"panel-success",margin:true,collapsible:true,collapsed:false},true);
                    entryCallBack(childNodePath,$panel,currentLine[tab]);
                }
            }
        }
        $(xlsxMapper).trigger("mapEnd");   
    }, 

    // *required by Importer internal*  
    getNodeName : function(node,index){  
        if(!index) console.log("undefined nodename for",node)
        return index;
    },

    // *required by Importer internal* 
    getNbRootChildren : function(node){  
                return node.length;
    },

    utils : {
        // get specific children in a nodeSet ( case sensitive )
        //arg[0] string : contains the seeked children nodeName. if undefined returns all
        //arg[1] bool   : option to match with substring containment
        //arg[2] bool   : is root node
        children : function(node,arg){
            var rtnNodeSet= [],
                seekAllChar = '*',
                seekedChildNodeName =   arg && arg[0] ? arg[0] : seekAllChar 
            if(seekedChildNodeName==seekAllChar)return node;
            var matchTest =  arg && arg[1] === true
                            ? function(a,b){ return a.indexOf(b) > -1}
                            : function(a,b){ return a === b}; 
            for (var i in node){
                for (var j in node[i]){
                    if(j!="__rowNum__" &&  matchTest(j,seekedChildNodeName) ){ 
                        rtnNodeSet.push(node[i][j]);
                    }
                }   
                // if(node && (seekedChildNodeName==seekAllChar || matchTest(node,seekedChildNodeName))){ 
                //     rtnNodeSet.push(node[i]);
                // }
            }   
            return rtnNodeSet;
        },
        index : function(node){
            return node["nodeName"];
        },
        text : function(node){  
            return node;
        },
        //get a random key because we don't care
        generate : function(node){
            return Math.floor((Math.random()*9999999999999999));
        }
    },





    setWorker : function (worker){ 
        xlsxMapper.worker     = worker;
    },

    xlsxworker : function(data, cb) {
          
            xlsxMapper.worker.onmessage = function(e) {
                    switch(e.data.t) {
                            case 'ready': break;
                            case 'e': console.error(e.data.d);
                            case 'xlsx': cb(JSON.parse(e.data.d)); break;
                    }
            };
            var arr = btoa(String.fromCharCode.apply(null, new Uint8Array(data)));
            xlsxMapper.worker.postMessage(arr);
    },

    to_json : function (workbook) {
            var result = {};
            workbook.SheetNames.forEach(function(sheetName) {
                    var roa = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                    if(roa.length > 0){
                            result[sheetName] = roa;
                    }
            });
            return result;
    },

    to_csv : function (workbook) {
            var result = [];
            workbook.SheetNames.forEach(function(sheetName) {
                    var csv = XLSX.utils.sheet_to_csv(workbook.Sheets[sheetName]);
                    if(csv.length > 0){
                            result.push("SHEET: " + sheetName);
                            result.push("");
                            result.push(csv);
                    }
            });
            return result.join("\n");
    },

   to_formulae : function (workbook) {
            var result = [];
            workbook.SheetNames.forEach(function(sheetName) {
                    var formulae = XLSX.utils.get_formulae(workbook.Sheets[sheetName]);
                    if(formulae.length > 0){
                            result.push("SHEET: " + sheetName);
                            result.push("");
                            result.push(formulae.join("\n"));
                    }
            });
            return result.join("\n");
    },  
}