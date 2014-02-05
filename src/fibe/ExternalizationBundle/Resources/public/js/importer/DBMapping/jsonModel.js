Model = {
	

	"entities" : {
		"Conference" : { 
			attributes : {
				required : {
					"summary" : {
						setter : "setSummary",
				}},
				optionnal : { 
					"url" : {
						setter : "setUrl",
					}, 
					"description" : {
						setter : "setDescription",
					},
					"comment" : {
						setter : "setComment",
					}, 
					"organizer" : {
						setter : "setOrganizer",
					}, 
					"contacts" : {
						setter : "setContacts",
					}, 
					"acronym" : {
						setter : "setAcronym",
					}, 
					"logo" : {
						setter : "setLogo",
					},
					"location" : {
						setter : "setLocation",
				}}
			} 
		}, 
		"Events" : { 
            array   :"events",
			attributes : {
				required : {
					"summary" : {
						setter : "setSummary",
				}},
				optionnal : {  
					"description" : {
						setter : "setStartAt",
					},
					"description" : {
						setter : "setEndAt",
					},
					"description" : {
						setter : "setDescription",
					},
					"comment" : {
						setter : "setComment",
					}, 
					"organizer" : {
						setter : "setOrganizer",
					}, 
					"contacts" : {
						setter : "setContacts",
					}, 
					"acronym" : {
						setter : "setAcronym",
					}, 
					"logo" : {
						setter : "setLogo",
					},
					"location" : {
						setter : "setLocation",
				}}
			} 
		}, 
		"Person" : {
            array   :"persons",
			attributes : {
				required : {
					"familyName" : {
						setter : "setFamilyName",
					}, 
					"firstName" : {
						setter : "setFirstName",
				}},
				optionnal : { 
					"email" : {
						setter : "setEmail",
					}, 
					"age" : {
						setter : "setAge",
					},
					"img" : {
						setter : "setImg",
					}, 
					"page" : {
						setter : "setPage",
					}, 
					"organizations" : {
						setter : "setOrganizations",
					}, 
					"accounts" : {
						setter : "setAccounts",
					}, 
					"papers" : {
						setter : "setPapers",
				}}
			}
		},

	 
		"Publication" : {
            array   :"proceedings",
			attributes : {
				required : {
					"title" : {
						setter : "setTitle",
					}, 
					"abstract" : {
						setter : "setAbstract",
				}},
				optionnal : { 
					"publishdate" : {
						setter : "setPublishdate",
					}, 
					"publisher" : {
						setter : "setPublisher",
					},
					"url" : {
						setter : "setUrl",
					}, 
					"authors" : {
						setter : "setAuthors",
					}, 
					"subjects" : {
						setter : "setSubjects",
					}, 
					"topics" : {
						setter : "setTopics",
					}, 
					"events" : {
						setter : "setEvents",
				}}
			}
		},

		"Organization" : {
            array   : "organizations",
			attributes : {
				required : {
					"name" : {
						setter : "setName",
				}},
				optionnal : { 
					"page" : {
						setter : "setPage",
					},
					"country" : {
						setter : "setCountry",
					}, 
					"members" : {
						setter : "setMembers",
					}, 
					"subjects" : {
						setter : "setSubjects",
					}, 
					"topics" : {
						setter : "setTopics",
					}, 
					"events" : {
						setter : "setEvents",
				}} 
			}
	  	},

		// "Social Account" : {
		// 	attributes : {
		// 		required : {
		// 			"accountName" : {
		// 				setter : "setAccountName",
		// 		}},
		// 		optionnal : { 
		// 			"owner" : {  
		// 				setter : "setOwner",
		// 			},
		// 			"socialService" : {
		// 				setter : "setSocialService",
		// 		}}
		// 	}

		// },

	
		"Location" : {
            array   : "locations", 
			attributes : {
				required : {
					"name" : {
						setter : "setName",
				}},
				optionnal : { 
					"capacity" : {
						setter : "setCapacity",
					}, 
					"description" : {
						setter : "setDescription",
					}, 
					"longitude" : {
						setter : "setLongitude",
					}, 
					"latitude" : {
						setter : "setLatitude",
				}}
			}
					 
		}, 
		"Keyword" : {
            array : "topics", 
			attributes : {
				required : {
					"name" : {
						setter : "setName",
				}},
				optionnal : {}
		
			}		
		}
	},

	modelToTab : function($el){
        var html = Mapper.getPanelHtml("Base model",{panelClass:"panel-primary"}); 

		for(var i in Model.entities){
  			var entity = Model.entities[i];
        	html += Mapper.getPanelHtml(i,{panelClass:"panel-danger",margin:true,"model-path":entity.array || i});

       		$.each(entity.attributes.required, function(aIndex, attribute){ 
	       		var newAttr = Model.generateAttributeNode(aIndex, {required:true,"model-path":entity.array+"/"+aIndex});
	       		// newEntity.append(newAttr);
       			html += newAttr;
	       	})

       		$.each(entity.attributes.optionnal, function(aIndex, attribute){ 
       			var newAttr = Model.generateAttributeNode(aIndex, {required:false,"model-path":entity.array+"/"+aIndex});
       			html += newAttr;
       			// newEntity.append(newAttr);
	       	})
	        html+= Mapper.getClosingPanelHtml();
        };
        html+= Mapper.getClosingPanelHtml();
        $el.html(html);
 
		$(".model-node").droppable({
			accept: ".map-node" ,
            tolerance: "pointer" ,
			over: function( event, ui ) { 
		        	if($(this).hasClass("list-group-item-danger")){
			        	$(this).data("oldStyle","danger")
			        	$(this).removeClass("list-group-item-danger");
			        }else if($(this).hasClass("list-group-item-success")){
			        	$(this).data("oldStyle","success") 
			        }else{
			        	$(this).data("oldStyle",false)
			        }
			        $(this).addClass("list-group-item-success") 
			        var $this =$(this); 
		        		setTimeout(function() {validateParentPanel($this);
		        	},1);
			},
			out: function( event, ui ) { 
	        	$(this).removeClass("list-group-item-success")
		        if($(this).data("oldStyle")) {
		        	$(this).addClass("list-group-item-"+$(this).data("oldStyle")) ;
		        }
		        var $this =$(this); 
	        		setTimeout(function() {validateParentPanel($this);
	        	},1); 
			},
			drop: function( event, ui ) {  
		        // $(this).removeClass("list-group-item-success")
		        Mapper.dataLinks[$(this).data("model-path")] = {nodePath : ui.draggable.data("node-path")};
		        console.log(Mapper.dataLinks);  
			}
		});

		function validateParentPanel($div){

		        var panelDiv = $div.parent().parent(); 
		        if($div.siblings(".list-group-item-danger").length==0){
		        	panelDiv.addClass("panel-success")
		        	panelDiv.removeClass("panel-danger")
		        }else{
		        	panelDiv.removeClass("panel-success")
		        	panelDiv.addClass("panel-danger")
		        }
		}
	}, 

	generateAttributeNode : function(attribute, options){
        return '<li class="model-node list-group-item'+(options.required===true?" list-group-item-danger":"")+'" data-required="'+(options.required===true)+'" data-model-path="'+options["model-path"]+'" style="'+options.style+'">'+attribute+'</li>'; 

	},

	getSetter : function(entityName,attribute){
        var modelMapping = Model.getModelMappingByArrayName(entityName.toLowerCase()); 
        if(modelMapping.attributes.required[attribute]){
            return modelMapping.attributes.required[attribute].setter;
        }else{
            return modelMapping.attributes.optionnal[attribute].setter;
        }
    },

    getArrayName : function(entityName){
        for(var i in Model.entities){
        	debugger
            if( i.toLowerCase() == entityName.toLowerCase()){
                return entityName;
            }
        }
    },
	getModelMappingByArrayName : function(arrayName){
		if(arrayName=="Conference".toLowerCase())return Model.entities["Conference"];
	    for(var i in Model.entities){
	    	if(i == "Conference")continue;
	        if(Model.entities[i].array.toLowerCase() == arrayName){
	            return Model.entities[i];
	        }
	    }
    },

	
}



