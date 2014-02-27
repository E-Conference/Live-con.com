
xlsxImporter = {
	run : function(file,mapping,op,callback,fallback){

		if(!file){
            if(fallback!=undefined)fallback("Error with the file."); 
            return;
		} 

        objects["events"]         = [],
        objects["locations"]      = [],
        objects["xproperties"]    = [],
        objects["relations"]      = [],
        objects["categories"]     = [],
        objects["proceedings"]    = [],
        objects["persons"]        = [],
        objects["topics"]         = [],
        objects["proceedings"]    = [],
        objects["organizations"]  = [],
        objects["roles"]          = [], 
        objects["conference"]     = {},

        notImportedLog = [],
        importedLog    = [],
        objectMap      = {}, 
        objectsIndexes = {}, 
        fkMap          = [], 
        fkMapIndexes   = []; 


        

	}
} 