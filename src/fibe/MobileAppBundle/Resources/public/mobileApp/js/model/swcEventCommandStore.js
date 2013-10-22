/**   
*	Copyright <c> Claude Bernard - University Lyon 1 -  2013
* 	License : This file is part of the DataConf application, which is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License. See details at : http://liris.cnrs.fr/lionel.medini/wiki/doku.php?id=dataconf&#licensing 
*   Author: Lionel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan
*   Description: This object contains a json definition of all the commands that will prepare all the queries we want to send on the SemanticWebDogFood sparql endpoint.
*				 Each one of those commands declare the datatype, the method, the query string it is supposed to use on the endpoint and provide a model Callback to store results, a view CallBack to render data stored.		
*				 To declare a request, each commands can use the parameters declared for the route they are called in (see Configuration.js). Those parameters can be a name, an uri or both and represents
*				 the entity which we want informations on. After calling a command, the results are stored using the localStorageManager (see localStorage.js) and rendered when needed. It is the role of the router to call those commands according to the configuration file.
*   Version: 1.1
*   Tags:  JSON, SPARQL, AJAX
**/
define(['jquery', 'underscore', 'encoder','view/ViewAdapter', 'view/ViewAdapterText', 'localStorage/localStorageManager','moment', 'lib/FileSaver'], function($, _, Encoder, ViewAdapter, ViewAdapterText, StorageManager, moment, FileSaver){
	var swcEventCommandStore = { 

		getAllEvents : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { id_person : 6} ;
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>Browse conference tracks</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}
					}
				} 
			}
		},


		getAllSpeakers : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_person.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { } ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.speakerName =  this.name || "";
					JSONToken.speakerDesc =  this.description || "";
					JSONToken.speakerHomepage =  this.homepage || "";
					JSONToken.speakerImg =  this.image || "";
					JSONToken.speakerTwitter =  this.twitter || "";
					JSONToken.speakerId =  this.id || "";
					JSONToken.speakerSlug =  this.slug || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getAllSpeakers",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendListImage(parameters.JSONdata,
													 {baseHref:'#person/',
													  hrefCllbck:function(str){return Encoder.encode(str["speakerName"])+"/"+Encoder.encode(str["speakerSlug"])},
													  },
													 "speakerName",
													 "speakerImg",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "speaker : "+str["speakerName"];}});
						}
					}
				} 
			}
		},

		getPerson : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_person.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { slug : parameters.uri} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONToken = {};
				if(_.size(dataXML) > 0 ){
					JSONToken.speakerName =  dataXML[0].name || "";
					JSONToken.speakerDesc =  dataXML[0].description || "";
					JSONToken.speakerHomepage =  dataXML[0].homepage || "";
					JSONToken.speakerImg =  dataXML[0].image || "";
					JSONToken.speakerTwitter =  dataXML[0].twitter || "";
					JSONToken.speakerId =  dataXML[0].id || "";
					JSONToken.speakerSlug =  dataXML[0].slug || "";
				}
				console.log(JSONToken);
				//StorageManager.pushCommandToStorage(currentUri,"getSpeaker",JSONToken);
				return JSONToken;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							if(parameters.JSONdata.speakerImg){
								parameters.contentEl.append($('<img src="'+parameters.JSONdata.speakerImg+'"/>'));    
							}
							if(parameters.JSONdata.speakerName){
								$("[data-role = page]").find("#DataConf").html(parameters.JSONdata.speakerName);
							}
							if(parameters.JSONdata.speakerDesc){
								parameters.contentEl.append($('<h2>Description</h2>'));
								parameters.contentEl.append($('<p>'+parameters.JSONdata.speakerDesc+'</p>'));    
							}
							if(parameters.JSONdata.speakerHomepage){
								parameters.contentEl.append($('<h2>Homepage</h2>'));
								parameters.contentEl.append($('<a href='+parameters.JSONdata.speakerHomepage+'>'+parameters.JSONdata.speakerHomepage+'</a>'));    
							}
							if(parameters.JSONdata.speakerTwitter){
								parameters.contentEl.append($('<h2>Twitter</h2>'));
								parameters.contentEl.append($('<a href='+parameters.JSONdata.speakerTwitter+'>'+parameters.JSONdata.speakerTwitter+'</a>'));    
							}

						}
					}
				} 
			}
		},

		getEventBySpeakerName : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
		    	// debugger;
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { role_type : 'Speaker',person_slug:parameters.name} ;
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
		    	// debugger;
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>Speaker at</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}
					}
				} 
			}
		},

		getEventByChairName : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
		    	// debugger;
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { role_type : 'Chair',person_slug:parameters.name} ;
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
		    	// debugger;
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
				console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>Chair at</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}
					}
				} 
			}
		},

		getAllTheme : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_theme.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { } ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.themename =  this.name || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							parameters.contentEl.append('<h2>Themes</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#theme/',
													  hrefCllbck:function(str){return Encoder.encode(str["themename"])},
													  },
													 "themename",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["themename"];}});
						}

					}
				} 
			}
		},

		getEventbyTheme : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { theme_name : parameters.name } ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>'+parameters.name+'</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}

					}
				} 
			}
		},

		getAllCategories : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_category.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { } ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.categoryname =  this.name || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							parameters.contentEl.append('<h2>Categories</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#category/',
													  hrefCllbck:function(str){return Encoder.encode(str["categoryname"])},
													  },
													 "categoryname",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["categoryname"];}});
						}
					}
				} 
			}
		},

		getEventbyCategory : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { category_name : parameters.name } ;
		      return ajaxData; 
		    },
		    
		    
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>'+parameters.name+'</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}

					}
				} 
			}
		},

		getConferenceWorkshop : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
		    
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { category_id : 4};
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){

							parameters.contentEl.append('<h2>Workshops</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}
					}
				} 
			}
	    },
		
		/** Command used to get the track events of a given conference **/ 
	    getConferenceTalk : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
		    
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { category_id : 7} ;
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceTutorial",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
			
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>Talks</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}
					}
				} 
			}
	    },
		
		/** Command used to get the track events of a given conference **/ 
	    getConferencePanel : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
		    
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { category_id : 2} ;
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferencePanel",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
			
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>Panels</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}
					}
				} 
			}
	    },
		
		/** Command used to get the SpecialEvent  of a given conference **/ 
	    getConferenceSpecialEvent : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
		    
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { category_id : 8} ;
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceWorkshop",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
			
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>Special events</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}
					}
				} 
			}
	    },
	    
		/** Command used to get the panel events of a given conference **/ 
	    getConferenceEvent : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?", 
		    getQuery : function(parameters){	
		    
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { category_id : 3} ;
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferencePanel",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
			
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>Browse conference panels</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}
					}
				} 
			}
	    },
	    /** Command used to get and display the name, the start and end time and location of a given event  **/ 
	    getEvent : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?", 
		    getQuery : function(parameters){	
		    
			    var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { "xproperty_namespace" : "event_uri","xproperty_value" : parameters.uri} ; 
		      return ajaxData; 
			      
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
			 	var JSONfile = {}; 
			    if(_.size(dataXML) > 0 ){
				 	dataXML=dataXML[0];
				 
				  	console.log(dataXML);
					JSONfile.eventLabel = (dataXML.name?dataXML.name:"");
					JSONfile.eventDescription =  (dataXML.description?dataXML.description:"");
					JSONfile.eventAbstract =  (dataXML.comment?dataXML.comment:""); 
					JSONfile.eventHomepage =  (dataXML.url?dataXML.url:""); 
					JSONfile.eventStart = (dataXML.start_at!= '1980-01-01 00:00'?dataXML.start_at:"");
					JSONfile.eventEnd = (dataXML.end_at!= '1980-01-01 00:00'?dataXML.end_at:"");
					JSONfile.eventLocationName =  (dataXML.location.name?dataXML.location.name:"") ;
					JSONfile.eventThemes =  (dataXML.themes?dataXML.themes:"") ;
				}
				// StorageManager.pushCommandToStorage(currentUri,"getEvent",JSONfile);
				return JSONfile;
				
			}, 

			ViewCallBack : function(parameters){
				var JSONdata = parameters.JSONdata;
				var conferenceUri = parameters.conferenceUri;

				if(parameters.JSONdata != null){
					
					var eventInfo = parameters.JSONdata;
						
					if(_.size(eventInfo) > 0 ){
						if(parameters.mode == "text"){
									  
							var eventLabel  = eventInfo.eventLabel;				
							var eventLocation  = eventInfo.eventLocation;
							var eventHomepage  = eventInfo.eventHomepage;
							var eventDescription  = eventInfo.eventDescription;
							var eventAbstract  = eventInfo.eventAbstract;							
							var locationName  = eventInfo.eventLocationName;	
							var eventStart  = eventInfo.eventStart;	
							var eventEnd  = eventInfo.eventEnd;
							var eventThemes  = eventInfo.eventThemes;	
							var eventStartICS  = moment(eventInfo.eventStart,"YYY-MM-DD HH:mm:ss").format("YYYYMMDDTHHmmss");	
							var eventEndICS  = moment(eventInfo.eventEnd ,"YYY-MM-DD HH:mm:ss").format("YYYYMMDDTHHmmss");	

							var icsEvent="BEGIN:VCALENDAR\n"+
									"VERSION:2.0\n"+
									'PRODID: //'+parameters.conferenceUri+'//ES//EN\n'+
									"BEGIN:VTIMEZONE\n"+
									"TZID:Europe/Paris\n"+
									"BEGIN:DAYLIGHT\n"+
									"TZOFFSETFROM:+0100\n"+
									"RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU\n"+
									"DTSTART:19810329T020000\n"+
									"TZNAME:GMT+02:00\n"+
									"TZOFFSETTO:+0200\n"+
									"END:DAYLIGHT\n"+
									"BEGIN:STANDARD\n"+
									"TZOFFSETFROM:+0200\n"+
									"RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU\n"+
									"DTSTART:19961027T030000\n"+
									"TZNAME:GMT+01:00\n"+
									"TZOFFSETTO:+0100\n"+
									"END:STANDARD\n"+
									"END:VTIMEZONE\n"+
									"BEGIN:VEVENT\n"+
									"CATEGORIES:"+eventLabel+"\n"+
									"DTSTART;TZID=Europe/Paris:"+eventStartICS+"\n"+
									"DTEND;TZID=Europe/Paris:"+eventEndICS+"\n"+
									"SUMMARY:"+eventLabel+"\n"+
									"DESCRIPTION:"+eventAbstract+"\n"+
									"LOCATION:"+locationName+"\n"+
									"END:VEVENT\n"+
									"END:VCALENDAR";
							var isDefined = false;	
							if(eventDescription != ""){ 
								parameters.contentEl.append($('<h2>Description</h2>')); 
								parameters.contentEl.append($('<p>'+eventDescription+'</p>'));   
							}
							if(eventAbstract != ""){ 
								parameters.contentEl.append($('<h2>Abstract</h2>')); 
								parameters.contentEl.append($('<p>'+eventAbstract+'</p>'));   
							}
							if(eventHomepage != ""){ 
								parameters.contentEl.append($('<h2>Homepage</h2>')); 
								parameters.contentEl.append($('<a href="'+eventHomepage+'">'+eventHomepage+'</p>'));   
							}
							if(eventStart != ""){ 
								parameters.contentEl.append($('<h2>Starts at :  <span class="inline">'+moment(eventStart).format('MMMM Do YYYY, h:mm:ss a')+'</span></h2>'));
								isDefined = true;
							}
							if(eventEnd != ""){
								parameters.contentEl.append($('<h2>Ends at : <span class="inline">'+moment(eventEnd).format('MMMM Do YYYY, h:mm:ss a')+'</span></h2>'));  
							} 
							if(locationName != ""){ 
								parameters.contentEl.append($('<h2>Location : <a href="#schedule/'+Encoder.encode(locationName)+'" data-role="button" data-icon="search" data-inline="true">'+locationName+'</a></h2>'));
							}
							if(eventLabel !=""){ 
								$("[data-role = page]").find("#DataConf").html(eventLabel);
							}
							
							if(isDefined){
								var icsButton = $('<button data-role="button" data-inline="true" data-icon="gear" data-iconpos="left">Add to calendar</button>');
								icsButton.click(function(){
									var blob = new Blob([icsEvent], {type: "text/calendar;charset=utf-8"});
									saveAs(blob, "icsEvent.ics");
								});
								parameters.contentEl.append(icsButton);
							}

							if(eventThemes != ""){
								parameters.contentEl.append('<h2>Themes</h2>'); 
								$.each(eventThemes, function(i,theme){
									ViewAdapterText.appendButton(parameters.contentEl,'#theme/'+Encoder.encode(theme.name),theme.name,{tiny : 'true'});
								});
							}
						}else{
							
							 
							var eventLabel  = eventInfo.eventLabel;				
							var eventLocation  = eventInfo.eventLocation;
							var eventDescription  = eventInfo.eventDescription;
							var eventAbstract  = eventInfo.eventAbstract;							
							var locationName  = eventInfo.eventLocationName;	
							var eventStart  = eventInfo.eventStart;	
							var eventEnd  = eventInfo.eventEnd;
						
							if(eventDescription != ""){ 
								ViewAdapterGraph.addLeaf("Description :"+eventDescription);
							}
							if(eventAbstract != ""){ 
								ViewAdapterGraph.addLeaf("Abstract :"+eventAbstract);
							}
							if(eventStart != ""){ 
								alert("pop");
								ViewAdapterGraph.addLeaf("Starts at :"+moment(eventStart).format('MMMM Do YYYY, h:mm:ss a'));
							}
							if(eventEnd != ""){
								ViewAdapterGraph.addLeaf("Ends at :"+moment(eventEnd).format('MMMM Do YYYY, h:mm:ss a'));
							} 
							if(locationName != ""){ 
								ViewAdapterGraph.addLeaf("Location :"+locationName);
							}
							if(eventLabel !=""){ 
								ViewAdapterGraph.addLeaf("Label :"+eventLabel);
								$("[data-role = page]").find("#DataConf").html(eventLabel);
							}
							
						}
					}
					
				}
			}
	    },
	    

		/** Command used to get all session's sub event of a given event  **/
	    getSessionEvent : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?", 
		    getQuery : function(parameters){	
		    
			    var conferenceUri = parameters.conferenceUri; 
		      var ajaxData = { 'category_id': 5 } ; 
		      return ajaxData; 
			      
		    }, 
		    
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getTrackSubEvent",JSONfile);
				return JSONfile;
				
			},
			
			 
			
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						
						if(parameters.mode == "text"){
							parameters.contentEl.append($('<h2>Sessions</h2>')); 
							$.each(parameters.JSONdata, function(i,track){
								ViewAdapterText.appendButton(parameters.contentEl,'#event/'+Encoder.encode(track.eventUri),track.eventLabel);
							});
						}
					}
				}
			}
	                                         
	    },

	    getSpeakersFromEventUri : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_person.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { role_type : 'Speaker',event_uri:parameters.uri} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.speakerName =  this.name || "";
					JSONToken.speakerDesc =  this.description || "";
					JSONToken.speakerHomepage =  this.homepage || "";
					JSONToken.speakerImg =  this.image || "";
					JSONToken.speakerTwitter =  this.twitter || "";
					JSONToken.speakerId =  this.id || "";
					JSONToken.speakerSlug =  this.slug || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							parameters.contentEl.append($('<h2>Speaker(s)</h2>')); 
							$.each(parameters.JSONdata, function(i,speaker){
								ViewAdapterText.appendButton(parameters.contentEl,'#person/'+Encoder.encode(speaker.speakerSlug),speaker.speakerName,{tiny : 'true'});
							});
						}
					}
				} 
			}
		},

	    getChairsFromEventUri : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_person.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { role_type : 'Chair',event_uri:parameters.uri} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.speakerName =  this.name || "";
					JSONToken.speakerDesc =  this.description || "";
					JSONToken.speakerHomepage =  this.homepage || "";
					JSONToken.speakerImg =  this.image || "";
					JSONToken.speakerTwitter =  this.twitter || "";
					JSONToken.speakerId =  this.id || "";
					JSONToken.speakerSlug =  this.slug || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							parameters.contentEl.append($('<h2>Chair(s)</h2>')); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#person/',
													  hrefCllbck:function(str){return Encoder.encode(str["speakerSlug"])},
													  },
													 "speakerName",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "speaker : "+str["speakerName"];}});
						}
					}
				} 
			}
		},
	  
		/** Command used to get and display the documents linked to an event **/ 
	    getEventPublications : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?", 
		    getQuery : function(parameters){	
		    
			    var conferenceUri = parameters.conferenceUri; 
		      var ajaxData = { "parent_xproperty_value" : parameters.uri} ; 
		      return ajaxData; 
			      
		    },  
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='publication_uri'){
					      if (/[a-zA-Z]/.test(this.xproperties[j].xKey)) {
	                  JSONToken.publiUri =  this.xproperties[j].xValue; 
	                  JSONToken.publiTitle =  this.xproperties[j].xKey; 
					          JSONfile[i] = JSONToken;
	              }
	          }
					}
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getEventPublications",JSONfile);
				return JSONfile;
				
			}, 
			ViewCallBack : function(parameters){

				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							parameters.contentEl.append($('<h2>Publications</h2>')); 
							ViewAdapterText.appendList(parameters.JSONdata,
												 {baseHref:'#publication/',
												  hrefCllbck:function(str){return Encoder.encode(str["publiUri"])},
												  },
												 "publiTitle",
												 parameters.contentEl
												 );
						}
					}
				} 
			}
	    },
		
		
		/** Command used to get the session events of a given publication **/ 
	    getEventRelatedPublication : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?", 
		    getQuery : function(parameters){	
		    
			    var conferenceUri = parameters.conferenceUri; 
		      var ajaxData = { "child_xproperty_value" : parameters.uri} ; 
		      return ajaxData; 
			      
		    },   
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri'){
					                    JSONToken.eventUri =  this.xproperties[j].xValue;  
	          }
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getTrackSubEvent",JSONfile);
				return JSONfile;
				
			},   
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
						
							parameters.contentEl.append($('<h2>Related Sessions :</h2>')); 
							ViewAdapterText.appendList(parameters.JSONdata,
												 {baseHref:'#event/',
												  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
												  },
												 "eventLabel",
												 parameters.contentEl,
												 {type:"Node",labelCllbck:function(str){return "presentation : "+str["eventLabel"];}});

						}
					}
				} 
			}
	    },
		
		/** Command used to get the session events of a given Event  **/ 
	    getSessionSubEvent : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "schedule_event.jsonp?", 
		    getQuery : function(parameters){	
		      
			    var conferenceUri = parameters.conferenceUri; 
		      var ajaxData = { "parent_xproperty_value" : parameters.uri,category_id:4} ; 
		      return ajaxData; 
			      
		    },   
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri'){
					                    JSONToken.eventUri =  this.xproperties[j].xValue;  
					  }
					}
					JSONfile[i] = JSONToken;
				});
				
				//StorageManager.pushCommandToStorage(currentUri,"getTrackSubEvent",JSONfile);
				return JSONfile;
				
			},   
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
						
							parameters.contentEl.append($('<h2>Related Sessions :</h2>')); 
							ViewAdapterText.appendList(parameters.JSONdata,
												 {baseHref:'#event/',
												  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
												  },
												 "eventLabel",
												 parameters.contentEl,
												 {type:"Node",labelCllbck:function(str){return "presentation : "+str["eventLabel"];}});

						}
					}
				} 
			}
	    },
		
		getTrackSubEvent : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { parent_xproperty_value : parameters.uri,category_name : "TrackEvent" } ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$(dataXML).each(function(i){
					var JSONToken = {};
					JSONToken.eventLabel =  this.name
					for(var j=0;j<this.xproperties.length;j++){
					  if(this.xproperties[j].xNamespace=='event_uri')JSONToken.eventUri =  this.xproperties[j].xValue;
					}
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							
							parameters.contentEl.append('<h2>Sub events</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["eventUri"])},
													  },
													 "eventLabel",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Track : "+str["eventLabel"];}});
						}

					}
				} 
			}
		},
 

	    /** Command used Schedule of the conf **/
		getConferenceSchedule : {
	 
			dataType : "JSONP", 
			method : "GET",
			serviceUri : "schedule_event.jsonp?",  
			getQuery : function(parameters) {  
				//Building sparql query with prefix
				var query = ""; 
				//Encapsulating query in json object to return it
				if(parameters.uri != "null"){
					var  ajaxData = {"location_name" : parameters.uri};
				}else{
					var  ajaxData = {"all" : parameters.uri};
				}
				return ajaxData;
			},
			//Declaring the callback function to use when sending the command
			ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				
				if(dataXML.length != 0){
					var JSONfile = {};
					$(dataXML).each(function(i,event){  
						console.log(event);
						
						//////////////////////////////
						/// look for special event ///
						//////////////////////////////
					  	var currentEvent = {};
						currentEvent.eventType = (event.categories[0]?event.categories[0].name:"");
						 
						if(currentEvent.eventType!="Event" && currentEvent.eventType!="ConferenceEvent"){ 
						   
					    //retrieve current Start Slot
						  var currentStartSlot =  event.start_at; 	
						  if(!JSONfile[currentStartSlot]) JSONfile[currentStartSlot] = {}; 
						  currentStartSlot = JSONfile[currentStartSlot];
						  
					    //retrieve current End Slot
						  var currentEndSlot =  event.end_at;
						  if(!currentStartSlot[currentEndSlot]) currentStartSlot[currentEndSlot] = {bigEvents:{},events:[]}; 
						  currentEndSlot = currentStartSlot[currentEndSlot];
						  
						
						  //retrieve current eventType slot
						  if(!currentEndSlot.bigEvents[currentEvent.eventType]) currentEndSlot.bigEvents[currentEvent.eventType] = [];  
						  
						  
						//then push to the correct start/end slot 
						if(event.xproperties[0])
						  currentEvent.eventUri = event.xproperties[0].xValue; 
						  currentEvent.eventLabel =  event.name;
						  currentEvent.eventDesc =  $(this).find("[name = eventDesc]").text();
						  currentEvent.locationLabel =  event.location.name;
				          currentEndSlot.bigEvents[currentEvent.eventType].push(currentEvent);
						  
						}else { 
						
						  //currentEndSlot.events.push(currentEvent);
						  
						}
						
					});
					//StorageManager.pushCommandToStorage(currentUri,"getConferenceSchedule",JSONfile);
					return JSONfile;
				}
				return null;
			},
			
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
					  if(parameters.name!="null" && parameters.name!="")$("[data-role = page]").find("#DataConf").html(parameters.name);
					  var content=$("<div data-role='collapsible-set' data-inset='false'></div>");
					  var currentDay,currentUl ;
					  for (var startAt in parameters.JSONdata) {
					      
					      //if the day has changed
					      if(currentDay != moment(startAt).format('MMMM Do YYYY')){
					          currentCollabsible = $('<div data-role="collapsible" data-theme="d" ><h2>'+moment(startAt).format('MMMM Do YYYY')+'</h2></div>');
					          currentUl = $('<ul data-role="listview" data-inset="true" ></ul>');
					          //content.append(currentUl);
					          content.append(currentCollabsible); 
					          currentCollabsible.append(currentUl);
					      }
					      currentDay = moment(startAt).format('MMMM Do YYYY');
					      
					      var startTime = moment(startAt).format('h:mm a');
					      
	              currentUl.append("<li  data-theme='a' data-role='list-divider' >\
	                                    starts at "+startTime+"\
	                                </li>");
	                                
				        for (var endAt in parameters.JSONdata[startAt]) {
				         
				            var lasts  =  moment(startAt).from(moment(endAt),true); 
				            
				            var bigEvents = parameters.JSONdata[startAt][endAt].bigEvents;
			              if(_.size(bigEvents)>0){
			                for (var eventType in bigEvents) {
			                    
			                    for (var i=0 ;i<bigEvents[eventType].length;i++) {
		            
		                          var LocationHtml= ''; 
		                          
		                          if(parameters.name && parameters.name!="null" && parameters.name!=""){
		                              LocationHtml = '<p>'+parameters.name+'</p>';
	                            }else{
		                              LocationHtml = '<p>'+bigEvents[eventType][i].locationLabel+'</p>';
		                              LocationHtml += '<p><a href="#schedule/'+Encoder.encode(bigEvents[eventType][i].locationLabel)+'" data-role="button" data-icon="search" data-inline="true">'+bigEvents[eventType][i].locationLabel+'</a></p>';
	                            }
	                            currentUl.append('<li data-inset="true"  ><a href="#event/'+Encoder.encode(bigEvents[eventType][i].eventUri)+'">\
						                                        <h3>'+bigEvents[eventType][i].eventLabel+'</h3>\
						                                        <p>'+bigEvents[eventType][i].eventType+'</p>\
																<p>last : <strong>'+lasts+'</strong></p>\
						                                        '+LocationHtml+'</a></li>'); 
			                    } 
			                }
			                 
			              }
				        } 
					  }
					  parameters.contentEl.append(content);
					}
				}
			}
	    },

	    	    /** Command used Schedule of the conf **/
		getWhatsNext : {
	 
			dataType : "JSONP", 
			method : "GET",
			serviceUri : "schedule_event.jsonp?",  
			getQuery : function(parameters) {  
				//Building sparql query with prefix
				var query = ""; 
				//Encapsulating query in json object to return it
				
					var  ajaxData = {"after" : new Date()};
				
				return ajaxData;
			},
			//Declaring the callback function to use when sending the command
			ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				
				if(dataXML.length != 0){
					var JSONfile = {};
					var seenLocation = [];
					$(dataXML).each(function(i,event){  
						console.log(event);
						
						//////////////////////////////
						/// look for special event ///
						//////////////////////////////
					  	var currentEvent = {};
						currentEvent.eventType = (event.categories[0]?event.categories[0].name:"");
						currentEvent.eventLocation = (event.location?event.location.name:"");
						 
						if(currentEvent.eventType!="Event" && currentEvent.eventType!="ConferenceEvent" && currentEvent.eventLocation !=""){ 
							   
						    //retrieve first event by location
							var currentLocation =  event.location.name; 	
							if(_.indexOf(seenLocation, currentLocation) == -1){
								seenLocation.push(currentLocation);
								JSONfile[i] = {};

								currentEvent.eventUri = event.xproperties[0].xValue || "";
								currentEvent.eventLabel =  event.name || "";
								currentEvent.eventStart=  event.start_at || "";
								currentEvent.eventEnd= event.end_at || "";
								
								JSONfile[i].location = currentLocation;
								JSONfile[i].event = currentEvent;
							}
						}
					});
					//StorageManager.pushCommandToStorage(currentUri,"getConferenceSchedule",JSONfile);
					return JSONfile;
				}
				return null;
			},
			
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						var content=$("<div data-role='collapsible-set' data-inset='false'></div>");
						var currentDay,currentUl ;
						$.each(parameters.JSONdata, function(i,location){  
							var lasts  =  moment(location.event.eventStart).from(moment(location.event.eventEnd),true); 
							var formatedStart = moment(location.event.eventStart).format('h:mm a') 
							currentCollabsible = $('<div data-role="collapsible" data-theme="d" ><h2>'+location.location+'</h2></div>');
							currentUl = $('<ul data-role="listview" data-inset="true" ></ul>');
							content.append(currentCollabsible); 
							currentCollabsible.append(currentUl);

							currentUl.append('<li data-inset="true"  ><a href="#event/'+Encoder.encode(location.event.eventUri)+'">\
							                <h3>'+location.event.eventLabel+'</h3>\
							                <p>'+location.event.eventType+'</p>\
							                <p>Start at : <strong>'+formatedStart+'</p>\
											<p>last : <strong>'+lasts+'</strong></p>\
							                </a></li>'); 
					  	})

					  	parameters.contentEl.append(content);
					}
				}
			}
	    }
	};
 	return swcEventCommandStore;
});
