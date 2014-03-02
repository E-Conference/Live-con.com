xlsxMapper = {

    initialize : function (worker, file){
        xlsxMapper["nodeTab"] = [];
        xlsxMapper["file"] = file;
        xlsxMapper.knownNodes = [];
        xlsxMapper.dataLinks = [];
        xlsxMapper.worker = worker;
        xlsxMapper.jsonFile = {};
    },

	read : function(el){
          
	    var reader = new FileReader();
        xlsxMapper["el"] = $(el);
        reader.onload = function(e) {


                xlsxMapper["data"] = e.target.result;
                xlsxMapper["el"] = $(el);
                $(xlsxMapper).trigger("fileRead",e.target.result);

              
        };
        //reader.readAsBinaryString(f);
        reader.readAsArrayBuffer(xlsxMapper["file"]);
	},

    map : function(data,baseConfig){

         if(typeof Worker !== 'undefined') {
  
               xlsxMapper.xlsxworker(data,xlsxMapper.process_wb);
        } else {
                //var wb = XLSX.read(data, {type: 'binary'});
                var arr = String.fromCharCode.apply(null, new Uint8Array(data));
                var wb = XLSX.read(btoa(arr), {type: 'base64'});
                process_wb(wb);
        }

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

    get_radio_value : function ( radioName ) {
            var radios = document.getElementsByName( radioName );
            for( var i = 0; i < radios.length; i++ ) {
                    if( radios[i].checked ) {
                            return radios[i].value;
                    }
            }
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


    b64it : function () {
            var tarea = document.getElementById('b64data');
            var wb = XLSX.read(tarea.value, {type: 'base64'});
            process_wb(wb);
    },

    generateNode : function(nodeId){
      
        var rtn = "";
        rtn += '<li data-node-path="'+nodeId+'" class="map-node list-group-item list-group-item-warning">text</li>';

        return rtn;
    },

    process_wb : function (wb) {
            var output = "";
            var type = "json";

            switch(type) {
                    case "json":
                    output = xlsxMapper.to_json(wb);
                            break;
                    case "form":
                            output = to_formulae(wb);
                            break; 
                    default:
                    output = to_csv(wb);
            }
            var globalPanel = Pager.getPanelHtml("Found data",{panelClass:"panel-primary",margin:false,collapsible:false,collapsed:false});
               
            xlsxMapper.knownNodes = [];
            xlsxMapper.file = output.Sheet1;
            //Viewing all lines in the json return file  
             for(var i = 0; i < output.Sheet1.length; i++){
                var currentLine = output.Sheet1[i];
                //Viewing all property of a line
                 for(var tab in currentLine){

                    if(tab != "__rowNum__" && !xlsxMapper.knownNodes[tab]){

                        xlsxMapper.knownNodes[tab] = currentLine[tab];
                        var panel = Pager.getPanelHtml(tab,{panelClass:"panel-success",margin:true,collapsible:false,collapsed:false});
                        var node = $(xlsxMapper.generateNode(tab));
                        panel.append(node);
                        globalPanel.append(panel);
                    }
                }
            }
            xlsxMapper["el"].append(globalPanel);
            xlsxMapper.addDraggableHandler();

            return output;
    },

    addDraggableHandler : function(){

          $('.map-node').each(function(){

            var nodePath = $(this).data("node-path");

            var content =  xlsxMapper.knownNodes[nodePath];
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
        
          //loop only on validated mapping
        var mappedModel = $("#model-form .panel-success");

        if(mappedModel.length == 0) bootstrapAlert("Error",'Your mapping is incomplete ...',"Error : ","<i class='fa-2x fa fa-spinner fa-spin'></i>");

        var mappingFile = [];
        mappedModel.each(function(iPanel,panel){
            var modelName = $(panel).data("model-path");
            var mappingObj = {'modelId': modelName};
            var mappedProperties = [];
            $(panel).find(".list-group-item-success").each(function(){ 
                var propertyName = $(this).data("model-path").split('/')[1];
                var nodePath = $(this).data("node-path");
                var modelSetter = $(this).data("model-setter");

                var mappingProperty = {'modelSetter':modelSetter, 'nodePath' : nodePath }; 

                mappedProperties.push(mappingProperty);
                
            }) 
            mappingObj.mappedProperties = mappedProperties;
            mappingFile.push(mappingObj);
        });

        console.log("Mapped properties : ");
        console.log(mappingFile);
    }

}