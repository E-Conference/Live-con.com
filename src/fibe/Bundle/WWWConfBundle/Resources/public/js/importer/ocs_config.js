



var ocsConfig = {
    checkFormat : function(documentRootNode){
        return documentRootNode.firstChild && documentRootNode.firstChild.nodeName === 'conference';
    },
    getRootNode : function(documentRootNode){
        return $(documentRootNode).children();
    },
    getNodeKey : function(node){
        return $(node).attr("id");
    },
    action : function(documentRootNode){
        var confName = $(documentRootNode).children("name").text();
        var acronym = $(documentRootNode).children("acronym").text();
        var description = $(documentRootNode).children("description").text();
        var homepage = $(documentRootNode).children("homepage").text();
        //TODO save that
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
            'country' : {
                setter : 'setBased_near',
            },
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
            //keywords entity are created directly here (or retrieved)
            //then we register the correct index
            'keywords' : {
                wrapped : true,
                multiple : true,
                setter : 'addSubject',
                format : function(node){ 
                    var keywordName = $(node).text();
                    var index = getKeywordIdFromName(keywordName);
                    return index !== -1 ? index : false ;
                },
                action : function(node){
                    var keywordName = $(node).text();  
                    if(getKeywordIdFromName(keywordName)=== -1 ){
                        keywords.push({'setName':str_format(keywordName)});  
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
                setter : 'setBased_near',
            },
        }

    },
    relationMapping : {}
};