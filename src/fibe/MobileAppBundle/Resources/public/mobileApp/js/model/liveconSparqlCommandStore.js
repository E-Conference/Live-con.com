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
	var liveconSparqlCommandStore = { 

		getAllTopics : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "",
		    getQuery : function(parameters){	
	    		var prefix =   'PREFIX dc: <http://purl.org/dc/elements/1.1/> PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> ';
					
				var query =	'SELECT DISTINCT ?topicLabel  ?topicUri WHERE  {\
							 {	<'+parameters.uri+'>    swc:isSuperEventOf    ?eventUri.\
							  	?eventUri dc:subject ?topicUri.\
							  	?topicUri rdfs:label ?topicLabel.\
							 } UNION { \
							 	<'+parameters.uri+'>    swc:hasRelatedDocument ?publiUri.\
							  	?publiUri dc:subject ?topicUri.\
							  	?topicUri rdfs:label ?topicLabel. }\
							 } ORDER BY ASC(?topicLabel) '; 
	
				var  ajaxData = { query : prefix + query, output : "json"};
		     
		      return ajaxData;     
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.uri = this.topicUri ? this.topicUri.value  : null;
					JSONToken.name =  this.topicLabel ? this.topicLabel.value : null;
					JSONfile[i] = JSONToken;
				});
				console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getAllTopics",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#topic/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["uri"])},
													  },
													 "name",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "event : "+str["name"];}});
						}
					}
				} 
			}
		},

		getAllLocations : {

		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "",
		    getQuery : function(parameters){	
	    		var prefix =   'PREFIX dc: <http://purl.org/dc/elements/1.1/> PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> ';
					
				var query =	'SELECT DISTINCT ?locationName  ?locationUri WHERE  {\
							 <'+parameters.uri+'>    swc:isSuperEventOf    ?eventUri.\
							  	?eventUri swc:hasLocation ?locationUri.\
							  	?locationUri rdfs:label ?locationName.\
							 } ORDER BY ASC(?locationName) '; 
	
				var  ajaxData = { query : prefix + query, output : "json"};
		     
		      return ajaxData;     
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.uri = this.locationUri ? this.locationUri.value  : null;
					JSONToken.name =  this.locationName ? this.locationName.value : null;
					JSONfile[i] = JSONToken;
				});
				console.log(JSONfile);
				StorageManager.pushCommandToStorage(currentUri,"getAllTopics",JSONfile);
				return JSONfile;
			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#schedule/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])},
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
		    serviceUri : "",
		    getQuery : function(parameters){	
		    	var prefix  =   'PREFIX rdf:    <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\
                    PREFIX swc:     <http://data.semanticweb.org/ns/swc/ontology#>\
                    PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#>\
                    PREFIX event:   <http://purl.org/NET/c4dm/event.owl#> \
                    PREFIX ical: <http://www.w3.org/2002/12/cal/ical#> ';
                  

                var query  =   "SELECT DISTINCT  ?eventUri ?eventSummary WHERE {\
                    	<"+parameters.conference.baseUri+">    swc:isSuperEventOf    ?eventUri.\
                       ?eventUri     ical:summary 	?eventSummary.\
                    }";
                    
				
				var  ajaxData = { query : prefix + query, output : "json" };
		     	return ajaxData;
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){

				var JSONfile = {};			
				$.each(dataJSON.results.bindings,function(i){  
						var JSONToken = {};
						JSONToken.uri = this.eventUri.value || null;
						JSONToken.name = this.eventSummary.value || null;
						JSONfile[i] = JSONToken;
				})
				
				StorageManager.pushCommandToStorage(currentUri,"getAllEvents",JSONfile);
				return JSONfile;


			},
				
			ViewCallBack : function(parameters){
				if(parameters.JSONdata != null){
					if(_.size(parameters.JSONdata) > 0 ){
						if(parameters.mode == "text"){
							ViewAdapterText.appendList(parameters.JSONdata,
													 {baseHref:'#event/',
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["uri"])},
													  },
													 "name",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "event : "+str["name"];}});
						}
					}
				} 
			}
		},

		getAllAuthors : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "",
		    getQuery : function(parameters){	

				var prefix =   'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> ';
				var query =   'SELECT DISTINCT ?authorName  ?authorUri ?authorImg  WHERE  { ' +
								'    <'+parameters.conference.baseUri+'> swc:hasRelatedDocument ?uriPubli.' + 
								'   ?authorUri foaf:made ?uriPubli.  ' +
								'   ?authorUri foaf:name ?authorName.' +
								' OPTIONAL { ?authorUri foaf:img ?authorImg.}' +
								'} ORDER BY ASC(?authorName) '; 
				var  ajaxData = { query : prefix + query, output : "json" };
				return ajaxData;
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.name =  this.authorName.value || "";
					JSONToken.uri =  this.authorUri.value || "";
					JSONToken.image =  this.authorImg ? this.authorImg.value : null;
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
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["uri"])},
													  },
													 "name",
													 "image",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "person : "+str["uri"];}});
						}
					}
				} 
			}
		},

		getAllPersons : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "",
		    getQuery : function(parameters){	

				var prefix =   'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> ';
				var query =   'SELECT DISTINCT ?personName  ?personUri ?personImg  WHERE  { ' +
								'  {  <'+parameters.conference.baseUri+'> swc:hasRelatedDocument ?publiUri.' + 
								'   ?personUri foaf:made ?publiUri.  ' +
								'   ?personUri foaf:name ?personName.' +
								' OPTIONAL { ?personUri foaf:img ?personImg.} '+
								' } UNION {  <'+parameters.conference.baseUri+'> swc:isSuperEventOf ?eventUri.' + 
								'   ?roleUri swc:isRoleAt ?eventUri.  ' +
								'   ?roleUri swc:heldBy ?personUri.		'+
								'	?personUri foaf:name ?personName.  '+
								'	OPTIONAL { ?personUri foaf:img ?personImg.} } '+
								'} ORDER BY ASC(?personName) '; 
				var  ajaxData = { query : prefix + query, output : "json" };
				return ajaxData;
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.name =  this.personName.value || "";
					JSONToken.uri =  this.personUri.value || "";
					JSONToken.image =  this.personImg ? this.personImg.value : null;
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
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["uri"])},
													  },
													 "name",
													 "image",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "person : "+str["uri"];}});
						}
					}
				} 
			}
		},

		getAllPublications : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "",
		    getQuery : function(parameters){	
		       var query = 'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> '+
						'PREFIX foaf: <http://xmlns.com/foaf/0.1/> '+
						'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> '+
	                    'PREFIX dc: <http://purl.org/dc/elements/1.1/> '+
						'SELECT DISTINCT ?publiTitle ?publiUri WHERE {'+
						'    <'+parameters.conference.baseUri+'> swc:hasRelatedDocument ?publiUri.' + 
	                    '	 ?publiUri dc:title ?publiTitle.' + 
						'} ORDER BY ASC(?publiTitle)'; 
	             
			var  ajaxData = { query : query, output : "json" };
			return ajaxData;
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.uri =  this.publiUri.value || "";
					JSONToken.title =  this.publiTitle.value || "";					
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
													  hrefCllbck:function(str){return Encoder.encode(str["title"])+"/"+Encoder.encode(str["uri"])},
													  },
													 "title",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "paper : "+str["uri"];}});
						}
					}
				} 
			}
		},

		getAllOrganizations : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "",
		    getQuery : function(parameters){	
		    	var prefix =   'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> ';
				var query =   'SELECT DISTINCT ?organizationName  ?organizationUri WHERE  { ' +
								'    <'+parameters.conference.baseUri+'> swc:hasRelatedDocument ?uriPubli.' + 
								'   ?authorUri foaf:made ?uriPubli.  ' +
								'   ?authorUri swc:memberOf ?organizationUri.' +
								'   ?organizationUri rdfs:label ?organizationName.' +
								'} ORDER BY ASC(?organizationName) '; 
				var  ajaxData = { query : prefix + query, output : "json" };
				return ajaxData;
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.uri =  this.organizationName.value || "";
					JSONToken.name =  this.name || "";
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
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["uri"])},
													  },
													 "name",
													 "image",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "person : "+str["uri"];}});
						}
					}
				} 
			}
		},



		getAllRoles : {
		    dataType : "JSONP",
		    method : "GET", 
		    serviceUri : "",
		    getQuery : function(parameters){	
		     	var prefix =  'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>';
				var query =   'SELECT DISTINCT ?roleName  ?roleUri WHERE  { ' +
								'    <'+parameters.conference.baseUri+'> swc:isSuperEventOf ?eventUri.' + 
								'    ?roleUri  swc:isRoleAt  ?eventUri.' +
								'    ?roleUri rdf:type ?roleName.' +
								'} ORDER BY ASC(?roleName)'; 
				var  ajaxData = { query : prefix + query, output : "json" };
				return ajaxData;
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.uri =  this.roleUri.value || null;
					JSONToken.name =  this.roleName.value || null;
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
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+'/'+Encoder.encode(str["uri"])},
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
		    serviceUri : "",
		    getQuery : function(parameters){	
			 	var prefix =  'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>';
				var query =   'SELECT DISTINCT ?categoryUri  ?categoryName WHERE  { ' +
								'    <'+parameters.conference.baseUri+'> swc:isSuperEventOf ?eventUri.' + 
								'    ?eventUri  rdf:type  ?categoryUri.' +
								'    ?roleUri rdf:type ?roleName.' +
								'} ORDER BY ASC(?roleName)'; 
				var  ajaxData = { query : prefix + query, output : "json" };
				return ajaxData;
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.name =  this.categoryName ? this.categoryName.value : "";
					JSONToken.uri =  this.categoryUri ? this.categoryUri.value : "";
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
													  hrefCllbck:function(str){return Encoder.encode(str["uri"])+'/'+Encoder.encode(str["uri"])},
													  },
													 "uri",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "Categories : "+str["uri"];}});
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
		    serviceUri : "",
		    getQuery : function(parameters){	
			  
			    var prefix =	'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#>         ' +
							    'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>               ' +
							    'PREFIX dc: <http://purl.org/dc/elements/1.1/>                      ' +
							    'PREFIX swrc: <http://swrc.ontoware.org/ontology#>                  ' +
							    'PREFIX ical: <http://www.w3.org/2002/12/cal/ical#> 				' +
							    'PREFIX foaf: <http://xmlns.com/foaf/0.1/>            		        ' ;
							
			    var query  =	'SELECT DISTINCT  ?personName ?personHomepage ?personImg ?organizationUri ?organizationName ?publicationUri ?publicationName ?roleUri  ?eventUri ?eventName WHERE  {  ' +
							    ' { <'+parameters.uri+'>  foaf:name ?personName.                     ' +
							    '	OPTIONAL {<'+parameters.uri+'>  foaf:homepage ?personHomepage.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  foaf:img ?personImg.}' +
							    ' } UNION { ?organizationUri swrc:hasMember  <'+parameters.uri+'> . '+ 
							    '   ?organizationUri  rdfs:label ?organizationName.  ' +
							    ' } UNION { ?publicationUri foaf:maker  <'+parameters.uri+'> . '+ 
							    '   ?publicationUri  dc:title ?publicationName. ' +
							    ' } UNION { ?roleUri  swc:heldBy <'+parameters.uri+'>. '+ 
							    '   ?roleUri  swc:isRoleAt ?eventUri. ' +
							    '	?eventUri  ical:summary ?eventName. }' +
							    '}';
				var  ajaxData = { query : prefix + query, output : "json" };
		      	return ajaxData; 
		    }, 
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONToken = {};
				var results = dataJSON.results.bindings;
				if(_.size(results) > 0 ){
					JSONToken.name =  results[0].authorName ? results[0].authorName.value : null;
					//JSONToken.description =  results[0].description ? results[0].description : null;
					JSONToken.homepage = results[0].authorHomepage ? results[0].authorHomepage.value : null;
					JSONToken.image = results[0].authorImg ? results[0].authorImg.value : null;

					// JSONToken.roles = {};
					// var i = 0;
					// for(var j=0;j<dataXML[0].roles.length;j++){
					// 	var currentRole= dataXML[0].roles[j];
					// 	if(!JSONToken.roles[currentRole.type]){
					// 		JSONToken.roles[currentRole.type] = [];
					// 	}
					// 	JSONToken.roles[currentRole.type].push(currentRole["event"]);
						
					// }

					JSONToken.organizations = [];
					JSONToken.roles = {};
					JSONToken.publications = [];
					j = 0;
					k = 0;
					l= 0;
					$.each(results, function(i, token) {
						if(token.hasOwnProperty("publicationUri")){
							JSONToken.publications[j] =  token;
							j++;
						} 
						if(token.hasOwnProperty("organizationUri")){
							JSONToken.organizations[k]=  token;
							k++;
						}
						if(token.hasOwnProperty("roleUri")){
							if(!JSONToken.roles[token.roleUri.value]){
								
								JSONToken.roles[token.roleUri.value] = [];
							}
					    	JSONToken.roles[token.roleUri.value].push(token);
						}
					});

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
								parameters.contentEl.append($('<div style="min-height:50px; width:30%"><img style="width:100%;height:auto;" src="'+parameters.JSONdata.image+'"/></div>'));    
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
														  hrefCllbck:function(str){return Encoder.encode(str["eventName"])+"/"+Encoder.encode(str["eventUri"])},
														  },
														 "eventName",
														 parameters.contentEl,
														 {type:"Node",labelCllbck:function(str){return "person : "+str["eventUri"];}});
								};

							}

							if(parameters.JSONdata.publications.length > 0){
								parameters.contentEl.append($('<h2>'+parameters.conference.acronym+' Publications</h2>'));
								for(var i = 0; i < parameters.JSONdata.publications.length; i++){ 
									var publication = parameters.JSONdata.publications[i];
									ViewAdapterText.appendButton(parameters.contentEl,'#publication/'+Encoder.encode(publication.publicationName.value)+'/'+Encoder.encode(publication.publicationUri.value), publication.publicationName.value,{tiny : false});
									

								};
							}


							if(parameters.JSONdata.organizations.length > 0){
								parameters.contentEl.append($('<h2>Organizations</h2>'));
								for(var i = 0; i < parameters.JSONdata.organizations.length; i++){ 
									var organization = parameters.JSONdata.organizations[i];
									ViewAdapterText.appendButton(parameters.contentEl,'#organization/'+Encoder.encode(organization.organizationName.value)+'/'+Encoder.encode(organization.organizationUri.value), organization.organizationName.value,{tiny : true});
								};
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
		    serviceUri : "",
		    getQuery : function(parameters){	
		    
				var prefix =   'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> ';
				var query =   'SELECT DISTINCT ?personName  ?personUri ?personImg WHERE  { ' +
								'   <'+parameters.conference.baseUri+'> swc:isSuperEventOf ?uriSubEvent.' + 
								'   <'+parameters.uri+'> swc:isRoleAt  ?uriSubEvent.  ' +
								'    <'+parameters.uri+'>  swc:heldBy ?personUri.' +
								'   ?personUri  foaf:name ?personName.' +
								'   OPTIONAL{?personUri  foaf:img ?personImg.}' +
								'} ORDER BY ASC(?personName) '; 
				var  ajaxData = { query : prefix + query, output : "json" };
				return ajaxData;
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONfile = {};
				$.each(dataJSON.results.bindings,function(i){  
					var JSONToken = {};
					JSONToken.name =  this.personName ? this.personName.value : null;
					JSONToken.uri =  this.personUri ? this.personUri.value : null;
					JSONToken.image =  this.personImg ? this.personImg.value : null;
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
													  hrefCllbck:function(str){return Encoder.encode(str["name"])+"/"+Encoder.encode(str["uri"])},
													  },
													 "name",
													 "image",
													 parameters.contentEl,
													 {type:"Node",labelCllbck:function(str){return "person : "+str["uri"];}});
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
		    serviceUri : "",
		    getQuery : function(parameters){	
			  
			    var prefix =	'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#>         ' +
							    'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>               ' +
							    'PREFIX dc: <http://purl.org/dc/elements/1.1/>                      ' +
							    'PREFIX swrc: <http://swrc.ontoware.org/ontology#>                  ' +
							    'PREFIX foaf: <http://xmlns.com/foaf/0.1/>            		        ' ;
							
			    var query  =	'SELECT DISTINCT   ?publiTitle ?publiAbstract ?publiUrl ?authorUri ?authorName ?keywordUri ?keywordName WHERE  {  ' +
							    '  {<'+parameters.uri+'>  dc:title ?publiTitle.                     ' +
							    '	OPTIONAL {<'+parameters.uri+'>  swrc:abstract ?publiAbstract.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  swrc:url ?publiUrl.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  swrc:year ?publiPublishDate.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  swrc:link_publisher ?publiPublisher.}' +
							    '	 } UNION {<'+parameters.uri+'>  foaf:maker  ?authorUri. '+ 
							    '   ?authorUri  foaf:name ?authorName.  }' +
							    '	 UNION  {<'+parameters.uri+'>  dc:subject  ?keywordUri. '+
							    '   ?keywordUri  rdfs:label ?keywordName.  }' + 		 
							    '}';
				var  ajaxData = { query : prefix + query, output : "json" };
		      	return ajaxData; 
		    }, 
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				var JSONToken = {};
				var results = dataJSON.results.bindings;
				if(_.size(dataJSON) > 0 ){
					JSONToken.title =  results[0].publiTitle ? results[0].publiTitle.value : null;
					JSONToken.abstract =  results[0].publiAbstract ?  results[0].publiAbstract.value : null;
					JSONToken.publishDate =  results[0].publiPublishDate ? results[0].publiPublishDate .value : null;
					JSONToken.url =  results[0].publiUrl ? results[0].publiUrl.value : null;
					JSONToken.publisher =  results[0].publiPublisher ? results[0].publiPublisher.value : null;

					JSONToken.keywords = [];
					JSONToken.authors = [];
					j = 0;
					k = 0;
					$.each(results, function(i, token) {
						if(token.hasOwnProperty("authorUri")){
							JSONToken.authors[j] =  token;
							j++;
						} 
						if(token.hasOwnProperty("keywordUri")){
							JSONToken.keywords[k]=  token;
							k++;
						}
					});
						
					
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
								for(var i = 0; i < parameters.JSONdata.authors.length; i++){ 
									var author = parameters.JSONdata.authors[i];
									ViewAdapterText.appendButton(parameters.contentEl,'#person/'+Encoder.encode(author.authorName.value)+'/'+Encoder.encode(author.authorUri.value), author.authorName.value,{tiny : true});
								};
								
							}
							if(_.size(parameters.JSONdata.keywords) > 0 ){
								parameters.contentEl.append($('<h2>Keywords</h2>'));
								for(var i = 0; i < parameters.JSONdata.keywords.length; i++){ 
									var keyword = parameters.JSONdata.keywords[i];
									ViewAdapterText.appendButton(parameters.contentEl,'#topic/'+Encoder.encode(keyword.keywordLabel.value)+'/'+Encoder.encode(keyword.keywordUri.value),keyword.keywordLabel.value, {tiny : true});
								};

							}
						}
					}
				} 
			}
		},


		// getAllTheme : {
		//     dataType : "JSONP",
		//     method : "GET", 
		//     serviceUri : "",
		//     getQuery : function(parameters){	
		// 	 	 var prefix =	'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#>         ' +
		// 					    'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>               ' +
		// 					    'PREFIX dc: <http://purl.org/dc/elements/1.1/>                      ' +
		// 					    'PREFIX swrc: <http://swrc.ontoware.org/ontology#>                  ' +
		// 					    'PREFIX foaf: <http://xmlns.com/foaf/0.1/>            		        ' ;
							
		// 	    var query  =	'SELECT DISTINCT   ?publiTitle ?publiAbstract ?publiUrl ?authorUri ?authorName ?keywordUri ?keywordName WHERE  {  ' +
		// 					    '  {<'+parameters.uri+'>  dc:title ?publiTitle.                     ' +
		// 					    '	OPTIONAL {<'+parameters.uri+'>  swrc:abstract ?publiAbstract.}' +
		// 					    '	OPTIONAL {<'+parameters.uri+'>  swrc:url ?publiUrl.}' +
		// 					    '	OPTIONAL {<'+parameters.uri+'>  swrc:year ?publiPublishDate.}' +
		// 					    '	OPTIONAL {<'+parameters.uri+'>  swrc:link_publisher ?publiPublisher.}' +
		// 					    '	 } UNION {<'+parameters.uri+'>  foaf:maker  ?authorUri. '+ 
		// 					    '   ?authorUri  foaf:name ?authorName.  }' +
		// 					    '	 UNION  {<'+parameters.uri+'>  dc:subject  ?keywordUri. '+
		// 					    '   ?keywordUri  rdfs:label ?keywordName.  }' + 		 
		// 					    '}';
		// 		var  ajaxData = { query : prefix + query, output : "json" };
		//       return ajaxData; 
		//     },
		    
		//     ModelCallBack : function(dataXML,conferenceUri,datasourceUri, currentUri){
		// 		var JSONfile = {};
		// 		$.each(dataXML,function(i){  
		// 			var JSONToken = {};
		// 			JSONToken.themename =  this.name || "";
		// 			JSONfile[i] = JSONToken;
		// 		});
		// 			console.log(JSONfile);
		// 		//StorageManager.pushCommandToStorage(currentUri,"getConferenceMainTrackEvent",JSONfile);
		// 		return JSONfile;
		// 	},
				
		// 	ViewCallBack : functioeventStartn(parameters){
		// 		//Reasoner.getMoreSpecificKeywords();
		// 		if(parameters.JSONdata != null){
		// 			if(_.size(parameters.JSONdata) > 0 ){
		// 				if(parameters.mode == "text"){
		// 					parameters.contentEl.append('<h2>Themes</h2>'); 
		// 					ViewAdapterText.appendList(parameters.JSONdata,
		// 											 {baseHref:'#theme/',
		// 											  hrefCllbck:function(str){return Encoder.encode(str["themename"])},
		// 											  },
		// 											 "themename",
		// 											 parameters.contentEl,
		// 											 {type:"Node",labelCllbck:function(str){return "Track : "+str["themename"];}});
		// 				}

		// 			}
		// 		} 
		// 	}
		// },

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
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "", 
		    getQuery : function(parameters){	
		        var prefix =	'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#>         ' +
							    'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>               ' +
							    'PREFIX dc: <http://purl.org/dc/elements/1.1/>                      ' +
							    'PREFIX ical: <http://www.w3.org/2002/12/cal/ical#> 				' +
							    'PREFIX swrc: <http://swrc.ontoware.org/ontology#>                  ' +
							    'PREFIX foaf: <http://xmlns.com/foaf/0.1/>            		        ' ;
							
			    var query  =	'SELECT DISTINCT  ?eventSummary ?eventStart ?eventEnd ?eventDesc ?eventComent ?eventUrl ?eventContact ?locationUri ?locationName ?subEventSummary ?subEventUri ?roleUri ?subEventUri ?personUri ?personName WHERE  {  ' +
							    ' { <'+parameters.uri+'>  ical:summary ?eventSummary. ' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:dtstart ?eventStart.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:dtend ?eventEnd.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:description ?eventDesc.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:comment ?eventComent.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:url ?eventUrl.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:contact ?eventContact.}' +
							    ' } UNION {	<'+parameters.uri+'>  swc:hasLocation ?locationUri. '+ 
							    '   ?locationUri  rdfs:label ?locationName. ' +
							    ' }' +
							    '}';
				var  ajaxData = { query : prefix + query, output : "json" };
		      	return ajaxData; 
			      
			     
		    },
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
			 	var JSONfile = {}; 
			 	var results = dataJSON.results.bindings;
			    if(_.size(results) > 0 ){
				
					var JSONToken = {};
					JSONfile.eventLabel = results[0].eventSummary ? results[0].eventSummary.value : null;
					JSONfile.eventDescription =   results[0].eventDesc ? results[0].eventDesc.value : null;
					JSONfile.eventAbstract =   results[0].eventComent ? results[0].eventComent.value : null;
					JSONfile.eventHomepage =  results[0].eventUrl ? results[0].eventUrl.value : null;
					JSONfile.eventStart = results[0].eventStart ? results[0].eventStart.value : null;
					JSONfile.eventEnd = results[0].eventEnd ? results[0].eventEnd.value : null;

					JSONfile.roles = [];
					JSONfile.locations = [];
					j = 0;
					k = 0;

					$.each(results, function(i, token) {
						if(token.hasOwnProperty("roleUri")){
							JSONfile.roles[j] =  token;
							j++;
						} 
						if(token.hasOwnProperty("locationUri")){
							JSONfile.locations[m] =  token;
							k++;
						}

					});
				}

				StorageManager.pushCommandToStorage(currentUri,"getConferenceEvent",JSONfile);
				return JSONfile;
				
			}, 
		    
		    
			ViewCallBack : function(parameters){
				var JSONdata = parameters.JSONdata;
				var conferenceUri = parameters.conferenceUri;

				if(parameters.JSONdata != null){
					
					var eventInfo = parameters.JSONdata;
						
					if(_.size(eventInfo) > 0 ){
						if(parameters.mode == "text"){
						
							if(eventInfo.eventLabel){ 
								$("[data-role = page]").find("#DataConf").html(eventInfo.eventLabel);
							}
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
						
						

							// if(eventInfo.eventThemes && eventInfo.eventThemes.length>0){
							// 	parameters.contentEl.append('<h2>Themes</h2>'); 
							// 	$.each(eventInfo.eventThemes, function(i,theme){
							// 		ViewAdapterText.appendButton(parameters.contentEl,'#topic/'+Encoder.encode(theme.name)+"/"+Encoder.encode(theme.id),theme.name,{tiny : 'true'});
							// 	});
							// }

							// if(_.size(parameters.JSONdata.authors) > 0 ){
							// 	parameters.contentEl.append($('<h2>Authors</h2>'));
							// 	for(var i = 0; i < parameters.JSONdata.authors.length; i++){ 
							// 		var author = parameters.JSONdata.authors[i];
							// 		ViewAdapterText.appendButton(parameters.contentEl,'#person/'+Encoder.encode(author.authorName.value)+'/'+Encoder.encode(author.authorUri.value), author.authorName.value,{tiny : true});
							// 	};
							// }

						
						
							if(_.size(parameters.JSONdata.locations) > 0 ){
								parameters.contentEl.append($('<h2>Location</h2>'));
								for(var i = 0; i < parameters.JSONdata.locations.length; i++){ 
									var location = parameters.JSONdata.locations[i];
									ViewAdapterText.appendButton(parameters.contentEl,'#schedule/'+Encoder.encode(location.locationName.value)+'/'+Encoder.encode(location.locationUri.value),location.locationName.value, {tiny : true});
								};
							}


						
						}
					}
				}
			}
	    },


	    /** Command used to get and display the name, the start and end time and location of a given event  **/ 
	    getEvent : {
		    dataType : "JSONP",
		    method : "GET",
		    serviceUri : "", 
		    getQuery : function(parameters){	
		    	  
			    var prefix =	'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#>         ' +
							    'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>               ' +
							    'PREFIX dc: <http://purl.org/dc/elements/1.1/>                      ' +
							    'PREFIX ical: <http://www.w3.org/2002/12/cal/ical#> 				' +
							    'PREFIX swrc: <http://swrc.ontoware.org/ontology#>                  ' +
							    'PREFIX foaf: <http://xmlns.com/foaf/0.1/>            		        ' ;
							
			    var query  =	'SELECT DISTINCT  ?eventSummary ?eventStart ?eventEnd ?eventDesc ?eventComent ?eventUrl ?eventContact ?locationUri ?locationName ?subEventSummary ?subEventUri ?roleUri ?subEventUri ?personUri ?personName WHERE  {  ' +
							    ' { <'+parameters.uri+'>  ical:summary ?eventSummary. ' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:dtstart ?eventStart.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:dtend ?eventEnd.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:description ?eventDesc.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:comment ?eventComent.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:url ?eventUrl.}' +
							    '	OPTIONAL {<'+parameters.uri+'>  ical:contact ?eventContact.}' +
							    ' } UNION {	<'+parameters.uri+'>  swc:hasLocation ?locationUri. '+ 
							    '   ?locationUri  rdfs:label ?locationName. ' +
							    ' } UNION { <'+parameters.uri+'>  swc:isSuperEventOf ?subEventUri. '+ 
							    '   ?subEventUri  foaf:summary ?subEventSummary.  ' +
							    ' } UNION { <'+parameters.uri+'>  swc:isSuperEventOf ?subEventUri. '+ 
							    '   ?subEventUri  foaf:summary ?subEventSummary.  ' +
							    ' } UNION { <'+parameters.uri+'>  swc:hasRole ?roleUri. '+ 
							    '   ?roleUri  swc:heldBy ?personUri. ' +
							    '	?personUri  foaf:name ?personame. }' +
							    '}';
				var  ajaxData = { query : prefix + query, output : "json" };
		      	return ajaxData; 
			      
		    },
		    
		    ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
			 	var JSONfile = {}; 
			 	var results = dataJSON.results.bindings;
			    if(_.size(results) > 0 ){
				
					var JSONToken = {};
					JSONfile.eventLabel = results[0].eventSummary ? results[0].eventSummary.value : null;
					JSONfile.eventDescription =   results[0].eventDesc ? results[0].eventDesc.value : null;
					JSONfile.eventAbstract =   results[0].eventComent ? results[0].eventComent.value : null;
					JSONfile.eventHomepage =  results[0].eventUrl ? results[0].eventUrl.value : null;
					JSONfile.eventStart = results[0].eventStart ? results[0].eventStart.value : null;
					JSONfile.eventEnd = results[0].eventEnd ? results[0].eventEnd.value : null;

					JSONfile.roles = [];
					JSONfile.subEvents = [];
					JSONfile.publications = [];
					JSONfile.topics = [];
					JSONfile.locations = [];
					j = 0;
					k = 0;
					l = 0;
					m = 0;
					n = 0;
					$.each(results, function(i, token) {
						if(token.hasOwnProperty("roleUri")){
							JSONfile.roles[j] =  token;
							j++;
						} 
						if(token.hasOwnProperty("subEventUri")){
							JSONfile.subEvents[k] =  token;
							k++;
						}
						if(token.hasOwnProperty("publiUri")){
							JSONfile.publications[l] =  token;
							l++;
						}
						if(token.hasOwnProperty("topicUri")){
							JSONfile.topics[m] =  token;
							m++;
						}
						if(token.hasOwnProperty("locationUri")){
							JSONfile.locations[m] =  token;
							n++;
						}

					});
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
						
							if(eventInfo.eventLabel){ 
								$("[data-role = page]").find("#DataConf").html(eventInfo.eventLabel);
							}
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
						
						

							// if(eventInfo.eventThemes && eventInfo.eventThemes.length>0){
							// 	parameters.contentEl.append('<h2>Themes</h2>'); 
							// 	$.each(eventInfo.eventThemes, function(i,theme){
							// 		ViewAdapterText.appendButton(parameters.contentEl,'#topic/'+Encoder.encode(theme.name)+"/"+Encoder.encode(theme.id),theme.name,{tiny : 'true'});
							// 	});
							// }

							// if(_.size(parameters.JSONdata.authors) > 0 ){
							// 	parameters.contentEl.append($('<h2>Authors</h2>'));
							// 	for(var i = 0; i < parameters.JSONdata.authors.length; i++){ 
							// 		var author = parameters.JSONdata.authors[i];
							// 		ViewAdapterText.appendButton(parameters.contentEl,'#person/'+Encoder.encode(author.authorName.value)+'/'+Encoder.encode(author.authorUri.value), author.authorName.value,{tiny : true});
							// 	};
							// }

							if(_.size(parameters.JSONdata.topics) > 0 ){
								parameters.contentEl.append($('<h2>Topics</h2>'));
								for(var i = 0; i < parameters.JSONdata.topics.length; i++){ 
									var topic = parameters.JSONdata.topics[i];
									ViewAdapterText.appendButton(parameters.contentEl,'#topic/'+Encoder.encode(topic.topicName.value)+'/'+Encoder.encode(topic.topicUri.value),topic.topicName.value, {tiny : true});
								};
							}

							if(_.size(parameters.JSONdata.locations) > 0 ){
								parameters.contentEl.append($('<h2>Location</h2>'));
								for(var i = 0; i < parameters.JSONdata.locations.length; i++){ 
									var location = parameters.JSONdata.locations[i];
									ViewAdapterText.appendButton(parameters.contentEl,'#schedule/'+Encoder.encode(location.locationName.value),location.locationName.value, {tiny : true});
								};
							}

							if(_.size(parameters.JSONdata.subEvents) > 0 ){
								parameters.contentEl.append('<h2>Sub events</h2>'); 
								for(var i = 0; i < parameters.JSONdata.subEents.length; i++){ 
									var subEvent = parameters.JSONdata.subEents[i];
									ViewAdapterText.appendButton(parameters.contentEl,'#event/'+Encoder.encode(subEvent.subEventName.value)+"/"+Encoder.encode(subEvent.subEventUri.value),subEvent.subEventName.value,{tiny : 'true'});
								};
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
			serviceUri : "",  
			getQuery : function(parameters) {  
				 var prefix =	'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#>         ' +
							    'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>               ' +
							    'PREFIX dc: <http://purl.org/dc/elements/1.1/>                      ' +
							    'PREFIX ical: <http://www.w3.org/2002/12/cal/ical#> 				' +
							    'PREFIX swrc: <http://swrc.ontoware.org/ontology#>                  ' +
							    'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>			' +
							    'PREFIX foaf: <http://xmlns.com/foaf/0.1/>            		        ' ;
							
			    var query  =	'SELECT DISTINCT  ?eventUri ?eventSummary ?eventStart ?eventEnd ?eventType ?locationUri ?locationName WHERE  {  ' +
							    '  <'+parameters.conference.baseUri+'>  swc:isSuperEventOf ?eventUri '+
							    '	OPTIONAL {?eventUri ical:summary ?eventSummary. }' +
							    '	OPTIONAL {?eventUri ical:dtstart ?eventStart.}' +
							    '	OPTIONAL {?eventUri  ical:dtend ?eventEnd.}' +
							    '	OPTIONAL {?eventUri rdf:type ?eventType.}' +
							    '   OPTIONAL {?eventUri  swc:hasLocation ?locationUri. '+ 
							    '   ?locationUri  rdfs:label ?locationName. }' +
							    '}';

				var  ajaxData = { query : prefix + query, output : "json" };
		      	return ajaxData; 
			},
			//Declaring the callback function to use when sending the command
			ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
				
				
					var JSONfile = {};
					$(dataJSON.results.bindings).each(function(i,event){  
						console.log(event);
						
						//////////////////////////////
						/// look for special event ///
						//////////////////////////////
					  	var currentEvent = {};
						currentEvent.eventType = event.eventType ? event.eventType.value : "";
						 
						if(currentEvent.eventType!="Event" && currentEvent.eventType!="http://data.semanticweb.org/ns/swc/ontology#ConferenceEvent"){ 
						   
					    //retrieve current Start Slot
						  var currentStartSlot =  event.eventStart.value; 	
						  if(!JSONfile[currentStartSlot]) JSONfile[currentStartSlot] = {}; 
						  currentStartSlot = JSONfile[currentStartSlot];
						  
					    //retrieve current End Slot
						  var currentEndSlot =  event.eventEnd.value;
						  if(!currentStartSlot[currentEndSlot]) currentStartSlot[currentEndSlot] = {bigEvents:{},events:[]}; 
						  currentEndSlot = currentStartSlot[currentEndSlot];
						  
						
						  //retrieve current eventType slot
						  if(!currentEndSlot.bigEvents[currentEvent.eventType]) currentEndSlot.bigEvents[currentEvent.eventType] = [];  
						  
						  
						//then push to the correct start/end slot 
						if(event.eventStart.value)
						  currentEvent.eventUri = event.eventUri ? event.eventUri.value : null; 
						  currentEvent.eventLabel =  event.eventSummary ? event.eventSummary.value : null;
						//  currentEvent.eventDesc =  $(this).find("[name = eventDesc]").text();
						  currentEvent.locationLabel =  event.locationName ? event.locationName.value : null;
				          currentEndSlot.bigEvents[currentEvent.eventType].push(currentEvent);
						  
						}else { 
						
						  //currentEndSlot.events.push(currentEvent);
						  
						}
						
					});
					StorageManager.pushCommandToStorage(currentUri,"getConferenceSchedule",JSONfile);
					return JSONfile;
				
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
		                            var newEventlink = $('<a href="#event/'+Encoder.encode(bigEvents[eventType][i].eventLabel)+'/'+Encoder.encode(bigEvents[eventType][i].eventUri)+'">');
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
			serviceUri : "",  
			getQuery : function(parameters) {  

			 	var prefix =	'PREFIX swc: <http://data.semanticweb.org/ns/swc/ontology#>         ' +
						    'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>               ' +
						    'PREFIX dc: <http://purl.org/dc/elements/1.1/>                      ' +
						    'PREFIX ical: <http://www.w3.org/2002/12/cal/ical#> 				' +
						    'PREFIX swrc: <http://swrc.ontoware.org/ontology#>                  ' +
						    'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>			' +
						    'PREFIX foaf: <http://xmlns.com/foaf/0.1/>            		        ' ;
							
			    var query  =	'SELECT DISTINCT  ?eventUri ?eventSummary ?eventStart ?eventEnd ?eventType ?locationUri ?locationName WHERE  {  ' +
							    '  <'+parameters.conference.baseUri+'>  swc:isSuperEventOf ?eventUri '+
							    '	OPTIONAL {?eventUri ical:summary ?eventSummary. }' +
							    '	OPTIONAL {?eventUri ical:dtstart ?eventStart.}' +
							    '	OPTIONAL {?eventUri  ical:dtend ?eventEnd.}' +
							    '	OPTIONAL {?eventUri rdf:type ?eventType.}' +
							    '   OPTIONAL {?eventUri  swc:hasLocation ?locationUri. '+ 
							    '   ?locationUri  rdfs:label ?locationName. }' +
							    '} ORDER BY ASC(?eventStart)';

				var  ajaxData = { query : prefix + query, output : "json" };
		      	return ajaxData; 
			},
			//Declaring the callback function to use when sending the command
			ModelCallBack : function(dataJSON,conferenceUri,datasourceUri, currentUri){
							
				if(dataJSON.length != 0){
					var JSONfile = {};
					var seenLocation = [];
					$(dataJSON.results.bindings).each(function(i,event){  
						console.log(event);
						if(event.eventStart){
							var now = new Date();
							if(moment(now).isBefore(event.eventStart.value)){
							  	var currentEvent = {};
								currentEvent.eventType = event.eventType ? event.eventType.value : "";
								currentEvent.eventLocation = event.locationName ? event.locationName.value:"";
								 
								if(currentEvent.eventType!="Event" && currentEvent.eventType!="http://data.semanticweb.org/ns/swc/ontology#ConferenceEvent" && currentEvent.eventLocation !=""){ 
									   
								    //retrieve first event by location
									var currentLocation =  event.locationName.value; 	
									if(_.indexOf(seenLocation, currentLocation) == -1){
										seenLocation.push(currentLocation);
										JSONfile[i] = {};

										currentEvent.eventUri = event.eventUri ? event.eventUri.value : null;
										currentEvent.eventLabel =  event.eventSummary ?  event.eventSummary.value : null;
										currentEvent.eventStart=  event.eventStart ? event.eventStart.value : null;
										currentEvent.eventEnd= event.eventEnd ? event.eventEnd.value : null;
										
										JSONfile[i].location = currentLocation;
										JSONfile[i].event = currentEvent;
									}
								}
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

							currentUl.append('<li data-inset="true"  ><a href="#event/'+Encoder.encode(location.event.eventLabel)+'/'+Encoder.encode(location.event.eventUri)+'">\
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
 	return liveconSparqlCommandStore;
});
