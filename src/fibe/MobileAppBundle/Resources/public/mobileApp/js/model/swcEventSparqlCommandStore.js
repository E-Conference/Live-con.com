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
	var swcEventSparqlCommandStore = { 

		getAllTopics : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_topic.jsonp?",
		    getQuery : function(parameters){	
		      var ajaxData = {conference_id : parameters.conference.id} ;
		      return ajaxData;     
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON,function(i){  
					var JSONToken = {};
					JSONToken.id = this.id || null;
					JSONToken.name = this.name || null;
					JSONfile[i] = JSONToken;
				});
				console.log(JSONfile);
				//StorageManager.pushCommandToStorage(currentUri,"getAllTopics",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#topic/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["id"])},
													  },
													 "name",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "event : "+str["name"];}});
						}
					}
				} 
			}
		},

		getAllEvents : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
		      var ajaxData = {conference_id : parameters.conference.id} ;
		      return ajaxData;     
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON,function(i){  
					var JSONToken = {};
					JSONToken.id = this.id || null;
					JSONToken.name = this.name || null;
					JSONfile[i] = JSONToken;
				});
				console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getAllEvents",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["id"])},
													  },
													 "name",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "event : "+str["name"];}});
						}
					}
				} 
			}
		},


		getAllPersons : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_person.jsonp?",
		    getQuery : function(parameters){	
		      var ajaxData = {conference_id : parameters.conference.id} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.name =  this.name || "";
					JSONToken.description =  this.description || "";
					JSONToken.homepage =  this.homepage || "";
					JSONToken.image =  this.image || "";
					JSONToken.twitter =  this.twitter || "";
					JSONToken.id =  this.id || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getAllPersons",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendListImage(parameters.JSONdata,
													 {baseHref:'#person/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["id"])},
													  },
													 "name",
													 "image",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "person : "+str["id"];}});
						}
					}
				} 
			}
		},

		getAllPublications : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_paper.jsonp?",
		    getQuery : function(parameters){	
		      var ajaxData = {conference_id : parameters.conference.id} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.id =  this.id || "";
					JSONToken.title =  this.title || "";					
					JSONfile[i] = JSONToken;
				});
				console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getAllPublications",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#publication/',
													  hrefCllbck:function(str){return Encoder.encode(str["title"])+"/"+Encoder.encode(str["id"])},
													  },
													 "title",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "paper : "+str["id"];}});
						}
					}
				} 
			}
		},

		getAllOrganizations : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_organization.jsonp?",
		    getQuery : function(parameters){	
		      var ajaxData = {conference_id : parameters.conference.id} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.id =  this.id || "";
					JSONToken.name =  this.name || "";
					JSONToken.page =  this.page || "";
					JSONToken.country =  this.country || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getAllOrganizations",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendListImage(parameters.JSONdata,
													 {baseHref:'#organization/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["id"])},
													  },
													 "name",
													 "image",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "person : "+str["id"];}});
						}
					}
				} 
			}
		},



		getAllRoles : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_role.jsonp?",
		    getQuery : function(parameters){	
		      var ajaxData = {} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.id =  this.id || null;
					JSONToken.name =  this.name || null;
					JSONfile[i] = JSONToken;
				});
				console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getAllRoles",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#person-by-role/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+'/'+Encoder.encode(str["id"])},
													  },
													 "name",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Role : "+str["name"];}});
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
					JSONToken.name =  this.name || "";
					JSONToken.id =  this.id || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getAllCategories",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event-by-category/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+'/'+Encoder.encode(str["id"])},
													  },
													 "name",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Categories : "+str["name"];}});
						}
					}
				} 
			}
		},

		getTopic : {
			dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_topic.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { id : parameters.uri} ;
		      return ajaxData; 
		    }, 
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONToken = {};
				if(_.size(dataXML) > 0 ){
					JSONToken.name =  dataXML[0].name || null;
					JSONToken.id =  dataXML[0].id || null;

					JSONToken.publications = [];
					for(var j=0;j<dataXML[0].papers.length;j++){
					  	JSONToken.publications[j]=  dataXML[0].papers[j];
					}

					JSONToken.events = [];
					for(j=0;j<dataXML[0].events.length;j++){
					  	JSONToken.events[j] =  dataXML[0].events[j];
					}
				}
				console.log(JSONToken);
				//StorageManager.pushCommandToStorage(currentUri,"getTopic",JSONToken);
				return JSONToken;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							if(parameters.JSONdata.name){
								$("[data-role = page]").find("#DataConf").html(parameters.JSONdata.name);
							}
							if(_.size(parameters.JSONdata.publications) > 0 ){
								parameters.contentEl.append($('<h2>Related publication(s)</h2>'));
								$.each(parameters.JSONdata.publications, function(i,publication){
									ViewAdapterText.appendButton(parameters.contentEl,'#publication/'+Encoder.encode(publication.name)+'/'+Encoder.encode(publication.id), publication.name,{tiny : true});
								});
								
							}
							if(_.size(parameters.JSONdata.events) > 0 ){
								parameters.contentEl.append($('<h2>Related event(s)</h2>'));
								$.each(parameters.JSONdata.events, function(i,ev){
									ViewAdapterText.appendButton(parameters.contentEl,'#event/'+Encoder.encode(ev.name)+'/'+Encoder.encode(ev.id), ev.name, {tiny : true});
								});
							}
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
		      var ajaxData = { id : parameters.uri} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONToken = {};
				if(_.size(dataXML) > 0 ){
					JSONToken.name =  dataXML[0].name || "";
					JSONToken.description =  dataXML[0].description || "";
					JSONToken.homepage =  dataXML[0].homepage || "";
					JSONToken.image =  dataXML[0].image || "";
					JSONToken.twitter =  dataXML[0].twitter || "";
					JSONToken.id =  dataXML[0].id || "";

					JSONToken.roles = {};
					var i = 0;
					for(var j=0;j<dataXML[0].roles.length;j++){
						var currentRole= dataXML[0].roles[j];
						if(!JSONToken.roles[currentRole.type]){
							JSONToken.roles[currentRole.type] = [];
						}
						JSONToken.roles[currentRole.type].push(currentRole["event"]);
						
					}

					JSONToken.organizations = [];
					for(var j=0;j<dataXML[0].organizations.length;j++){
					  	JSONToken.organizations[j]=  dataXML[0].organizations[j];
					}

				}
				console.log(JSONToken);
				StorageManager.pushCommandToStorage(currentUri,"getPerson",JSONToken);
				return JSONToken;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							if(parameters.JSONdata.image){
								parameters.contentEl.append($('<img src="'+parameters.JSONdata.image+'"/>'));    
							}
							if(parameters.JSONdata.name){
								$("[data-role = page]").find("#DataConf").html(parameters.JSONdata.name);
							}
							if(parameters.JSONdata.description){
								parameters.contentEl.append($('<h2>Description</h2>'));
								parameters.contentEl.append($('<p>'+parameters.JSONdata.description+'</p>'));    
							}
							if(parameters.JSONdata.homepage){
								parameters.contentEl.append($('<h2>Homepage</h2>'));
								parameters.contentEl.append($('<a href='+parameters.JSONdata.homepage+'>'+parameters.JSONdata.homepage+'</a>'));    
							}
							if(parameters.JSONdata.twitter){
								parameters.contentEl.append($('<h2>Twitter</h2>'));
								parameters.contentEl.append($('<a href='+parameters.JSONdata.twitter+'>'+parameters.JSONdata.twitter+'</a>'));    
							}
							if(parameters.JSONdata.roles) {
								for(var roleType in parameters.JSONdata.roles){
									parameters.JSONdata.roles[roleType];
									parameters.contentEl.append($('<h2>'+roleType+' at </h2>'));
									ViewAdapterText.appendList(parameters.JSONdata.roles[roleType],
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["id"])},
													  },
													 "name",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "person : "+str["id"];}});
								};
							
								parameters.contentEl.append($('<a href='+parameters.JSONdata.twitter+'>'+parameters.JSONdata.twitter+'</a>'));    
							}

							if(_.size(parameters.JSONdata.organizations) > 0 ){
								parameters.contentEl.append($('<h2>Organizations</h2>'));
								$.each(parameters.JSONdata.organizations, function(i,organization){
									ViewAdapterText.appendButton(parameters.contentEl,'#organization/'+Encoder.encode(organization.name)+'/'+Encoder.encode(organization.id), organization.name,{tiny : true});
								});
							}

						}
					}
				} 
			}
		},


		getOrganization : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_organization.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { id : parameters.uri} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONToken = {};
				if(_.size(dataXML) > 0 ){
					JSONToken.name =  dataXML[0].name || "";
					JSONToken.page =  dataXML[0].page || "";
					JSONToken.country =  dataXML[0].country || "";
					JSONToken.id =  dataXML[0].id || "";

					JSONToken.members = [];
					for(var j=0;j<dataXML[0].members.length;j++){
					  	JSONToken.members[j]=  dataXML[0].members[j];
					}

				}
				console.log(JSONToken);
				StorageManager.pushCommandToStorage(currentUri,"getOrganization",JSONToken);
				return JSONToken;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							if(parameters.JSONdata.name){
								$("[data-role = page]").find("#DataConf").html(parameters.JSONdata.name);
							}
							if(parameters.JSONdata.page){
								parameters.contentEl.append($('<h2>Page</h2>'));
								parameters.contentEl.append($('<a href='+parameters.JSONdata.page+'>'+parameters.JSONdata.page+'</a>')); 
								
							}
							if(parameters.JSONdata.country){
								parameters.contentEl.append($('<h2>Country</h2>'));
								parameters.contentEl.append($('<p>'+parameters.JSONdata.country+'</p>'));      
							}

							if(_.size(parameters.JSONdata.members) > 0 ){
								parameters.contentEl.append($('<h2>Members</h2>'));
								$.each(parameters.JSONdata.members, function(i,member){
									ViewAdapterText.appendButton(parameters.contentEl,'#person/'+Encoder.encode(member.name)+'/'+Encoder.encode(member.id), member.name,{tiny : true});
								});
								
							}
						

						}
					}
				} 
			}
		},

		getPersonByRole: {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_person.jsonp?",
		    getQuery : function(parameters){	
		      var ajaxData = {conference_id : parameters.conference.id, role_type_id: parameters.uri} ;
		      return ajaxData; 
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataXML,function(i){  
					var JSONToken = {};
					JSONToken.name =  this.name || "";
					JSONToken.description =  this.description || "";
					JSONToken.homepage =  this.homepage || "";
					JSONToken.image =  this.image || "";
					JSONToken.twitter =  this.twitter || "";
					JSONToken.id =  this.id || "";
					JSONfile[i] = JSONToken;
				});
					console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getPersonByRole",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendListImage(parameters.JSONdata,
													 {baseHref:'#person/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["id"])},
													  },
													 "name",
													 "image",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "person : "+str["id"];}});
						}
					}
				} 
			}
		},


	

		getPublicationsByAuthorId : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_paper.jsonp?",
		    getQuery : function(parameters){	
		    	// debugger;
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = {  conference_id: parameters.conference.id, author_id:parameters.uri} ;
		      return ajaxData; 
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
		    	// debugger;
				var JSONfile = {};
				$(dataXML).each(function(i){  
					var JSONToken = {};
					JSONToken.title =  this.title;
					JSONToken.id =  this.id;

					JSONfile[i] = JSONToken;
				});

				console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getPublicationsByAuthorId",JSONfile);
				return JSONfile;
				
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
						
							parameters.contentEl.append('<h2>'+parameters.conference.acronym +' publications</h2>'); 
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#publication/',
													  hrefCllbck:function(str){return Encoder.encode(str["title"])+'/'+Encoder.encode(str["id"])},
													  },
													 "title",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "publication : "+str["id"];}});
						}
					}
				} 
			}
		},

		getPublication : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_paper.jsonp?",
		    getQuery : function(parameters){	
			  var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { id : parameters.uri} ;
		      return ajaxData; 
		    }, 
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
				var JSONToken = {};
				if(_.size(dataXML) > 0 ){
					JSONToken.title =  dataXML[0].title || null;
					JSONToken.abstract =  dataXML[0].abstract || null;
					JSONToken.publishDate =  dataXML[0].publishDate || null;
					JSONToken.url =  dataXML[0].url || null;
					JSONToken.publisher =  dataXML[0].publisher || null;

					JSONToken.keywords = [];
					for(var j=0;j<dataXML[0].topics.length;j++){
					  	JSONToken.keywords[j]=  dataXML[0].topics[j];
					}

					JSONToken.authors = [];
					for(j=0;j<dataXML[0].authors.length;j++){
					  	JSONToken.authors[j] =  dataXML[0].authors[j];
					}
				}
				console.log(JSONToken);
				StorageManager.pushCommandToStorage(currentUri,"getPublication",JSONToken);
				return JSONToken;
			},
				
			ViewCallBack : function(parameters){
				//Reasoner.getMoreSpecificKeywords();
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							if(parameters.JSONdata.title){
								$("[data-role = page]").find("#DataConf").html(parameters.JSONdata.title);
							}
							if(parameters.JSONdata.abstract){
								parameters.contentEl.append($('<h2>Abstract</h2>'));
								parameters.contentEl.append($('<p>'+parameters.JSONdata.abstract+'</p>'));
							}
							if(parameters.JSONdata.publishDate){
								parameters.contentEl.append($('<h2>Published date</h2>'));
								parameters.contentEl.append($('<p>'+parameters.JSONdata.publishDate+'</p>'));    
							}
							if(parameters.JSONdata.url){
								parameters.contentEl.append($('<h2>Url</h2>'));
								parameters.contentEl.append($('<a href='+parameters.JSONdata.url+'>'+parameters.JSONdata.url+'</a>'));    
							}
							if(parameters.JSONdata.publisher){
								parameters.contentEl.append($('<h2>Published by</h2>'));
								parameters.contentEl.append($('<a href='+parameters.JSONdata.publisher+'>'+parameters.JSONdata.publisher+'</a>'));    
							}

							if(_.size(parameters.JSONdata.authors) > 0 ){
								parameters.contentEl.append($('<h2>Authors</h2>'));
								$.each(parameters.JSONdata.authors, function(i,author){
									ViewAdapterText.appendButton(parameters.contentEl,'#person/'+Encoder.encode(author.name)+'/'+Encoder.encode(author.id), author.name,{tiny : true});
								});
								
							}
							if(_.size(parameters.JSONdata.keywords) > 0 ){
								parameters.contentEl.append($('<h2>Keywords</h2>'));
								$.each(parameters.JSONdata.keywords, function(i,keyword){
									ViewAdapterText.appendButton(parameters.contentEl,'#topic/'+Encoder.encode(keyword.name)+'/'+Encoder.encode(keyword.id),keyword.name, {tiny : true});
								});
							}
						}
					}
				} 
			}
		},


		getAllTheme : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_topic.jsonp?",
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
				StorageManager.pushCommandToStorage(currentUri,"getEventbyTheme",JSONfile);
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

	

		getEventByCategory : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "schedule_event.jsonp?",
		    getQuery : function(parameters){	
		      var ajaxData = { category_id : parameters.uri } ;
		      return ajaxData; 
		    },
		    
		    
		     ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON,function(i){  
					var JSONToken = {};
					JSONToken.id = this.id || null;
					JSONToken.name = this.name || null;
					JSONfile[i] = JSONToken;
				});
				console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getEventByCategory",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["id"])},
													  },
													 "name",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "event : "+str["name"];}});
						}
					}
				} 
			}
		},


		/** Command used to get the panel events of a given conference **/ 
	    getConferenceEvent : {
		    dataType : "CORS",
		    method : "GET",
		    serviceUri : "", 
		    getQuery : function(parameters){	
		    
	    	   var prefix =	'' ;

						
			    var query = 	'SELECT DISTINCT ?eventLabel ?eventDescription ?eventAbstract ?eventHomepage ?locationName ?eventStart ?eventEnd ?eventStartCal ?eventEndCal WHERE {                   ' +
							    '	OPTIONAL { <'+parameters.uri+'> rdfs:label ?eventLabel.} ' +
								'	OPTIONAL { <'+parameters.uri+'> ical:description  ?eventDescription.}' +
								'	OPTIONAL { <'+parameters.uri+'> swrc:abstract ?eventAbstract. } '+
								'	OPTIONAL { <'+parameters.uri+'> foaf:homepage ?eventHomepage. } '+
							    '	OPTIONAL { <'+parameters.uri+'> <http://www.w3.org/2002/12/cal/icaltzd#dtstart> ?eventStart.}' +
							    '	OPTIONAL { <'+parameters.uri+'> <http://www.w3.org/2002/12/cal/icaltzd#dtend> ?eventEnd.}' +
								'	OPTIONAL { <'+parameters.uri+'> ical:dtstart ?eventStartCal.} ' +
							    '	OPTIONAL { <'+parameters.uri+'> ical:dtend   ?eventEndCal.}' +
			 				    '}';
		 	    var  ajaxData = { query : prefix+query };
			 	return ajaxData;
			     
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){

		    	var JSONfile = {};
				$(dataXML).find("sparql > results > result").each(function(i){  
					
					JSONfile["eventLabel"] =  $(dataXML).find("[name = eventLabel]").text();
					JSONfile["eventLocation"] =  $(dataXML).find("[name = eventLocation]").text(); 
					JSONfile["eventDescription"] =  $(dataXML).find("[name = eventDescription]").text(); 
					JSONfile["eventAbstract"] =  $(dataXML).find("[name = eventAbstract]").text(); 
					JSONfile["eventHomepage"] =  $(dataXML).find("[name = eventHomepage]").text(); 
					JSONfile["eventLocationName"] =  $(dataXML).find("[name = locationName]").text();
					if($(dataXML).find("[name = eventStart]").text() != ""){
						JSONfile["eventStart"] =  $(dataXML).find("[name = eventStart]").text();
						JSONfile["eventEnd"] =  $(dataXML).find("[name = eventEnd]").text();
					}else{
						JSONfile["eventStart"] =  $(dataXML).find("[name = eventStartCal]").text();
						JSONfile["eventEnd"] =  $(dataXML).find("[name = eventEndCal]").text();
					}
				
				});
				StorageManager.pushCommandToStorage(currentUri,"getEvent",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				var JSONdata = parameters.JSONdata;
				if(parameters.JSONdata != null){
					var eventInfo = parameters.JSONdata;	
					if(_.size(eventInfo) > 0 ){
						if(JSONdata.eventDescription){ 
							parameters.contentEl.append($('<h2>Description</h2>')); 
							parameters.contentEl.append($('<p>'+JSONdata.eventDescription+'</p>'));   
						}
						if(JSONdata.eventAbstract){ 
							parameters.contentEl.append($('<h2>Abstract</h2>')); 
							parameters.contentEl.append($('<p>'+JSONdata.eventAbstract+'</p>'));   
						}
						if(JSONdata.eventUrl){ 
							parameters.contentEl.append($('<h2>Homepage</h2>')); 
							parameters.contentEl.append($('<a href="'+JSONdata.eventUrl+'">'+JSONdata.eventUrl+'</p>'));   
						}
						if(JSONdata.eventStart){ 
							parameters.contentEl.append($('<h2>Starts at :  <span class="inline">'+moment(JSONdata.eventStart).format('MMMM Do YYYY, h:mm:ss a')+'</span></h2>'));
							isDefined = true;
						}
						if(JSONdata.eventEnd){
							parameters.contentEl.append($('<h2>Ends at : <span class="inline">'+moment(JSONdata.eventEnd).format('MMMM Do YYYY, h:mm:ss a')+'</span></h2>'));  
						} 
						if(JSONdata.locationName){ 
							parameters.contentEl.append($('<h2>Location : <a href="#schedule/'+Encoder.encode(JSONdata.locationName)+'" data-role="button" data-icon="search" data-inline="true">'+JSONdata.locationName+'</a></h2>'));
						}
						if(JSONdata.eventLabel){ 
							$("[data-role = page]").find("#DataConf").html(JSONdata.eventLabel);
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
		      var ajaxData = { conference_id : parameters.conference.id, id : parameters.uri} ; 
		      return ajaxData; 
			      
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
			 	var JSONfile = {}; 
			    if(_.size(dataXML) > 0 ){
				 	dataXML=dataXML[0];
				  	console.log(dataXML);
					JSONfile.eventLabel = dataXML.name || null;
					JSONfile.eventId = dataXML.id || null;
					JSONfile.eventDescription =  dataXML.description || null;
					JSONfile.eventAbstract =  dataXML.comment || null;
					JSONfile.eventHomepage =  dataXML.url|| null;
					JSONfile.eventStart = (dataXML.start_at!= '1980-01-01 00:00'?dataXML.start_at:null);
					JSONfile.eventEnd = (dataXML.end_at!= '1980-01-01 00:00'?dataXML.end_at:null);
					JSONfile.eventLocationName =  dataXML.location.name  || null;
					JSONfile.eventThemes =  dataXML.themes ||null;
					JSONfile.eventChildren =  dataXML.children ||null;
					JSONfile.eventPapers =  dataXML.papers || null;

				}
				StorageManager.pushCommandToStorage(currentUri,"getEvent",JSONfile);
				return JSONfile;
				
			}, 

			ViewCallBack : function(parameters){
				var JSONdata = parameters.JSONdata;
				var conferenceUri = parameters.conferenceUri;

				if(parameters.JSONdata != null){
					
					var eventInfo = parameters.JSONdata;
						
					if(_.size(eventInfo) > 0 ){
						if(parameters.mode == "text"){
						
							if(eventInfo.eventDescription){ 
								parameters.contentEl.append($('<h2>Description</h2>')); 
								parameters.contentEl.append($('<p>'+eventInfo.eventDescription+'</p>'));   
							}
							if(eventInfo.eventAbstract){ 
								parameters.contentEl.append($('<h2>Abstract</h2>')); 
								parameters.contentEl.append($('<p>'+eventInfo.eventAbstract+'</p>'));   
							}
							if(eventInfo.eventHomepage){ 
								parameters.contentEl.append($('<h2>Homepage</h2>')); 
								parameters.contentEl.append($('<a href="'+eventInfo.eventHomepage+'">'+eventInfo.eventHomepage+'</p>'));   
							}
							if(eventInfo.eventStart){ 
								parameters.contentEl.append($('<h2>Starts at :  <span class="inline">'+moment(eventInfo.eventStart).format('MMMM Do YYYY, h:mm:ss a')+'</span></h2>'));
								isDefined = true;
							}
							if(eventInfo.eventEnd){
								parameters.contentEl.append($('<h2>Ends at : <span class="inline">'+moment(eventInfo.eventEnd).format('MMMM Do YYYY, h:mm:ss a')+'</span></h2>'));  
							} 
							if(eventInfo.locationName){ 
								parameters.contentEl.append($('<h2>Location : <a href="#schedule/'+Encoder.encode(eventInfo.locationName)+'" data-role="button" data-icon="search" data-inline="true">'+locationName+'</a></h2>'));
							}
							if(eventInfo.eventLabel){ 
								$("[data-role = page]").find("#DataConf").html(eventInfo.eventLabel);
							}

							if(eventInfo.eventThemes && eventInfo.eventThemes.length>0){
								parameters.contentEl.append('<h2>Themes</h2>'); 
								$.each(eventInfo.eventThemes, function(i,theme){
									ViewAdapterText.appendButton(parameters.contentEl,'#topic/'+Encoder.encode(theme.name)+"/"+Encoder.encode(theme.id),theme.name,{tiny : 'true'});
								});
							}

							if(eventInfo.eventChildren &&  eventInfo.eventChildren.length>0){
								parameters.contentEl.append('<h2>Sub events</h2>'); 
								$.each(eventInfo.eventChildren, function(i,theme){
									ViewAdapterText.appendButton(parameters.contentEl,'#event/'+Encoder.encode(theme.name)+"/"+Encoder.encode(theme.id),theme.name,{tiny : 'true'});
								});
							}
							if(eventInfo.eventPapers &&  eventInfo.eventPapers.length>0){
								parameters.contentEl.append('<h2>Related documents</h2>'); 
								$.each(eventInfo.eventPapers, function(i,paper){
									ViewAdapterText.appendButton(parameters.contentEl,'#publication/'+Encoder.encode(paper.title)+"/"+Encoder.encode(paper.id),paper.title,{tiny : 'true'});
								});
							}
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
					var  ajaxData = {conference_id : parameters.conference.id};
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
						  currentEvent.eventId =  event.id;
						  currentEvent.eventDesc =  $(this).find("[name = eventDesc]").text();
						  currentEvent.locationLabel =  event.location.name;
				          currentEndSlot.bigEvents[currentEvent.eventType].push(currentEvent);
						  
						}else { 
						
						  //currentEndSlot.events.push(currentEvent);
						  
						}
						
					});
					StorageManager.pushCommandToStorage(currentUri,"getConferenceSchedule",JSONfile);
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
					      
	              currentUl.append("<li data-role='list-divider' >\
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
			                             // LocationHtml = '<p>'+bigEvents[eventType][i].locationLabel+'</p>';
			                              LocationHtml += '<p><a href="#schedule/'+Encoder.encode(bigEvents[eventType][i].locationLabel)+'" data-role="button" data-icon="search" data-inline="true">'+bigEvents[eventType][i].locationLabel+'</a></p>';
		                            }

		                            var newLi = $('<li data-inset="true" ></li>');
		                            var newEventlink = $('<a href="#event/'+Encoder.encode(bigEvents[eventType][i].eventLabel)+'/'+Encoder.encode(bigEvents[eventType][i].eventId)+'">');
		                            var newLabel = $('<h3>'+bigEvents[eventType][i].eventLabel+'</h3>');
		                            var newCategory = $('<p>'+bigEvents[eventType][i].eventType+'</p>');
		                            var newLast = $('<p>last : <strong>'+lasts+'</strong></p>');

		                            newEventlink.append(newLabel);
		                            newEventlink.append(newCategory);
		                            newEventlink.append(newLast);
		                            newEventlink.append(LocationHtml);
		                            newLi.append(newEventlink);

		                            currentUl.append(newLi);
		                         }
		                        }
			              	}
				        } 
					  }
					  parameters.contentEl.append('<h2>Schedule</h2>'); 
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
								currentEvent.eventId =  event.id || "";
								currentEvent.eventStart=  event.start_at || "";
								currentEvent.eventEnd= event.end_at || "";
								
								JSONfile[i].location = currentLocation;
								JSONfile[i].event = currentEvent;
							}
						}
					});
					//StorageManager.pushCommandToStorage(currentUri,"getWhatsNext",JSONfile);
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

							currentUl.append('<li data-inset="true"  ><a href="#event/'+Encoder.encode(location.event.eventLabel)+'/'+Encoder.encode(location.event.eventId)+'">\
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
	    },

	    /************************** ICS   ********************************/

	    /** Command used to get and display the name, the start and end time and location of a given event  **/ 
	    getEventIcs : {
		    dataType : "text",
		    method : "GET",
		    serviceUri : "schedule_event.ics?", 
		    getQuery : function(parameters){	
		    
			   var conferenceUri = parameters.conferenceUri;
		      var ajaxData = { conference_id : parameters.conference.id, id : parameters.uri} ; 
		      return ajaxData; 
			      
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
			 	var JSONfile = {"ics": dataXML}; 
		   
				//StorageManager.pushCommandToStorage(currentUri,"getEvent",JSONfile);
				return JSONfile;
				
			}, 

			ViewCallBack : function(parameters){
				var JSONdata = parameters.JSONdata;

				var icsButton = $('<button data-role="button" data-inline="true"><i class="fa fa-download"></i>  Add to calendar</button>');
				icsButton.click(function(){
					var blob = new Blob([JSONdata.ics], {type: "text/calendar;charset=utf-8"});
					saveAs(blob, "icsEvent.ics");
				});
				$("#bonusPanel").append(icsButton);
				
			}
	    },


	      /** Command used to get and display the name, the start and end time and location of a given event  **/ 
	    getConferenceScheduleIcs : {
		    dataType : "text",
		    method : "GET",
		    serviceUri : "schedule_event.ics?", 
		    getQuery : function(parameters){	
		      var ajaxData = { id : parameters.conference.eventId } ; 
		      return ajaxData; 
			      
		    },
		    
		    ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
			 	var JSONfile = {"ics": dataXML}; 
				//StorageManager.pushCommandToStorage(currentUri,"getEvent",JSONfile);
				return JSONfile;
				
			}, 

			ViewCallBack : function(parameters){
				var JSONdata = parameters.JSONdata;
				var conferenceUri = parameters.conferenceUri;

				var icsButton = $('<button data-role="button" data-inline="true"><i class="fa fa-download"></i>  Add to calendar</button>');
				icsButton.click(function(){
					var blob = new Blob([JSONdata.ics], {type: "text/calendar;charset=utf-8"});
					saveAs(blob, "icsEvent.ics");
				});
				$("#bonusPanel").append(icsButton);
				
			}
	    }
	};
 	return swcEventSparqlCommandStore;
});
