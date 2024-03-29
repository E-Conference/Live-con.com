/**   
* Copyright <c> Claude Bernard - University Lyon 1 -  2013
*  License : This file is part of the DataConf application, which is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License. See details at : http://liris.cnrs.fr/lionel.medini/wiki/doku.php?id=dataconf&#licensing 
*   Author: Lionel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan
*   Description: This object is a sort of view conrollers, it is in charge of changing the page and the switch between the two available view (graph or text)
*                It is directly connected to the ViewAdapterGraph and ViewAdapterText on which it will trigger event in right order.
*   Version: 1.2
*   Tags:  arborjs   
**/
define(['jquery', 'jqueryMobile','arbor', 'view/ViewAdapterGraph', 'view/ViewAdapterText', 'view/AbstractView'], function($, jqueryMobile, arbor, ViewAdapterGraph, ViewAdapterText, AbstractView){
	var ViewAdapter = {

		initialize : function(mode){
			this.mode = mode;
			ViewAdapterGraph.initSystem();
		},

		update : function(routeItem,title,conference,datasources,uri,name){
			this.currentPage = this.changePage(new AbstractView({templateName :  routeItem.view ,title : title, model : conference }));
			this.template = routeItem.view;
			this.graphView = routeItem.graphView;
			this.title = title;
			this.conference = conference;
			this.datasources = datasources;
			this.commands = routeItem.commands;
			this.uri = uri;
			this.name = name;
			this.initPage(this.graphView);
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
		
		initPage : function (showButton){
			
			if(this.mode == "text" || showButton == "no"){
				if(showButton == "yes"){
					this.addswitchButton();
				}
				this.mode = "text";
				_.each(this.commands,function(commandItem){
					ViewAdapterText.generateContainer(this.currentPage,commandItem.name);	
				},this);
			}else{
				this.currentPage.find(".content").empty();
				this.addswitchButton();
				ViewAdapterGraph.initContainer(this.currentPage.find(".content"),this.uri,this.name);
			}
		},
		addswitchButton : function (){
			var btnLabel = "";
			if(this.mode == "text"){
				btnlabel = "Graph View";
			}else{
				btnlabel = "Text View";
			}

			switchViewBtn = ViewAdapterText.appendButton(this.currentPage.find(".content"),'javascript:void(0)',btnlabel,{tiny:true,theme:"b",prepend:true, align : "right",margin: "20px"}) ;
			switchViewBtn.addClass("switch");
			switchViewBtn.css("margin"," 0px");   
			switchViewBtn.css("z-index","20"); 
			switchViewBtn.trigger("create");

			switchViewBtn.click(function(){  
				this.changeMode();
			});
		},
		changeMode : function(){
		
			if(this.mode == "text"){
				this.mode = "graph";
			}else{
				this.mode = "text";
			}
			this.currentPage = this.changePage(new AbstractView({templateName :  this.template ,title : this.title, model : this.conference }), "flip");
			this.initPage(this.graphView);
			
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

