/**   
*	Copyright <c> Claude Bernard - University Lyon 1 -  2013
* 	License : This file is part of the DataConf application, which is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License. See details at : http://liris.cnrs.fr/lionel.medini/wiki/doku.php?id=dataconf&#licensing 
*   Author: Lionel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan
*   Description: Object containing the communication interface with the reasoner web worker.
*   Version: 1.2
*   Tags:  MOBIL-REASONING, WEB WORKERS
**/
define(['jquery', 'underscore','reasonerWorker'], function($, _, reasonerWorker){
	
	var Reasoner = {
		
		startWorker : function(){
		
			worker = new Worker(reasonerWorker);
			
			worker.onmessage=function(event){
				console.log(event);
				console.log(event.data.results);
			
				if(event.data.callback != undefined){
					var callback;
					try {
						callback = eval(event.data.callback);
					} catch (ex) {
						callback = eval("(" + event.data.callback + ")");
					}
					callback(event.data.results,JSON.parse(event.data.param));
				}
			};
		},
		stopWorker : function () {
			worker.terminate();
		},
		sendRequest : function (queryString,keyword,parameters,fCallback){
			var callbackString = fCallback.toString();
			var parameterString = JSON.stringify(parameters);
			
			
			worker.postMessage({command: "process", text: queryString,keyword:keyword,param: parameterString, callback: callbackString});
			
		},
		initReasoner : function(){
			$.ajax({
				url: "js/reasoner/reasonerData.json",
				success:function(result){
					worker.postMessage({command: "start", text: result});
				},
				error : function(jqXHR, exception, erorThrown){
					console.log( erorThrown);
				}
			});
		},
	    initialize : function(){	
			Reasoner.startWorker();
			Reasoner.initReasoner();
			
	    },

		filterResult : function (keywordLabel, resultLabel){
			console.log(resultLabel);
			if(resultLabel == "http://www.w3.org/2002/07/owlThing" || resultLabel == "http://www.w3.org/2002/07/owl#Thing" || resultLabel =="http://proton.semanticweb.org/2005/04/protontObject" || resultLabel == keywordLabel || resultLabel == "http://proton.semanticweb.org/2005/04/protont#Object"){
				return false;
			}
			return true;
		},
		labelToUri:function(keywordLabel){
			return "#"+keywordLabel.split(" ").join("_");
		},
		
		UriToLabel:function(keywordUri){
			return keywordUri.replace('#','').split("_").join(" ");
		}
		
	}
	return Reasoner;
});