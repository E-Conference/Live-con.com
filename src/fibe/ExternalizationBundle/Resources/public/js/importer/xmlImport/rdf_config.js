
/**
 *      rdf/owl importer config for OWLImporter.js
 */


var rdfConfig = {
    isRDF : false,
 
    util : "xmlUtil",
    getNodeKey : {
        format : [{
            fn : "attr",
            arg : ["rdf:about"]
        }] 
    }, 
    getNodeName : {
        format : [{
            fn : "rdfNodeName"
        }] 
    },  
    mappings : [
        {
            array   : "locations",  
            format : [{
                fn : "children",
                arg : ["meetingroomplace",true]
            }], 
            label : [
                {
                    format : [{
                        fn : "children",
                        arg : ["rdfs:label"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setName'
                }, 
                {
                    format : [{
                        fn : "children",
                        arg : ["rdfs:comment"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setDescription'
                }
            ]
        },
        {
            array   : "organizations",  
            format : [{
                fn : "children",
                arg : ["organization",true]
            }], 
            label : [
                {
                    format : [{
                        fn : "children",
                        arg : ["rdfs:label"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setName'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:name"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setName'
                }
            ]
        },
        {
            array   : "persons", 
            format : [{
                fn : "children",
                arg : ["person",true]
            }],  
            label : [

                //some dataset use rdfs:label instead of foaf ontology
                // TODO ADD SPLITER NODEUTIL
                // TODO ADD SPLITER NODEUTIL
                // TODO ADD SPLITER NODEUTIL
                // TODO ADD SPLITER NODEUTIL
                {
                    format : [{
                        fn : "children",
                        arg : ["rdfs:label"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setFirstName',
                    // format : function(node){  
                    //     return $(node).text().split(" ")[0];
                    // },
                    preProcess : function(val,person){
                        person["setFamilyName"] =val.split(" ")[1] || "";
                    }
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:firstName"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setFirstName'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:lastName"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setFamilyName'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:name"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setName'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:img"]
                    },{
                        fn : "text"
                    }],
                    escape : false,
                    setter : 'setImg'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:homepage"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setPage'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:twitter"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setTwitter'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:description"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setDescription'
                }, 
                {
                    format : [{
                        fn : "children",
                        arg : ["swrc:affiliation"]
                    },{
                        fn : "text"
                    }],
                    multiple : true,
                    setter : 'addOrganization',
                    fk : {
                        format : [{
                            fn : "attr",
                            arg : ["rdf:resource"]
                        }],
                        array : "organizations"
                    }
                }
            ]
        },
        {
            array   : "proceedings",  
            format : [{
                fn : "children",
                arg : ["inproceedings",true]
            }],  

            label : [
                {
                    format : [{
                        fn : "children",
                        arg : ["dc:title"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setTitle'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["rdfs:label"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setTitle'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["swrc:abstract"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setAbstract'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["dc:subject"]
                    },{
                        fn : "text"
                    }],
                    multiple : true,
                    setter : 'addTopic', 
                    fk : {
                        format : [{
                            fn : "text"
                        }],
                        array : "topics",
                        //pointed entity isn't a concrete node in this format and thus, don't contains any index 
                        //so we must retrieve an index with Importer().getArrayId instead of objectMap 
                        create : "setName"
                    }
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["swrc:listkeyword"]
                    },{
                        fn : "text"
                    }],
                    multiple : true,
                    //TODO add splitter format
                    //TODO add splitter format
                    //TODO add splitter format
                    list : {delimiter:", "},
                    setter : 'addTopic',
                    fk : {
                        format : [{
                            fn : "text"
                        }],
                        array : "topics",
                        //pointed entity isn't a concrete node in this format and thus, don't contains any index 
                        //so we must retrieve an index with Importer().getArrayId instead of objectMap 
                        create : "setName"
                    }
                },
                //authors are retrieved from their id in the objectMap .
                {
                    format : [{
                        fn : "children",
                        arg : ["dc:creator"]
                    },{
                        fn : "text"
                    }],
                    multiple : true,
                    setter : 'addAuthor',
                    fk : {
                        format : [{
                            fn : "attr",
                            arg : ["rdf:resource"]
                        }],
                        array : "persons"
                    }
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:maker"]
                    },{
                        fn : "text"
                    }],
                    multiple : true,
                    setter : 'addAuthor',
                    fk : {
                        format : [{
                            fn : "attr",
                            arg : ["rdf:resource"]
                        }],
                        array : "persons"
                    }
                }
            ]
        },
        {
            array   : "events",  
            format : [{
                fn : "children",
                arg : ["event",true]
            }],  
            label : [
                {
                    format : [{
                        fn : "children",
                        arg : ["rdfs:label"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setSummary'
                },
                //only for conference event
                {
                    format : [{
                        fn : "children",
                        arg : ["swc:hasacronym"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setAcronym'
                },
                //only for conference event
                {
                    format : [{
                        fn : "children",
                        arg : ["swc:haslogo"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setLogoPath',
                    format : [{
                        fn : "attr",
                        arg : ["rdf:resource"]
                    }]
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["dce:description"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setDescription'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["ical:description"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setDescription'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["icaltzd:dtstart"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setStartAt'
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["icaltzd:dtend"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setEndAt'
                },
                //TODO ADD TIME PARSER
                //TODO ADD TIME PARSER
                //TODO ADD TIME PARSER
                {
                    format : [{
                        fn : "children",
                        arg : ["ical:dtstart"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setStartAt'
                    // format : function(node){ 
                    //     var rtn;
                    //     $(node).children().each(function(){
                    //         if(this.nodeName !=="ical:date") return;
                    //         rtn = $(this).text();  
                    //     });
                    //     return moment(rtn || $(node).text()).format();
                    // }
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["ical:dtend"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setEndAt'
                    // format : function(node){ 
                    //     var rtn;
                    //     $(node).children().each(function(){
                    //         if(this.nodeName !=="ical:date") return;
                    //         rtn = $(this).text();  
                    //     });
                    //     return moment(rtn || $(node).text()).format();
                    // }
                },
                // 'swc:hasRelatedDocument' : { 
                //     preProcess : function(node){
                //         // var xproperty= {}; 
                //         // xproperty['setCalendarEntity']=events.length;
                //         // xproperty['setXNamespace']="publication_uri";
                //         // xproperty['setXValue']=$(node).text() || $(node).attr('rdf:resource');
                //         // xproperties.push(xproperty);
                //     }
                // },
                {
                    format : [{
                        fn : "children",
                        arg : ["dc:subject"]
                    },{
                        fn : "text"
                    }],
                    multiple: true,
                    setter : 'addTopic',
                    fk : {
                        format : [{
                            fn : "text"
                        }],
                        array : "topics", 
                        create : "setName"
                    }
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["swc:hasLocation"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setLocation'
                    // TODO add spliter nodeutil 
                    // TODO add spliter nodeutil 
                    // TODO add spliter nodeutil 
                    // format : function(node){
                    //     var key = $(node).text() || $(node).attr('rdf:resource');
                    //     if(objectMap[key])
                    //         locationName = objectMap[key]['setName'];
                    //     else {
                    //         locationName = key.split("/");
                    //         locationName = locationName[locationName.length -1 ];
                    //     }
                    //     return Importer().getArrayId("locations",'setName',locationName)  
                    //     // return getLocationIdFromName(locationName);
                    // },
                    // preProcess : function(node){
                    //     var key = $(node).text() || $(node).attr('rdf:resource');
                    //     if(objectMap[key])
                    //         locationName = objectMap[key]['setName'];
                    //     else {
                    //         locationName = key.split("/");
                    //         locationName = locationName[locationName.length -1 ];
                    //     }  
                    //     if(Importer().getArrayId("locations",'setName',locationName) === -1 ){
                    //         locations.push({setDescription:"",setName:str_format(locationName)});  
                    //     }
                    // }
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["icaltzd:location"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setLocation'
                    // TODO add spliter nodeutil 
                    // TODO add spliter nodeutil 
                    // TODO add spliter nodeutil 
                    // format : function(node){ 
                    //     var key = $(node).text() || $(node).attr('rdf:resource');
                    //     if(objectMap[key])
                    //         locationName = objectMap[key]['setName'];
                    //     else {
                    //         locationName = key.split("/");
                    //         locationName = locationName[locationName.length -1 ];
                    //     }
                    //     return Importer().getArrayId("locations",'setName',locationName) ;
                    // }
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["foaf:homepage"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setUrl',
                    format : [{
                        fn : "attr",
                        arg : ["rdf:resource"]
                    }]
                },
                {
                    format : [{
                        fn : "children",
                        arg : ["swc:issubeventof"]
                    },{
                        fn : "text"
                    }],
                    setter : 'setParent',
                    fk : {
                        format : [{
                            fn : "attr",
                            arg : ["rdf:resource"]
                        }],
                        array : "events"
                    }
                } 
            ],
            postProcess : function(node,event,nodeName){

                // EVENT CAT 
                var catName
                    ,tmp
                    ,isMainConfEvent = false;
                //3 different ways to get the category name 
                tmp = node[0].nodeName.split("swc:").join("").split("&swc;").join("").split("event:").join("");
                if(testCatName(tmp))catName = tmp;

                tmp = nodeName; 
                if(testCatName(tmp))catName = tmp;

                tmp = nodeName.split("&swc;").join("").split("swc:").join("").split("event:").join("");
                if(testCatName(tmp))catName = tmp; 
     
                if(catName){
                    var catId = Importer().getArrayId("categories","setName",catName) 
                    if(catId==-1){ 
                        var category= {}; 
                        category['setName']=catName;
                        // console.log(catName);
                        if(catName.toLowerCase() == "conferenceevent") {
                            isMainConfEvent = true;
                            console.log("mainconference event is ",event)
                        }
                        Importer().objects.categories.push(category);
                        catId = Importer().objects.categories.length-1;
                    }
                    if(!isMainConfEvent)event['addCategorie']=catId;
                }
                
                
                // store uri via xproperty array to get the event back in the relation loop
                // if(!isMainConfEvent){
                //     var xproperty= {}; 
                //     xproperty['setCalendarEntity']=Importer().objects.events.length;
                //     xproperty['setXNamespace']="event_uri";
                //     xproperty['setXValue']=$(node).attr('rdf:about');
                //     Importer().objects.xproperties.push(xproperty);
                // }
                //don't store the original event
                return isMainConfEvent;

                function testCatName(catName){
                    if(!catName)return;
                    var cn = catName.toLowerCase(); 

                    return (cn.indexOf("event") !== -1 && cn !== "event")
                }
            }
        },
        
        //TODO DO NOT PERMIT OVERRIDING
        //TODO DO NOT PERMIT OVERRIDING
        //TODO DO NOT PERMIT OVERRIDING
        //TODO DO NOT PERMIT OVERRIDING
        {
            array   : "roles",  
            format : [{
                fn : "children",
                arg : ["presenter",true]
            }],  
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
        {
            array   : "roles",  
            format : [{
                fn : "children",
                arg : ["chair",true]
            }],  
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
        }
    ]
}
 