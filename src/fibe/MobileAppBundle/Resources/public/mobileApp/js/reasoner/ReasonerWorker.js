/**   
*	Copyright <c> Claude Bernard - University Lyon 1 -  2013
* 	License : This file is part of the DataConf application, which is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License. See details at : http://liris.cnrs.fr/lionel.medini/wiki/doku.php?id=dataconf&#licensing 
*   Author: Lionel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan
*   Description: message function for the reasoner dedicated worker
*	Version: 1.2				   
*   Tags:  WEB WORKERS, DEDICATED WORKER
**/

/*importScripts('processOntology.js');
importScripts('jsw.js', 'query.js', 'util-min.js', 'jswui.js');*/

define({basePath:"./lib"},['processOntoloy','jsw', 'query', 'utilMin', 'jswui' ], function(ontologyProcessor, jsw, query, utilMin, jswui){
	var  workerInterface = {
		/**
		* Convenient caller for the showResult function
		* only change the function call for a postMerssage call when moving it in the worker
		*/
		
		send : function(data) {
			postMessage(data);
		},

		onmessage : function(event) {
			if(event.data.command === "start") {
				ontologyProcessor.startReasoner(event.data.text);
			} else if(event.data.command === "process") {
				ontologyProcessor.queryReasoner({queryString :event.data.text,keyword : event.data.keyword,param: event.data.param, callback : event.data.callback});
			}
		}
	}
	return workerInterface;
});
