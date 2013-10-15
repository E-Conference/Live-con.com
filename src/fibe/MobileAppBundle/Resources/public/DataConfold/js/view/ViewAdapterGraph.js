/**   
*	Copyright <c> Claude Bernard - University Lyon 1 -  2013
* 	License : This file is part of the DataConf application, which is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License. See details at : http://liris.cnrs.fr/lionel.medini/wiki/doku.php?id=dataconf&#licensing 
*   Author: Lionel MEDINI(supervisor), Florian BACLE, Fiona LEPEUTREC, Benoît DURANT-DE-LA-PASTELLIERE, NGUYEN Hoang Duy Tan
*   Description: This file contains all function used to handle the graph view.
*   Version: 1.2
*   Tags:  Backbone Jquery-ui-mobile Adapter Linked-Data Graph html5 canvas
**/
define(['jquery', 'underscore', 'arbor'], function($, _, arbor){
	var ViewAdapterGraph = { 
	   
	    rootNodeUri : '',
	    nodeLimit : 9,
	    nodeCounter : 0,
	    theUI : '',

		/** Initialize the particul system **/
		initSystem : function (){
			this.sys = arbor.ParticleSystem();
		},
		
		/** Prepare the canvas the root node and the renderer of the graph
		* el : The current page identifier
		* rootNodeUri : The current root node in use
		**/
	    initContainer : function(el,rootNodeUri,rootNodeLabel){
			this["nodeCounter"]=0;
			this.el=el;

			if(rootNodeLabel != undefined){
				this.rootNodeUri=rootNodeLabel;
			}else{
				this.rootNodeUri=rootNodeUri;
			}
			//ViewAdapterGraph.rootNodeUri=rootNodeUri;
			

			this.canvas = $('<canvas style="clear:both; id="graph">');	
			el.append(ViewAdapterGraph.canvas);
			
			this["theUI"] = {nodes:{},edges:{}};
			this.theUI.nodes[this.rootNodeUri]={color:"#8F0000", alpha:0.8, rootNode:true, alone:true, mass:2.5};
			this.theUI.edges[this.rootNodeUri]={};

		  
			this.sys.parameters({stiffness:900, repulsion:2000, gravity:true, dt:0.015});
			this.sys.renderer = Renderer(this.canvas);
			this.sys.graft(this.theUI);
			
			$(this.sys.renderer).on('navigate',function(event,data){
				if(data.href!=undefined)document.location.href = data.href;
			});
	    },
		
		/** Parsing of a result list **/
		appendList : function(dataList,href,labelProperty,appendToDiv,graphPt){
	 
		if(!href) var href={};
			_.each(dataList, function(i,currentData){
				var currentHref=href.baseHref+href.hrefCllbck(currentData);
				var currentLabel=currentData[labelProperty];
		    
				//show
				if(currentLabel){
					//graph node
					if(graphPt){
						var nodeLabel = graphPt.labelCllbck(currentData);
						
						 self.addNode(nodeLabel,currentHref,graphPt.option);
					}  
				}
		   },this);//end each  
		},

	    
	    //generate clickable node
	    addNode : function(label,href,option){
			if(this.nodeCounter<=this.nodeLimit){
			if(!option)var option ={}; 
				var rootNodeLabel=this.rootNodeUri;
				this.theUI.nodes[label]={color     : (option.color?option.color:"#53CF29"), 
												fontColor : (option.fontColor?option.fontColor:"#F2F2F2"), 
												fontSize : (option.fontSize?option.fontSize:14), 
												alpha     : (option.alpha?option.alpha:0.9),
												href      : href
												   };
				this.theUI.edges[rootNodeLabel][label] = {length:1};
				this["nodeCounter"]=this.nodeCounter+1;
				this.sys.merge(this.theUI);  
			}
	    },
	    
	    //generate info node
	    addLeaf : function(label,option){
		
			if(this.nodeCounter<=this.nodeLimit){

			if(!option)var option ={}; 

				var rootNodeLabel=this.rootNodeUri;
				this.theUI.nodes[label]={color     : (option.color?option.color:"orange"), 
												  fontColor : (option.fontColor?option.fontColor:"#F2F2F2"), 
												  alpha     : (option.alpha?option.alpha:0.5),
												 };
				this.theUI.edges[rootNodeLabel][label] = {length:1};
				this["nodeCounter"]=this.nodeCounter+1;
				this.sys.merge(this.theUI); 
	     
			}
	    }, 
	};
	return ViewAdapterGraph;
});






