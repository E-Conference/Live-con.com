



var rdfConfig = {
    getRootNode : function(documentRootNode){   
        return $(documentRootNode).children();
    },
    getNodeKey : function(node){
        return $(node).attr("rdf:about");
    },
    personMapping : {
        nodeName : 'Person',
        label : {
            'foaf:firstName' : {
                setter : 'setFirstName',
            },
            'foaf:lastName' : {
                setter : 'setFamilyName'
            },
            'foaf:name' : {
                setter : 'setName'
            },
            'foaf:img' : {
                escape : false,
                setter : 'setImg'
            },
            'foaf:homepage' : {
                setter : 'setPage'
            },
            'foaf:twitter' : {
                setter : 'setTwitter'
            },
            'foaf:description' : {
                setter : 'setDescription'
            },
        }
    },

    locationMapping : {
        nodeName : 'MeetingRoomplace',
        label : {
            'rdfs:label' : {
                setter : 'setName'
            }, 
            'rdfs:comment' : {
                setter : 'setDescription',
            },
        }
    },

    proceedingMapping : {
        nodeName : 'InProceedings',
        overide : function(addArray,mapping,node){  
            var eventUri;
            $(node).children().each(function()
            {
                if(this.nodeName=="swc:relatedToEvent"  )
                {
                    eventUri = $(this).attr("rdf:resource");
                }
                
            });
            
            if(eventUri){ 
                var eventId = getEventIdFromURI(eventUri);

                //if we find the related event 
                var xproperty= {}; 
                xproperty['setCalendarEntity']=eventId;
                xproperty['setXNamespace']="publication_uri";
                xproperty['setXValue']=$(node).attr('rdf:about');
                
                //we look for the title
                $(node).children().each(function(){
                    if(this.nodeName=="dce:title" || this.nodeName=="rdfs:label"  || this.nodeName=="dc:title" )
                    {
                        //to finally store it in the setXKey !
                        xproperty.setXKey=str_format($(node).text());
                    }
                });
                xproperties.push(xproperty);
            } 
        }
    },

    eventMapping : {
        nodeName : 'Event',
        label : {
            'rdfs:label' : {
                setter : 'setSummary'
            },
            'dce:description' : {
                setter : 'setDescription'
            },
            'ical:description' : {
                setter : 'setDescription'
            },
            'icaltzd:dtstart' : {
                setter : 'setStartAt'
            },
            'ical:dtstart' : {
                setter : 'setStartAt',
                format : function(node){ 
                    var rtn;
                    $(node).children().each(function(){
                        if(this.nodeName !=="ical:date") return;
                        rtn = $(this).text();  
                    });
                    return rtn || $(node).text();
                }
            },
            'icaltzd:dtend' : {
                setter : 'setEndAt'
            },
            'ical:dtend' : {
                setter : 'setEndAt',
                format : function(node){ 
                    var rtn;
                    $(node).children().each(function(){
                        if(this.nodeName !=="ical:date") return;
                        rtn = $(this).text();  
                    });
                    return rtn || $(node).text();
                }
            },
            'swc:hasRelatedDocument' : { 
                action : function(node){
                    var xproperty= {}; 
                    xproperty['setCalendarEntity']=events.length;
                    xproperty['setXNamespace']="publication_uri";
                    xproperty['setXValue']=$(node).attr('rdf:resource');
                    xproperties.push(xproperty);
                }
            },
            'dc:subject' : {
                multiple: true,
                setter : 'addTheme',
                format : function(node){ 
                    var themeName = $(node).text(); 
                    return getThemeIdFromName(themeName);
                },
                action : function(node){
                    var themeName = $(node).text(); 
                    if(getThemeIdFromName(themeName)=== -1 ){
                        themes.push({setName:str_format(themeName)});  
                    }
                }
            },
            'swc:hasLocation' : {
                setter : 'setLocation',
                format : function(node){ 
                    var key = $(node).attr('rdf:resource');
                    if(objectMap[key])
                        locationName = objectMap[key]['setName'];
                    else {
                        locationName = key.split("/");
                        locationName = locationName[locationName.length -1 ];
                    }
                    return getLocationIdFromName(locationName);
                },
                action : function(node){
                    var key = $(node).attr('rdf:resource');
                    if(objectMap[key])
                        locationName = objectMap[key]['setName'];
                    else {
                        locationName = key.split("/");
                        locationName = locationName[locationName.length -1 ];
                    }
                    if(getLocationIdFromName(locationName)=== -1 ){
                        locations.push({setDescription:"",setName:str_format(locationName)});  
                    }
                }
            },
            'icaltzd:location' : {
                setter : 'setLocation',
                format : function(node){ 
                    var key = $(node).attr('rdf:resource');
                    if(objectMap[key])
                        locationName = objectMap[key]['setName'];
                    else {
                        locationName = key.split("/");
                        locationName = locationName[locationName.length -1 ];
                    }
                    return getLocationIdFromName(locationName);
                },
            },
            'foaf:homepage' : {
                setter : 'setUrl',
                format : function(node){ 
                    return $(node).attr('rdf:resource');
                }, 
            },
        },
        action : function(node,event){
              // EVENT CAT
            var catName = node.nodeName.split("swc:").join("").split("event:").join("");
            if(catName=="NamedIndividual")catName= getNodeName(node);
            var tmp=catName;
            if(tmp.split("Event").join("")!="")
            {
                catName=tmp;
            }else //OWL fix
            {
                catName = getNodeName(node).split("swc:").join("").split("event:").join("") ;
                tmp=catName;
                if(tmp.split("Event").join("")!="")
                {
                    catName=tmp; 
                }
            }  //OWL fix

            var catId = getCategoryIdFromName(catName);
            if(catId==undefined){ 
              var category= {}; 
              category['setName']=catName;
              if(catName == "ConferenceEvent") confName = event['setSummary'];
              categories.push(category);
              catId = categories.length-1;
            }
            event['addCategorie']=catId;
            
            
              // EVENT store URI
            var xproperty= {}; 
            xproperty['setCalendarEntity']=events.length;
            xproperty['setXNamespace']="event_uri";
            xproperty['setXValue']=$(node).attr('rdf:about');
            xproperties.push(xproperty);
        }, 
    },
 
    relationMapping : {
        overide : function(event,currentEventId){ 
            var found=false;
            $(event).children().each(function(){
                if(this.nodeName=="swc:isSubEventOf"||this.nodeName=="swc:isSuperEventOf"){ 
                    var relatedToEventId=getEventIdFromURI($(this).attr('rdf:resource'));
                    if(relatedToEventId!=undefined && events[relatedToEventId]!=undefined ){
                    
                        var relationId = getRelationIdFromCalendarEntityId(currentEventId,relatedToEventId);
                        if(!relations[relationId]){
                            var relationType = this.nodeName.indexOf("swc:isSubEventOf")!== -1?"PARENT":"CHILD";
                            events[currentEventId]['setParent'] = parseInt(relatedToEventId);
                            var relation= {}; 
                            relation['setCalendarEntity']=parseInt(relatedToEventId); 
                            relation['setRelationType']=relationType;
                            relation['setRelatedTo']=parseInt(currentEventId);
                            //console.log("----------   PUSHED    -----------");
                            //console.log(relation);
                            relations.push(relation);
    
                            var relationType = (relationType=="PARENT"?"CHILD":"PARENT");
                            var relation= {};
                            relation['setCalendarEntity']=parseInt(currentEventId);
                            relation['setRelationType']=relationType;
                            relation['setRelatedTo']=parseInt(relatedToEventId);
                            //console.log(relation);
                            relations.push(relation); 
                            found=true;
                        } 
                    }else{
                      //console.log( event['setSummary']+", "+$(this).attr('rdf:resource'));
                      //console.log("Unknown parent");
                      
                    }
                }  
            }); 
        }
    },
    organizationMapping : {

    },

}
 