



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
    getRootNode : function(documentRootNode){

        var rootNode = $(documentRootNode).children();
 
        $(documentRootNode).each(function(){
            if(this.nodeName.toUpperCase()=== "CONFERENCE"){ 
                rootNode = $(this);
            }
        })

        return rootNode;
 
    },
    getNodeKey : function(node){
        // console.log(node)
        return $(node).attr("id");
    },
    getNodeName : function(node){
        return node.localName;
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
    action : function(documentRootNode){
        conference = { 
            setSummary    : $(documentRootNode).children("name").text(),
            setAcronym    : $(documentRootNode).children("acronym").text(),
            setDescription: $(documentRootNode).children("description").text(),
            setUrl        : $(documentRootNode).children("homepage").text(),
        }
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
            // 'country' : {
            //     setter : 'setCountry',
            // },
            'organization-id' : {
                multiple : true,
                setter : 'addOrganization',
                format : function(node){ 
                    var key = $(node).text(); 
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
                format : function(node){ 
                    var key = $(node).text(); 
                    if(objectMap[key])
                        return $.inArray(objectMap[key], proceedings);
                    else {
                        console.warn("paper : "+key+" can't be found");
                    }  
                } 
            },
            'pc-chairs' : {
                wrapped : true,
                multiple : true,
                setter : 'addChair',
                format : function(node){ 
                    var key = $(node).text(); 
                    if(objectMap[key])
                        return $.inArray(objectMap[key], persons);
                    else {
                        console.warn("chair : "+key+" can't be found");
                    }  
                },  
            }
        },
        action : function(node,event){
              // add session category
            var catName = "SessionEvent"; 

            var catId = getCategoryIdFromName(catName);
            if(catId==undefined){ 
              var category= {}; 
              category['setName']=catName;
              
              categories.push(category);
              catId = categories.length-1;
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
                format : function(node){ 
                    var topicName = $(node).text();
                    var index = getTopicIdFromName(topicName);
                    return index !== -1 ? index : false ;
                },
                action : function(node){
                    var topicName = $(node).text();  
                    if(getTopicIdFromName(topicName)=== -1 ){
                        topics.push({'setName':str_format(topicName)});  
                    }
                }
            },
            //authors are retrieved from their id in the objectMap .
            'authors' : {
                wrapped : true,
                multiple : true,
                setter : 'addAuthor',
                format : function(node){ 
                    var key = $(node).text(); 
                    if(objectMap[key])
                        return $.inArray(objectMap[key], persons);
                    else {
                        console.warn("author : "+key+" can't be found");
                    }  
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
    relationMapping : {}
};