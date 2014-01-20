
/**
 *      rdf/owl importer config for OWLImporter.js
 */


var rdfConfig = {
    isRDF : false,
    getRootNode : function(documentRootNode){
        var rootNode = $(documentRootNode).children();
 
        $(documentRootNode).each(function(){
            if(this.nodeName.toUpperCase()=== "RDF:RDF"){
                console.log("input file is RDF");
                rdfConfig.isRDF = true;
                rootNode = $(this);
            }
        })
        return rootNode;
    },
    getNodeKey : function(node){ 
        return $(node).attr("rdf:about");
    },
    getNodeName : function(node){
        var uri=[];
        var rtn;
        $(node).children().each(function(){ 
            if(this.localName.indexOf("rdf:type")!== -1 ){
                if($(this).attr('rdf:resource').indexOf("#")!== -1 ){ 
                    uri.push($(this).attr('rdf:resource').split('#')[1]); 
                }
                else{
                    var nodeName = $(this).attr('rdf:resource').split('/'); 
                    uri.push(nodeName[nodeName.length-1]);  
                }
            } 
        });
        // console.log("getNodeName",node.localName)
        // console.log("uri",uri)
        for(var i in uri){
            var lc = uri[i].toLowerCase();
            if(lc.indexOf('keynotetalk')>-1){
                rtn = 'KeynoteEvent'; 
            }
        } 
        var lc = node.localName.toLowerCase();
        if(lc.indexOf('keynotetalk')>-1){
            rtn = 'KeynoteEvent'; 
        }
        else if(uri.length==1)
        {
            rtn = uri[0];
        }
        else if(uri.length==0) //rdf
        { 
            rtn = node.localName;
        } 
        return rtn;
    },
    parseItemOrder : {
            "locationMapping" : "locations",
            "organizationMapping" : "organizations",
            "personMapping" : "persons",
            "proceedingMapping" : "proceedings",
            "eventMapping" : "events",
            "presenterMapping" : "roles",
            "chairMapping" : "roles", 
    },
    persons : {
        nodeName : 'person',
        label : {

            //some dataset use rdfs:label instead of foaf ontology
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

    locations : {
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

    proceedings : {
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
            //topics entity are created directly here (or retrieved)
            //then we register the correct index
            'dc:subject' : {
                multiple : true,
                setter : 'addTopic',
                format : function(node){ 
                    var topicName = $(node).text() || $(node).attr("rdf:resource");
                    var index = getTopicIdFromName(topicName);
                    return index !== -1 ? index : false ;
                },
                preProcess : function(node){
                    var topicName = $(node).text() || $(node).attr("rdf:resource");  
                    if(getTopicIdFromName(topicName)=== -1 ){
                        topics.push({'setName':str_format(topicName)});  
                    }
                }
            },
            'swrc:listkeyword' : {
                multiple : true,
                list : {delimiter:", "},
                setter : 'addTopic',
                format : function(node,value){ 
                    var topicName = value;
                    var index = getTopicIdFromName(topicName);
                    return index !== -1 ? index : false ;
                },
                preProcess : function(node,rtnArray,value){
                    var topicName = value;  
                    if(getTopicIdFromName(topicName)=== -1 ){
                        topics.push({'setName':str_format(topicName)});  
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
    },

    events : {
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
                setter : 'setLogoPath'
                ,format : function(node){ 
                    return $(node).attr("rdf:resource");  
                },
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
                format : function(node){ 
                    var topicName = $(node).text() || $(node).attr("rdf:resource"); 
                    return getTopicIdFromName(topicName);
                },
                preProcess : function(node){
                    var topicName = $(node).text() || $(node).attr("rdf:resource"); 
                    if(getTopicIdFromName(topicName)=== -1 ){
                        topics.push({setName:str_format(topicName)});  
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
                preProcess : function(node){
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
        //post processing
        postProcess : function(node,event){

            // EVENT CAT 
            var catName
                ,tmp
                ,isMainConfEvent = false;

            //3 different ways to get the category name 
            tmp = node.nodeName.split("swc:").join("").split("&swc;").join("").split("event:").join("");
            if(testCatName(tmp))catName = tmp;

            tmp = rdfConfig.getNodeName(node);
            if(testCatName(tmp))catName = tmp;

            tmp = rdfConfig.getNodeName(node).split("&swc;").join("").split("swc:").join("").split("event:").join("");
            if(testCatName(tmp))catName = tmp; 
 
            if(catName){
                var catId = getCategoryIdFromName(catName);
                if(catId==-1){ 
                    var category= {}; 
                    category['setName']=catName;
                    // console.log(catName);
                    if(catName.toLowerCase() == "conferenceevent") {
                        isMainConfEvent = true;
                        console.debug("mainconference event is ",event)
                        defaultDate = event['setStartAt'] || defaultDate;
                    }
                    categories.push(category);
                    catId = categories.length-1;
                }
                if(!isMainConfEvent)event['addCategorie']=catId;
            }
            
            
            // store uri via xproperty array to get the event back in the relation loop
            if(!isMainConfEvent){
                var xproperty= {}; 
                xproperty['setCalendarEntity']=events.length;
                xproperty['setXNamespace']="event_uri";
                xproperty['setXValue']=$(node).attr('rdf:about');
                xproperties.push(xproperty);
            }
            //don't store the original event
            return isMainConfEvent;

            function testCatName(catName){
                var cn = catName.toLowerCase(); 

                return (cn.indexOf("event") !== -1 && cn !== "event")
            }
        }, 
    },
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
        overide : function(node){

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

    relations : {
        nodeName : 'event',
        overide : function(node){ 
            var event = objectMap[rdfConfig.getNodeKey(node)];
            var found=false;
            $(node).children().each(function(){
                if(this.nodeName.toLowerCase()=="swc:issubeventof"){ 
                    var relatedToEventId=getEventIdFromURI($(this).attr('rdf:resource')) 
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
 