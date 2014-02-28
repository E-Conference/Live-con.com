



var ocsConfig = {

    // choosed by the user in the select format
    // checkFormat : function(documentRootNode){
 
    //     var formatOk = false;
    //     $(documentRootNode).each(function(){
    //         if(this.nodeName.toUpperCase()=== "CONFERENCE"){
    //             console.log("input file is OCS");
    //             formatOk= true;
    //         }
    //     })

    //     return formatOk;  
    // }
    
    //the parsing util function set
    util : "xmlUtil",
    getNodeKey : {
        format : [{
            fn : "attr",
            arg : ["id"],
        }]
    },

    getNodeName : {
        format : [{
            fn : "localName",
        }]
    }, 

    //preproccessing of the root node which contains the conference informations 
    parseConference : {
        //conference mapping
        setSummary : {
            format : [{
                fn : "child",
                arg : ["name"],
            },{
                fn : "text",
            }] 
        },
        setAcronym : {
            format : [{
                fn : "child",
                arg : ["acronym"],
            },{
                fn : "text",
            }] 
        },
        setDescription : {
            format : [{
                fn : "child",
                arg : ["description"],
            },{
                fn : "text",
            }] 
        },
        setUrl : {
            format : [{
                fn : "child",
                arg : ["homepage"],
            },{
                fn : "text",
            }] 
        },
    },
    
    //
    mappings : [
        {
            array   : "organizations", 
            format : [{
                fn : "children",
                arg : ["organizations"],
            },{
                fn : "children",
                arg : ["organization"],
            }], 
            label   : {
                'name' : {
                    setter : 'setName',
                },
                'country' : {
                    setter : 'setCountry',
                },
            }

        },
        {
            //nodes are wrapped in a collection node 
            array   :"persons", 
            format : [{
                fn : "children",
                arg : ["persons"],
            },{
                fn : "children",
                arg : ["person"],
            }], 
            label   : {
                'firstname' : {
                    setter : 'setFirstName',
                },
                'lastname' : {
                    setter : 'setFamilyName'
                },
                'email' : {
                    setter : 'setEmail'
                },
                'organization-id' : {
                    multiple : true,
                    setter : 'addOrganization',
                    fk : {
                        format : [{
                            fn : "text",
                        }],
                        array : "organizations",
                    },  
                },
            }
        },
        {
            array   :"proceedings", 
            format : [{
                fn : "children",
                arg : ["papers"],
            },{
                fn : "children",
                arg : ["paper"],
            }], 
            label   : {
                'title' : {
                    setter : 'setTitle',
                },
                'abstract': {
                    setter : 'setAbstract',
                }, 
                //topics entity are created directly here (or retrieved)
                //then we register the correct index
                'keywords' : {
                    wrapped : true,
                    multiple : true, 
                    //TODO add splitter format
                    //TODO add splitter format
                    //TODO add splitter format
                    list : {delimiter:";"},
                    setter : 'addTopic',
                    //pointed entity isn't a concrete node in this format and thus, don't contains any index 
                    //so we must retrieve an index with getArrayId instead of objectMap 
                    fk : {
                        format : [{
                            fn : "text",
                        }],
                        array : "topics", 
                        create : "setName",
                    },   
                },
                //authors are retrieved from their id in the objectMap .
                'authors' : {
                    wrapped : true,
                    multiple : true,
                    setter : 'addAuthor',
                    fk : {
                        format : [{
                            fn : "text",
                        }],
                        array : "persons",
                    },
                }
            },
        },
        {  
            array   : "events", 
            format : [{
                fn : "children",
                arg : ["sessions"],
            },{
                fn : "children",
                arg : ["session"],
            }], 
            label   : {
                'name' : {
                    setter : 'setSummary'
                },
                'papers' : {
                    wrapped : true,
                    multiple : true,
                    setter : 'addPaper',
                    fk : {
                        format : [{
                            fn : "text",
                        }],
                        array : "proceedings",
                    },  
                },
                'pc-chairs' : {
                    wrapped : true,
                    multiple : true,
                    setter : 'addChair',
                    fk : {
                        format : [{
                            fn : "text",
                        }],
                        array : "persons",
                    }, 
                }
            },
            // set all events to sessionEvent
            // postProcess : function(node,event){
            //     var catName = "SessionEvent";
            //     var catId = getArrayId("categories",'setName',catName);
            //     if(catId==-1){
            //       var category= {}; 
            //       category['setName']=catName;
            //       objects.categories.push(category);
            //       catId = objects.categories.length-1;
            //     }
            //     event['addCategorie']=catId; 
            // },
        },

    ],
    

    
    

};