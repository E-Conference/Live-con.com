
/**
 *      rdf/owl importer config for OWLImporter.js
 */


var rdfConfig = {
    isRDF : false,

    rootNode : {
        format : [{
            nodeUtils : "node",
            arg : ["RDF:RDF"],
        }] 
    }, 
    getNodeKey : {
        format : [{
            nodeUtils : "attr",
            arg : ["rdf:about"],
        }] 
    },
    rootNode : {
        format : [{
            nodeUtils : "node",
            arg : ["RDF:RDF"],
        }] 
    },
    getNodeName : {
        format : [{
            nodeUtils : "rdfNodeName", 
        }] 
    }, 
    parseItemOrder : {
            "locationMapping" : "locations",
            "organizationMapping" : "organizations",
            "personMapping" : "persons",
            "proceedingMapping" : "proceedings",
            "eventMapping" : "events",
            "presenterMapping" : "roles",
            "chairMapping" : "roles", 
            "relationMapping" : "events", 
    },
    personMapping : {
        nodeName : 'person',
        label : {

            //some dataset use rdfs:label instead of foaf ontology
            // TODO ADD SPLITER NODEUTIL
            // TODO ADD SPLITER NODEUTIL
            // TODO ADD SPLITER NODEUTIL
            // TODO ADD SPLITER NODEUTIL
            'rdfs:label' : {
                setter : 'setFirstName',
                format : function(node){  
                    return $(node).text().split(" ")[0];
                },
                preProcess : function(node,person){
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
                fk : {
                    format : [{
                        nodeUtils : "attr",
                        arg : ["rdf:resource"],
                    }],
                    array : "organizations",
                },  
            },
        }
    },

    locationMapping : {
        nodeName : 'meetingroomplace',
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
        nodeName : 'inproceedings',

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
            'dc:subject' : {
                multiple : true,
                setter : 'addTopic', 
                fk : {
                    format : [{
                        nodeUtils : "text",
                    }],
                    array : "topics",
                    //pointed entity isn't a concrete node in this format and thus, don't contains any index 
                    //so we must retrieve an index with getArrayId instead of objectMap 
                    findInArrayWith : "setName",
                    create : true,
                },    
            },
            'swrc:listkeyword' : {
                multiple : true,
                list : {delimiter:", "},
                setter : 'addTopic',
                fk : {
                    format : [{
                        nodeUtils : "text",
                    }],
                    array : "topics",
                    //pointed entity isn't a concrete node in this format and thus, don't contains any index 
                    //so we must retrieve an index with getArrayId instead of objectMap 
                    findInArrayWith : "setName",
                    create : true,
                },    
            },
            //authors are retrieved from their id in the objectMap .
            'dc:creator' : {
                multiple : true,
                setter : 'addAuthor',
                fk : {
                    format : [{
                        nodeUtils : "attr",
                        arg : ["rdf:resource"],
                    }],
                    array : "persons",
                }, 
            },
            'foaf:maker' : {
                multiple : true,
                setter : 'addAuthor',
                fk : {
                    format : [{
                        nodeUtils : "attr",
                        arg : ["rdf:resource"],
                    }],
                    array : "persons",
                }, 
            },
        },
    },

    eventMapping : {
        nodeName : 'event',
        label : {
            'rdfs:label' : {
                setter : 'setSummary'
            },
            //only for conference event
            'swc:hasacronym' : {
                setter : 'setAcronym'
            },
            //only for conference event
            'swc:haslogo' : {
                setter : 'setLogoPath',
                format : [{
                    nodeUtils : "attr",
                    arg : ["rdf:resource"],
                }], 
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
            'icaltzd:dtend' : {
                setter : 'setEndAt'
            },
            //TODO ADD TIME PARSER
            //TODO ADD TIME PARSER
            //TODO ADD TIME PARSER
            'ical:dtstart' : {
                setter : 'setStartAt',
                format : function(node){ 
                    var rtn;
                    $(node).children().each(function(){
                        if(this.nodeName !=="ical:date") return;
                        rtn = $(this).text();  
                    });
                    return moment(rtn || $(node).text()).format();
                }
            },
            'ical:dtend' : {
                setter : 'setEndAt',
                format : function(node){ 
                    var rtn;
                    $(node).children().each(function(){
                        if(this.nodeName !=="ical:date") return;
                        rtn = $(this).text();  
                    });
                    return moment(rtn || $(node).text()).format();
                }
            },
            'swc:hasRelatedDocument' : { 
                preProcess : function(node){
                    // var xproperty= {}; 
                    // xproperty['setCalendarEntity']=events.length;
                    // xproperty['setXNamespace']="publication_uri";
                    // xproperty['setXValue']=$(node).text() || $(node).attr('rdf:resource');
                    // xproperties.push(xproperty);
                }
            },
            'dc:subject' : {
                multiple: true,
                setter : 'addTopic',
                fk : {
                    format : [{
                        nodeUtils : "text",
                    }],
                    array : "topics",
                    findInArrayWith : "setName",
                    create : true,
                },   
                // format : function(node){ 
                //     var topicName = $(node).text() || $(node).attr("rdf:resource"); 
                //     return getTopicIdFromName(topicName);
                // },
                // preProcess : function(node){
                //     var topicName = $(node).text() || $(node).attr("rdf:resource"); 
                //     if(getTopicIdFromName(topicName)=== -1 ){
                //         topics.push({setName:str_format(topicName)});  
                //     }
                // }
            },
            'swc:hasLocation' : {
                setter : 'setLocation',
                // TODO add spliter nodeutil and add to objectmap anyway
                // TODO add spliter nodeutil and add to objectmap anyway
                // TODO add spliter nodeutil and add to objectmap anyway
                format : function(node){
                    var key = $(node).text() || $(node).attr('rdf:resource');
                    if(objectMap[key])
                        locationName = objectMap[key]['setName'];
                    else {
                        locationName = key.split("/");
                        locationName = locationName[locationName.length -1 ];
                    }
                    return getArrayId("locations",'setName',locationName)  
                    // return getLocationIdFromName(locationName);
                },
                preProcess : function(node){
                    var key = $(node).text() || $(node).attr('rdf:resource');
                    if(objectMap[key])
                        locationName = objectMap[key]['setName'];
                    else {
                        locationName = key.split("/");
                        locationName = locationName[locationName.length -1 ];
                    }  
                    if(getArrayId("locations",'setName',locationName) === -1 ){
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
                    return getArrayId("locations",'setName',locationName) ;
                },
            },
            'foaf:homepage' : {
                setter : 'setUrl',
                format : [{
                    nodeUtils : "attr",
                    arg : ["rdf:resource"],
                }],  
            },
        },
        postProcess : function(node,event){

            // EVENT CAT 
            var catName
                ,tmp
                ,isMainConfEvent = false;

            //3 different ways to get the category name 
            tmp = node.nodeName.split("swc:").join("").split("&swc;").join("").split("event:").join("");
            if(testCatName(tmp))catName = tmp;
 
            tmp = doFormat(node,rdfConfig.getNodeName.format); 
            if(testCatName(tmp))catName = tmp;

            tmp = doFormat(node,rdfConfig.getNodeName.format).split("&swc;").join("").split("swc:").join("").split("event:").join("");
            if(testCatName(tmp))catName = tmp; 
 
            if(catName){
                var catId = getArrayId("categories","setName",catName) 
                if(catId==-1){ 
                    var category= {}; 
                    category['setName']=catName;
                    // console.log(catName);
                    if(catName.toLowerCase() == "conferenceevent") {
                        isMainConfEvent = true;
                        console.debug("mainconference event is ",event)
                        defaultDate = event['setStartAt'] || defaultDate;
                    }
                    objects.categories.push(category);
                    catId = objects.categories.length-1;
                }
                if(!isMainConfEvent)event['addCategorie']=catId;
            }
            
            
            // store uri via xproperty array to get the event back in the relation loop
            if(!isMainConfEvent){
                var xproperty= {}; 
                xproperty['setCalendarEntity']=objects.events.length;
                xproperty['setXNamespace']="event_uri";
                xproperty['setXValue']=$(node).attr('rdf:about');
                objects.xproperties.push(xproperty);
            }
            //don't store the original event
            return isMainConfEvent;

            function testCatName(catName){
                var cn = catName.toLowerCase(); 

                return (cn.indexOf("event") !== -1 && cn !== "event")
            }
        }, 
    },
    //TODO DO NOT PERMIT OVERRIDING
    //TODO DO NOT PERMIT OVERRIDING
    //TODO DO NOT PERMIT OVERRIDING
    //TODO DO NOT PERMIT OVERRIDING
    presenterMapping : {
        nodeName : 'presenter', 
        override : function(node){

            var event ;
            $(node).children().each(function(){
                if(this.nodeName=="swc:isroleat"){  
                    event = objectMap[$(this).attr("rdf:resource")]
                } 
            });
            if(event){
                event['addPresenter'] = [];
                $(node).children().each(function(){
                    if(this.nodeName=="swc:heldby"){ 
                        var person = $(this).attr("rdf:resource"); 
                        if(objectMap[person]){
                            event['addPresenter'].push( $.inArray(objectMap[person], persons)); 
                        }
                    } 
                }); 
            }
        }
    },
    chairMapping : {
        nodeName : 'chair',
        override : function(node){

            var event ;
            $(node).children().each(function(){
                if(this.nodeName=="swc:isroleat"){  
                    event = objectMap[$(this).attr("rdf:resource")]
                } 
            });
            if(event){
                event['addChair'] = [];
                $(node).children().each(function(){
                    if(this.nodeName=="swc:heldby"){ 
                        var person = $(this).attr("rdf:resource"); 
                        if(objectMap[person]){
                            event['addChair'].push( $.inArray(objectMap[person], persons)); 
                        }
                    } 
                }); 
            }
        }
    },

    relationMapping : {
        nodeName : 'event',
        override : function(node){  
            var event = doFormat(node,rdfConfig.getNodeKey.format);     
            var found=false;
            $(node).children().each(function(){
                if(this.nodeName.toLowerCase()=="swc:issubeventof"){ 

                var relatedToEventId = getArrayId("xproperties",'setXValue',$(this).attr('rdf:resource')) 
                // var relatedToEventId=getEventIdFromURI($(this).attr('rdf:resource')) 
                    if(relatedToEventId){
                        event['setParent']= relatedToEventId;
                    } 
                } 
            });
        }
    },
    organizationMapping : {
        nodeName : 'organization',
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
 