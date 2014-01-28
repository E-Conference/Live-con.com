



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
    // getRootNode : function(documentRootNode){
    //     var rootNode = $(documentRootNode).children();
    //     $(documentRootNode).each(function(){
    //         if(this.nodeName.toUpperCase()=== "CONFERENCE"){
    //             rootNode = $(this);
    //         }
    //     })
    //     return rootNode;
    // },
    getNodeKey : "idAttr",
    getNodeName : "localName",
    parseItemOrder : {
            "organizationMapping" : "organizations",
            "personMapping" : "persons",
            "proceedingMapping" : "proceedings",
            "eventMapping" : "events"
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
    personMapping : {
        //nodes are wrapped in a collection node
        wrapped : true,
        nodeName : 'persons',
        label : {
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
                    key : "text",
                    array : "organizations",
                },  
            },
        }
    },

    eventMapping : {  
        nodeName : 'sessions',
        wrapped : true,
        label : {
            'name' : {
                setter : 'setSummary'
            },
            'papers' : {
                wrapped : true,
                multiple : true,
                setter : 'addPaper',
                fk : {
                    key : "text",
                    array : "proceedings",
                },  
            },
            'pc-chairs' : {
                wrapped : true,
                multiple : true,
                setter : 'addChair',
                fk : {
                    key : "text",
                    array : "persons",
                }, 
            }
        },
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

    proceedingMapping : {
        nodeName : 'papers',
        wrapped : true,
        label : {
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
                setter : 'addTopic',
                //keywords aren't entities in this format and thus, don't contains any index 
                //so we must retrieve an index with getArrayId instead of objectMap 
                fk : {
                    key : "text",
                    array : "topics",
                    findInArrayWith : "setName",
                    create : true,
                },   
            },
            //authors are retrieved from their id in the objectMap .
            'authors' : {
                wrapped : true,
                multiple : true,
                setter : 'addAuthor',
                fk : {
                    key : "text",
                    array : "persons",
                },
            }
        },
    },

    organizationMapping : {
        nodeName : 'organizations',
        wrapped : true,
        label : {
            'name' : {
                setter : 'setName',
            },
            'country' : {
                setter : 'setCountry',
            },
        }

    },

};