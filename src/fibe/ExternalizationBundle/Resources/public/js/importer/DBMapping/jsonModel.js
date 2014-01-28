Model = {
	

	"entities" : {
		"wwwConf" : {
			label : "event",
			attributes : {
						 required : ["summary"],
						 optionnal : [ "url", "description","comment", "organizer", "contacts", "acronym", "logo","location", "events", "persons", "roles",  "organizations",  "topics" ]

			}
		}, 

		"ConfEvent": {
			label : "conference",
			attributes : {
						required : ["summary"],
						optionnal : [ "url", "description","comment", "organizer", "contacts", "acronym", "logo","location", "papers", "persons", "roles",  "children",  "topics" ]
					}
		},

		"Person":{
			label : "person",
			attributes : {
					required : ["familyName", "firstName"],
					optionnal : [ "email", "age","img", "page", "organizations", "accounts", "papers"]
						
					}
		},

	 
		"Paper": {
			label : "publication",
			attributes : {
					required : ["title", "abstract"],
					optionnal : [ "publishdate", "publisher","url", "authors", "subjects", "topics", "events"]
			}
		},

		"Organization": {
			label : "publication",
			attributes : {
					required : ["name"],
					optionnal : [ "name", "page","country", "members", "subjects", "topics", "events"]
			}
	  	},

		"SocialServiceAccount": {
			label : "Social account",
			attributes : {
					required : ["accountName"],
					optionnal : [ "owner", "socialService"]
			}

		},

	
		"Location": {
			label : "Social account",
			attributes : {
					required : ["name"],
					optionnal : [ "capacity", "description", "longitude", "latitude"]
			}
					 
		 },


		"Topic": {
			label : "Keyword",
			attributes : {
					required : ["name"],
					optionnal : []
			}		
		}
	},

	modelToTab : function($el){
		$.each(Model.entities, function(index, entity) {

       		var newEntity = Model.generateEntityNode(entity);
       		$el.append(newEntity);

       		$.each(entity.attributes.required, function(index, attribute){ 
	       		var newAttr = Model.generateAttributeNode(attribute, { style : "color : red"});
	       		newEntity.append(newAttr);
	       	})

       		$.each(entity.attributes.optionnal, function(index, attribute){ 
       			var newAttr = Model.generateAttributeNode(attribute, {style : "color : green"});
       			newEntity.append(newAttr);
	       	})
	       
        });
	},

	generateEntityNode : function(entity){
		return $("<div class='well'>"+entity.label+"</div>");

	},

	generateAttributeNode : function(attribute, options){
		return $("<div class='well' style="+options.style+">"+attribute+"</div>");

	}


	
}



