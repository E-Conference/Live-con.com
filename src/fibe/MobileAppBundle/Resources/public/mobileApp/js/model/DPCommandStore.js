  /**   
 *   Copyright <c> Claude Bernard - University Lyon 1 -  2013
 *   Author: Lionnel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan, Nicolas ARMANDO
 *   Description: Command store for the DataPaper datasource. Those information can be a lot of different thing, an email, a pdf link, a telephone number depending on the entity browsed.
 *   Version: 1.2
 *   Tags:  REST, AJAX
 **/
 define(['jquery', 'underscore', 'encoder', 'view/ViewAdapterText', 'localStorage/localStorageManager'], function($, _, Encoder, ViewAdapterText, StorageManager){
	 var DPCommandStore = {
	  
		/** This command is used to retrieve complementary informations of a publication **/
		getDataPaperRessource : {
			dataType : "JSONP",
			method : "GET",
			getQuery : function(parameters){
						var  ajaxData = 'key=["'+parameters.uri+'","document"]';
						return ajaxData;
			},
											
			ModelCallBack : function (dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				var JSONToken = {};
				if(dataJSON.rows.length>0){
					JSONToken.ressource  = dataJSON.rows;
				}
				JSONfile[0] = JSONToken;
				//StorageManager.pushCommandToStorage(currentUri,"getDataPaperRessource",JSONfile);
				return JSONfile;
			},
				
				
			ViewCallBack : function(parameters){
				if( parameters.JSONdata!= null){
					var dataPaper = parameters.JSONdata;
				
					if(dataPaper[0].hasOwnProperty("ressource")){
						if(dataPaper[0].ressource.length>0){
							if(parameters.mode == "text"){
								var out="<table>";
								for(i=0;i<dataPaper[0].ressource.length;i++){
									out+="<tr><td>"+dataPaper[0].ressource[i].value.description+"</td><td>"+'<a href="'+dataPaper[0].ressource[i].value.url+'" data-role="button" >'+dataPaper[0].ressource[i].value.type+'</a></td></tr>';
								}
								out+="</table>";
								parameters.contentEl.append('<h2>Ressource</h2>');
								parameters.contentEl.append(out);	
							}else{
								for(i=0;i<dataPaper[0].ressource.length;i++){
									ViewAdapterGraph.addNode("Ressource "+dataPaper[0].ressource[i].value.type+' '+dataPaper[0].ressource[i].value.description, dataPaper[0].ressource[i].value.url);
								}
							
							}
						}
					}
				}
			}   
		},

		/** This command is used to retrieve contact informations **/
		getDataPaperAuthor : {
			dataType : "JSONP",
			method : "GET",
			getQuery : function(parameters){ 
						var  ajaxData = 'key=["'+parameters.uri+'","user-information"]';
						return ajaxData
			},							
			ModelCallBack : function (dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				var JSONToken = {};
				if(dataJSON.rows.length>0){
					JSONToken.ressource  = dataJSON.rows;
				}
				JSONfile[0] = JSONToken;
				//StorageManager.pushCommandToStorage(currentUri,"getDataPaperAuthor",JSONfile);
				return JSONfile;
			},	
			ViewCallBack : function(parameters){
				if( parameters.JSONdata!= null){
					var dataPaper = parameters.JSONdata;
					if(dataPaper[0].hasOwnProperty("ressource")){
						if(dataPaper[0].ressource.length>0){
							if(parameters.mode == "text"){
								var out="<table>";
								for(i=0;i<dataPaper[0].ressource.length;i++){
									if(dataPaper[0].ressource[i].value.type==="user-photo"){
										parameters.contentEl.append('<figure style="height:120px; width:120px; "><img style="height:100%; width:100%; clear:left;" src="'+dataPaper[0].ressource[i].value.url+'" alt="'+dataPaper[0].ressource[i].value.description+'">  </figure>');
									}else if(dataPaper[0].ressource[i].value.type==="user-mail"){
										out+="<tr><td>"+dataPaper[0].ressource[i].value.description+"</td><td>"+'<address><a href="maito:'+dataPaper[0].ressource[i].value.url+'">'+dataPaper[0].ressource[i].value.url+'</a></address></td></tr>';
									}else{
										out+="<tr><td>"+dataPaper[0].ressource[i].value.description+"</td><td>"+'<a href="'+dataPaper[0].ressource[i].value.url+'" data-role="button" >'+dataPaper[0].ressource[i].value.type.replace('user-','')+'</a></td></tr>';
									}
								}
								out+="</table>";
								parameters.contentEl.append('<h2>Contact</h2>');
								parameters.contentEl.append(out);	
							}
						}
					}
				}
			}   
		}
	};

	return DPCommandStore;
});