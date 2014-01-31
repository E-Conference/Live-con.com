



var ocsConfig = {
    checkFormat : function(documentRootNode){
 
        var formatOk = false;
        $(documentRootNode).each(function(){
            if(this.nodeName.toUpperCase()=== "CONFERENCE"){
                console.log("input file is OCS");
                formatOk= true;
            }
        })

        return formatOk; 
    },
    rootNode : {
        format : [{
            nodeUtils : "node",
            arg : ["conference"],
        }] 
    },

    getNodeKey : {
        format : [{
            nodeUtils : "attr",
            arg : ["id"],
        }]
    },
    
    getNodeName : {
        format : [{
            nodeUtils : "localName",
        }]
    }, 
    //preproccessing of the root node which contains the conference informations
    
    parseConference : {
        //conference mapping
        setSummary : {
            format : [{
                nodeUtils : "child",
                arg : ["name"],
            },{
                nodeUtils : "text",
            }] 
        },
        setAcronym : {
            format : [{
                nodeUtils : "child",
                arg : ["acronym"],
            },{
                nodeUtils : "text",
            }] 
        },
        setDescription : {
            format : [{
                nodeUtils : "child",
                arg : ["description"],
            },{
                nodeUtils : "text",
            }] 
        },
        setUrl : {
            format : [{
                nodeUtils : "child",
                arg : ["homepage"],
            },{
                nodeUtils : "text",
            }] 
        },
    },
    
    mappings : [
        {
            array   : "organizations",
            nodeName: 'organizations',
            wrapped : true,
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
            wrapped : true,
            array   :"persons",
            nodeName: 'persons',
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
                            nodeUtils : "text",
                        }],
                        array : "organizations",
                    },  
                },
            }
        },
        {
            array   :"proceedings",
            nodeName: 'papers',
            wrapped : true,
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
                    list : {delimiter:";"},
                    setter : 'addTopic',
                    //pointed entity isn't a concrete node in this format and thus, don't contains any index 
                    //so we must retrieve an index with getArrayId instead of objectMap 
                    fk : {
                        format : [{
                            nodeUtils : "text",
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
                            nodeUtils : "text",
                        }],
                        array : "persons",
                    },
                }
            },
        },
        {  
            array   : "events",
            nodeName: 'sessions',
            wrapped : true,
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
                            nodeUtils : "text",
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
                            nodeUtils : "text",
                        }],
                        array : "persons",
                    }, 
                }
            },
            // set all events to sessionEvent
            postProcess : function(node,event){
                var catName = "SessionEvent";
                var catId = getArrayId("categories",'setName',catName);
                if(catId==-1){
                  var category= {}; 
                  category['setName']=catName;
                  objects.categories.push(category);
                  catId = objects.categories.length-1;
                }
                event['addCategorie']=catId; 
            },
        },

    ],
    

    
    

};