/**   
* Copyright <c> Claude Bernard - University Lyon 1 -  2013
*  License : This file is part of the DataConf application, which is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License. See details at : http://liris.cnrs.fr/lionel.medini/wiki/doku.php?id=dataconf&#licensing 
*   Author: Lionel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan
*   Description: This object is a sort of view conrollers, it is in charge of changing the page and the switch between the two available view (graph or text)
*                It is directly connected to the ViewAdapterGraph and ViewAdapterText on which it will trigger event in right order.
*   Version: 1.2
*   Tags:  arborjs   
**/
define(['jquery', 'jqueryMobile', 'view/ViewAdapterText', 'view/AbstractView'], function($, jqueryMobile, ViewAdapterText, AbstractView){
	var ViewAdapter = {

		initialize : function(mode){
			this.mode = mode;
			ViewAdapterGraph.initSystem();
		},

		update : function(routeItem,title,conference,datasources,uri,name){
			this.currentPage = this.changePage(new AbstractView({templateName :  routeItem.view ,title : title, model : conference }));
			this.template = routeItem.view;
			this.title = title;
			this.conference = conference;
			this.datasources = datasources;
			this.commands = routeItem.commands;
			this.uri = uri;
			this.name = name;
			this.initPage();
			return this.currentPage ;
		},

		/** Chaning page handling, call the rendering of the page and execute transition **/
		changePage : function (page, transitionEffect) {
			
			$(page.el).attr('data-role', 'page');
			page.render();
			$('body').append($(page.el));
			var transition = $.mobile.defaultPageTransition;
			if(transitionEffect !== undefined){
				transition = transitionEffect;
			}
			jqueryMobile.changePage($(page.el), {changeHash:false, transition: transition});
			
			$(page.el).bind('pagehide', function(event, data) {
				$(event.currentTarget).remove();
			});
			
			return $(page.el);
		},
		
		initPage : function (){
			_.each(this.commands,function(commandItem){
				ViewAdapterText.generateContainer(this.currentPage,commandItem.name);	
			},this);
		},

		changeMode : function(){
		

			this.currentPage = this.changePage(new AbstractView({templateName :  this.template ,title : this.title, model : this.conference }), "flip");
			this.initPage();
			
			var JSONdata = StorageManager.pullCommandFromStorage(this.uri);
			$.each(this.commands,function(i,commandItem){
			
				var currentDatasource = this.datasources[commandItem.datasource];
				var currentCommand    = currentDatasource.commands[commandItem.name];
				if(JSONdata != null){
					if(JSONdata.hasOwnProperty(commandItem.name)){
						currentCommand.ViewCallBack({JSONdata : JSONdata[commandItem.name],contentEl : this.currentPage.find("#"+commandItem.name), name : this.name, currentUri : this.uri});
					}
				}else{
					var ajaxData   = currentCommand.getQuery({conferenceUri : this.conference.baseUri, uri : this.uri,datasource : currentDatasource, name : name, conference : this.conference});
					AjaxLoader.executeCommand({datasource : currentDatasource, command : currentCommand,data : ajaxData, currentUri : this.uri, contentEl :  this.currentPage.find("#"+commandItem.name), name : name, conference : ViewAdapter.conference});
				}
			},this);
			this.generateJQMobileElement();
		},
		
		generateJQMobileElement : function(){
			this.currentPage.trigger("create");
		}
	};
	return ViewAdapter;
});

