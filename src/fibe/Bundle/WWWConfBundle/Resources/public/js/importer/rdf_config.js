
/**
 *      rdf/owl importer config for OWLImporter.js
 */


var rdfConfig = {
    isRDF : false,
    getRootNode : function(documentRootNode){
        rdfConfig.isRDF = $(documentRootNode).children()[0].nodeName === "rdf:RDF";
        return $(documentRootNode).children();
    },
    getNodeKey : function(node){ 
        return $(node).attr("rdf:about");
    },
    getNodeName : function(node){
        var uri=[];
        var rtn;
        $(node).children().each(function(){ 
            if(this.nodeName.indexOf("rdf:type")!== -1 ){
                if($(this).attr('rdf:resource').indexOf("#")!== -1 ){ 
                    uri.push($(this).attr('rdf:resource').split('#')[1]); 
                }
                else{
                    var nodeName = $(this).attr('rdf:resource').split('/'); 
                    uri.push(nodeName[nodeName.length-1]);  
                }
            } 
        }); 
        if($.inArray('KeynoteTalk', uri) > -1 || node.nodeName.indexOf('KeynoteTalk')>-1)
        {   
            rtn = 'KeynoteEvent';
        }
        else if(uri.length==1)
        {
            rtn = uri[0];
        }
        else if(uri.length==0) //rdf
        { 
            rtn = node.nodeName;
        } 
        return rtn;
    },
    getParseItemOrder : function(){
        return {
            "locationMapping" : locations,
            "organizationMapping" : organizations,
            "personMapping" : persons,
            "proceedingMapping" : proceedings,
            "eventMapping" : events
        };
    },
    personMapping : {
        nodeName : 'Person',
        label : {

            //some dataset use rdfs:label instead of foaf ontology
            'rdfs:label' : {
                setter : 'setFirstName',
                format : function(node){  
                    return $(node).text().split(" ")[0];
                },
                action : function(node,person){
                    person["setFamilyName"] =$(node).text().split(" ")[1] || "";
                }
            },
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
            'swrc:affiliation' : {
                multiple : true,
                setter : 'addOrganization',
                format : function(node){ 
                    var key = $(node).text() || $(node).attr("rdf:resource"); 
                    if(objectMap[key])
                        return $.inArray(objectMap[key], organizations);
                    else {
                        console.warn("organization : "+key+" can't be found");
                    }  
                },
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

        label : {
            'dc:title' : {
                setter : 'setTitle',
            },
            'rdfs:label' : {
                setter : 'setTitle',
            },
            'swrc:abstract': {
                setter : 'setAbstract',
            }, 
            //keywords entity are created directly here (or retrieved)
            //then we register the correct index
            'dc:subject' : {
                multiple : true,
                setter : 'addSubject',
                format : function(node){ 
                    var keywordName = $(node).text() || $(node).attr("rdf:resource");
                    var index = getKeywordIdFromName(keywordName);
                    return index !== -1 ? index : false ;
                },
                action : function(node){
                    var keywordName = $(node).text() || $(node).attr("rdf:resource");  
                    if(getKeywordIdFromName(keywordName)=== -1 ){
                        keywords.push({'setName':str_format(keywordName)});  
                    }
                }
            },
            //authors are retrieved from their id in the objectMap .
            'dc:creator' : {
                multiple : true,
                setter : 'addAuthor',
                format : function(node){ 
                    var key = $(node).text() || $(node).attr("rdf:resource"); 
                    if(objectMap[key])
                        return $.inArray(objectMap[key], persons);
                    else {
                        console.warn("author : "+key+" can't be found");
                    }  
                }, 
            },
            'foaf:maker' : {
                multiple : true,
                setter : 'addAuthor',
                format : function(node){ 
                    var key = $(node).text() || $(node).attr("rdf:resource"); 
                    if(objectMap[key]){
                        return $.inArray(objectMap[key], persons);
                    }else {
                        console.warn("author : "+key+" can't be found");
                    }  
                }, 
            },


        },
        /*
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
        }*/
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
                    // var xproperty= {}; 
                    // xproperty['setCalendarEntity']=events.length;
                    // xproperty['setXNamespace']="publication_uri";
                    // xproperty['setXValue']=$(node).text() || $(node).attr('rdf:resource');
                    // xproperties.push(xproperty);
                }
            },
            'dc:subject' : {
                multiple: true,
                setter : 'addTheme',
                format : function(node){ 
                    var themeName = $(node).text() || $(node).attr("rdf:resource"); 
                    return getThemeIdFromName(themeName);
                },
                action : function(node){
                    var themeName = $(node).text() || $(node).attr("rdf:resource"); 
                    if(getThemeIdFromName(themeName)=== -1 ){
                        themes.push({setName:str_format(themeName)});  
                    }
                }
            },
            'swc:hasLocation' : {
                setter : 'setLocation',
                format : function(node){ 
                    var key = $(node).text() || $(node).attr('rdf:resource');
                    if(objectMap[key])
                        locationName = objectMap[key]['setName'];
                    else {
                        locationName = key.split("/");
                        locationName = locationName[locationName.length -1 ];
                    }
                    return getLocationIdFromName(locationName);
                },
                action : function(node){
                    var key = $(node).text() || $(node).attr('rdf:resource');
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
                    var key = $(node).text() || $(node).attr('rdf:resource');
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
                    return $(node).text() || $(node).attr('rdf:resource');
                }, 
            },
        },
        action : function(node,event){

            //TODO refactore that
            //TODO refactore that
            //TODO refactore that

            // EVENT CAT 
            var catName,
                tmp;
            //different ways to get the category name 
            tmp = node.nodeName.split("swc:").join("").split("event:").join("");
            if(testCatName(tmp))catName = tmp;

            tmp = rdfConfig.getNodeName(node);
            if(testCatName(tmp))catName = tmp;

            tmp = rdfConfig.getNodeName(node).split("swc:").join("").split("event:").join("");
            if(testCatName(tmp))catName = tmp;

            if(catName.indexOf("Event") !== -1){
                var catId = getCategoryIdFromName(catName);
                if(catId==undefined){ 
                    var category= {}; 
                    category['setName']=catName;
                    if(catName == "ConferenceEvent") {
                        event['mainConferenceEvent']=true;
                    }
                    categories.push(category);
                    catId = categories.length-1;
                }
                event['addCategorie']=catId;
            }
            
            
            // store uri to get the event back in the relation loop
            var xproperty= {}; 
            xproperty['setCalendarEntity']=events.length;
            xproperty['setXNamespace']="event_uri";
            xproperty['setXValue']=$(node).attr('rdf:about');
            xproperties.push(xproperty);

            //don't store the original event
            return false;

            function testCatName(catName){
                return (catName.indexOf("Event") !== -1 && catName !== "Event")
            }
        }, 
    },
 
    relationMapping : {
        nodeName : 'Event',
        overide : function(node){ 
            var event = objectMap[rdfConfig.getNodeKey(node)];
            var found=false;
            $(node).children().each(function(){
                if(this.nodeName=="swc:isSubEventOf"){ 
                    var relatedToEventId=getEventIdFromURI($(this).attr('rdf:resource'))
                    if(relatedToEventId){
                        event['setParent']= relatedToEventId;
                    } 
                } 
            });
            console.log(event);
            // console.log("event",event,"currentEventId",currentEventId)
            // alert("hehe")
            // var found=false;
            // $(event).children().each(function(){
            //     if(this.nodeName=="swc:isSubEventOf"||this.nodeName=="swc:isSuperEventOf"){ 
            //         var relatedToEventId=getEventIdFromURI($(this).attr('rdf:resource'));
            //         if(relatedToEventId!=undefined && events[relatedToEventId]!=undefined ){
                    
            //             var relationId = getRelationIdFromCalendarEntityId(currentEventId,relatedToEventId);
            //             if(!relations[relationId]){
            //                 var relationType = this.nodeName.indexOf("swc:isSubEventOf")!== -1?"PARENT":"CHILD";
            //                 events[currentEventId]['setParent'] = parseInt(relatedToEventId);
            //                 var relation= {}; 
            //                 relation['setCalendarEntity']=parseInt(relatedToEventId); 
            //                 relation['setRelationType']=relationType;
            //                 relation['setRelatedTo']=parseInt(currentEventId);
            //                 //console.log("----------   PUSHED    -----------");
            //                 //console.log(relation);
            //                 relations.push(relation);
    
            //                 var relationType = (relationType=="PARENT"?"CHILD":"PARENT");
            //                 var relation= {};
            //                 relation['setCalendarEntity']=parseInt(currentEventId);
            //                 relation['setRelationType']=relationType;
            //                 relation['setRelatedTo']=parseInt(relatedToEventId);
            //                 //console.log(relation);
            //                 relations.push(relation); 
            //                 found=true;
            //             } 
            //         }else{
            //           //console.log( event['setSummary']+", "+$(this).attr('rdf:resource'));
            //           //console.log("Unknown parent");
                      
            //         }
            //     }  
            // });
        }
    },
    organizationMapping : {
        nodeName : 'Organization',
        label : {
            'rdfs:label' : {
                setter : 'setName'
            },
            'foaf:name' : {
                setter : 'setName'
            }, 
        }
    }, 

}
 