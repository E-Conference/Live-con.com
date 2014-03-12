xlsxMapper = {

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

	readFile : function(file,reader){  
        reader.readAsArrayBuffer(file);
	},
 
    defaultMapping : {
        util : "xlsUtil",
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

    map : function(data,nodePath,$el,nodeCallBack,entryCallBack){
 

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
            //TODO : add root node like a noob as a collection to permit getting 
            //       the nodePtyPath value like /name/text ( in mapper.generateMappingFile())
            mapper.checkIfMappingCollection(nodePath,[""]);
            $el = nodeCallBack(nodePath,$el,tab,{panelClass:"panel-success",margin:true,collapsible:true,collapsed:false});
            
            //Viewing all lines in the json return file  
            for(var i = 0; i < data.Sheet1.length; i++){
                var currentLine = data.Sheet1[i];
                //Viewing all property of a line

                for(var tab in currentLine){

                    if(tab != "__rowNum__"){ 
                        var childNodePath = nodePath+ "/"+tab; 
                        var $panel = nodeCallBack(childNodePath,$el,tab,{panelClass:"panel-success",margin:true,collapsible:true,collapsed:false});
                        entryCallBack(childNodePath,$panel,currentLine[tab]);
                    }
                }
            }
            $(xlsxMapper).trigger("mapEnd");  
        }
    }, 

    // generateNode : function(nodeId){
      
    //     var rtn = "";
    //     rtn += '<li data-node-path="'+nodeId+'" class="map-node list-group-item list-group-item-warning">text</li>';

    //     return rtn;
    // }, 

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