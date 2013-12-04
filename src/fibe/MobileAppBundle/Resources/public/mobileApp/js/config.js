 /**   
*	Copyright <c> Claude Bernard - University Lyon 1 -  2013
* 	License : This file is part of the DataConf application, which is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License. See details at : http://liris.cnrs.fr/lionel.medini/wiki/doku.php?id=dataconf&#licensing 
*   Author: Lionel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan
*   Description: This JSON object contains all the configurations of the application. It is a crutial part of the system, it desribes :
*				-> The conference informations, the uri, the logo uri and the name.
*				-> All the datasources defined by their uris, the cross domain  mode they use, and the commandStore (see /model) related to them.
*				   This command store contains the definition of all the command (a specific parameters+query+callback implementation) that can be send on it.
*				-> All the routes that the app will use. Each route is configured to display a specific view, if a template exist for this view name (see /templates)
				   it is rendered, otherwise a generic view is used. The commands we want to send are specified in a "command" array to explicit which command has to be send when the route is catched
				   
*   Tags:  JSON, ENDPOINT, SPARQL
**/
define(['model/SWDFCommandStore', 'model/DBLPCommandStore', 'model/DDGoCommandStore','model/GoogleCommandStore','model/swcEventCommandStore', 'model/DPCommandStore'],
	function(SWDFCommandStore, DBLPCommandStore, DDGoCommandStore, GoogleCommandStore, swcEventCommandStore, DPCommandStore) {
		
		var AppConfig = {
			"app" : {
				"liveconLogo" : "livecon.png"
			},
			//Defnition of the conference
			"conference" : {
				"id": "1",
				"eventId": "1",
				"name": "Livecon",
				"acronym": "Livecon",
				"logoUri": "css/images/banniereTranspBlend.png",
				"website": "http://www.blendconference.com/",
				"baseUri": "http://www.blendconference.com/",
				"storage": "false"
			},
			
			//Defnition of the datasources 
			// uri : It correspond to the uri to be used to access the service
			// crossDomainMode : "CORS" or "JSONP" explicits the cross domain technique to be used on the service 
			// commands : Name of the json var that implements all the commands that can be used on the service
			"datasources" : {
				"SemanticWebConferenceDatasource" : {
					"uri" : "http://poster.www2012.org/endpoint/eswc2013/sparql/",
					"crossDomainMode" : "CORS",
					"commands" : SWDFCommandStore
				},
				
				"DblpDatasource" : {
					"uri" : "http://dblp.rkbexplorer.com/sparql/",
					"crossDomainMode" : "CORS",
					"commands" : DBLPCommandStore
				},

				"DuckDuckGoDatasource" : {   
					"uri" : "http://api.duckduckgo.com/",
					"crossDomainMode" : "JSONP",
					"commands" : DDGoCommandStore
				},
				
				"GoogleDataSource" : {   
					"uri" : "https://ajax.googleapis.com/ajax/services/search/web",
					"crossDomainMode" : "JSONP",
					"commands" : GoogleCommandStore
				},
				"eventDatasource" : {
					"uri" : "http://dataconf.liris.cnrs.fr/simpleschedule-blend/web/api/",
					"crossDomainMode" : "JSONP",
					"commands" : swcEventCommandStore
				},
				"DataPaperDatasource" : {
					"uri" : "http://dataconf.liris.cnrs.fr:5984/datapaper/_design/public/_view/by_type",
					"crossDomainMode" : "JSONP",
					"commands" : DPCommandStore
				}
			}, 
			//Declaration of all the routes to be used by the router
			// hash : url to be catched by the router
			// view : the name of the view to display when catching the route (if a template in /templates matches the name, it is used, otherwise a generic view is used)
			// title : the title to display on the header when showing the view
			// commands : array of datasource/name to precise which command of which datasource to send when catching the route
			"routes" : {
		    "Home" : {
					"hash" : "",
					"view" : "home",
					"graphView" : "no",
					"title": "Du 1 au 2 octobre",
					"commands" : [ 
						{
							"datasource" : "eventDatasource",
							"name" : "getConferenceEvent",
						},
						/*{
							"datasource" : "eventDatasource",
							"name" : "getConferenceTalk",
						},
						{
							"datasource" : "eventDatasource",
							"name" : "getConferenceWorkshop",
						},
						{
							"datasource" : "eventDatasource",
							"name" : "getSessionEvent",
						},
						{
							"datasource" : "eventDatasource",
							"name" : "getConferenceSpecialEvent",
						}*/
						
					]
				}, 
		    	"Schedule" : {
					"hash" : "schedule/*locationLabel",
					"view" : "schedule",
					"graphView" : "no",
					"title": "Conference schedule",
					"commands" : [
						{
						    "datasource" : "eventDatasource",
						    "name" : "getConferenceSchedule"
						},
						{
							"datasource" : "eventDatasource",
							"name" : "getConferenceScheduleIcs"
						}
					]
				},
				"WhatsNext" : {
					"hash" : "whatsnext/",
					"view" : "whatsnext",
					"graphView" : "no",
					"title": "What's next?",
					"commands" : [
						{
						    "datasource" : "eventDatasource",
						    "name" : "getWhatsNext",
						},
					]
				},  
				"person-by-role" : { 
					"hash" : "person-by-role/:name/*uri",
					"view" : "person-by-role",
					"graphView" : "no",
					"title": "Search a person by role",
					"commands" : [
					    {
							"datasource" : "eventDatasource",
							"name" : "getPersonByRole",
						} 
					]
				},
			    "Proceedings-search-by-theme" : { 
					"hash" : "search/by-theme/*uri",
					"view" : "",
					"graphView" : "no",
					"title": "Search by theme",
					"commands" : [
					    {
							"datasource" : "eventDatasource",
							"name" : "getAllTheme",
						} 
					]
				},
			    "Proceedings-search-by-category" : { 
					"hash" : "search/by-category/*uri",
					"view" : "",
					"graphView" : "no",
					"title": "Search by category",
					"commands" : [
					    {
							"datasource" : "eventDatasource",
							"name" : "getAllCategories",
						} 
					]
				},
				"Events" : {
					"hash" : "events",
					"view" : "events",
					"graphView" : "no",
					"title": "Events",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getAllEvents",
						} 
					]
				},
				"Event" : { 
					"hash" : "event/:name/*uri",
					"view" : "event",
					"graphView" : "no",
					"title": "Search in event",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getEvent"	
						},
						{
							"datasource" : "eventDatasource",
							"name" : "getEventIcs"
						}						
					]
				},
				"Event-by-category" : { 
					"hash" : "event-by-category/:name/*uri",
					"view" : "event-by-category",
					"graphView" : "no",
					"title": "Search an event by category",
					"commands" : [
					    {
							"datasource" : "eventDatasource",
							"name" : "getEventByCategory",
						} 
					]
				},
				"Publication" : { 
					"hash" : "publication/:name/*uri",
					"view" : "publication",
					"graphView" : "no",
					"title": "Publication",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getPublication",
						},
						{
							"datasource" : "DataPaperDatasource",
							"name" : "getDataPaperRessource",
						}
					]
				},
				"EventSearch" : {
					"hash" : "search/event",
					"view" : "eventSearch",
					"graphView" : "no",
					"title": "Search an event",
					"commands" : [
					]
				},
				"PersonSearch" : {
					"hash" : "search/person",
					"view" : "personSearch",
					"graphView" : "no",
					"title": "search a person",
					"commands" : [
					]
				},
				"PublicationSearch" : {
					"hash" : "search/publication",
					"view" : "publicationSearch",
					"graphView" : "no",
					"title": "Search a publication",
					"commands" : [
					]
				},

				"Publications" : { 
					"hash" : "publications",
					"view" : "publications",
					"graphView" : "no",
					"title": "Publication",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getAllPublications",
						}
					]
				},

				"OrganizationSearch" : {
					"hash" : "search/organization",
					"view" : "organizationSearch",
					"graphView" : "no",
					"title": "Search an organization",
					"commands" : [
					]
				},
				"Persons" : {
					"hash" : "persons",
					"view" : "persons",
					"graphView" : "no",
					"title": "Persons",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getAllPersons",
						} 
					]
				},
				"Person" : {
					"hash" : "person/:name/*uri",
					"view" : "person",
					"graphView" : "no",
					"title": "Person",
					"commands" : [
						{
							"datasource" : "GoogleDataSource",
							"name" : "getAuthorPersonalPage",
						},
						{
							"datasource" : "eventDatasource",
							"name" : "getPerson",
						},
						{
							"datasource" : "eventDatasource",
							"name" : "getPublicationsByAuthorId",
						},
						{
							"datasource" : "DblpDatasource",
							"name" : "getAuthorPublications",
						}

					]
					
				},
				"Organizations" : {
					"hash" : "organizations",
					"view" : "organizations",
					"graphView" : "no",
					"title": "Organizations",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getAllOrganizations",
						} 
					]
				},
				"Roles" : {
					"hash" : "roles",
					"view" : "roles",
					"graphView" : "no",
					"title": "Roles",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getAllRoles",
						} 
					]
				},

				"Countries" : {
					"hash" : "countries",
					"view" : "countries",
					"graphView" : "no",
					"title": "Countries",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getAllCountries",
						} 
					]
				},
				
				"Topics" : {
					"hash" : "topics",
					"view" : "topics",
					"graphView" : "no",
					"title": "Topics",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getAllTopics",
						},
					]
				},
				"Topic" : {
					"hash" : "topic/:name/*uri",
					"view" : "topic",
					"graphView" : "no",
					"title": "Topic",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getTopic",
						},
					]
				},
				"Categories" : {
					"hash" : "categories",
					"view" : "categories",
					"graphView" : "no",
					"title": "Categories",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getAllCategories",
						} 
					]
				},
				"Category" : {
					"hash" : "category/:name",
					"view" : "category",
					"graphView" : "no",
					"title": "Category",
					"commands" : [
						{
							"datasource" : "eventDatasource",
							"name" : "getEventbyCategory",
						},
					]
				},
				"ExternPublication" : {
					"hash" : "externPublication/*uri",
					"view" : "externPublication",
					"graphView" : "no",
					"title": "External publication",
					"commands" : [
					    {
							"datasource" : "DblpDatasource",
							"name" : "getExternPublicationInfo",
						},
						{
							"datasource" : "DblpDatasource",
							"name" : "getExternPublicationAuthors",
						}
					]
				},
				"Organization" : {
					"hash" : "organization/:name/*uri",
					"view" : "organization",
					"graphView" : "no",
					"title": "Organization",
					"commands" : [
						{
							"datasource" : "DuckDuckGoDatasource",
							"name" : "getResultOrganization",
						},
						{
							"datasource" : "eventDatasource",
							"name" : "getOrganization",
						}
					]
				},
				"Recommendation" : {
					"hash" : "recommendation",
					"view" : "recommendation",
					"graphView" : "no",
					"title": "Recommendation",
					"commands" : [
						{
							"datasource" : "SemanticWebConferenceDatasource",
							"name" : "getRecommendedPublications",
						}
					]
				},
			}
		};
		return AppConfig;
})