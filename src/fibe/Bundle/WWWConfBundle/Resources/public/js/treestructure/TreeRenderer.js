  var TreeRenderer = {
    initted : false,
    $treeDiv : undefined,
    updateTreeStructure : function(events){
    if(!TreeRenderer.$treeDiv)
      TreeRenderer.$treeDiv = $('#tree-structure');
      TreeRenderer.$searchField = $('#tree-structure-search');
    if(!TreeRenderer.initted)
    {
      TreeRenderer.initted = true;
      
      TreeRenderer.$treeDiv.jstree({
        "core" : {
          "check_callback" : true,

          /*******Themes Plugin***/
          "themes" : {
            "variant" : "large"
          },

          /********Data Load*******/
          'data' : events
        },
        /**********Types Plugin*****/
        "types" : {
           /*Racine*/
          "#" : {
                 "valid_children" : ["Conference"]
          },
          /*Conference*/
          "Conference" : {
                 "icon" : "glyphicon glyphicon-file",
                 "valid_children": ["Session"]
          },
           /*Track*/
          "Track" : {
                 "icon" : "http://jstree.com/tree.png",
                 "valid_children": ["Session"]
          },
           /*Session*/
          "Session" : {
                 "icon" : "glyphicon glyphicon-flash",
                 "valid_children": ["Event"]
          },
          "Event" : {
             "icon" : "glyphicon glyphicon-leaf",
             "valid_children": []
          },
        },
        /*****Context Menu Plugin*****/
         "contextmenu" : {
           /*Racine*/
            items : {
                "Duplicate": {

                  "separator_before"  : false,
                  "separator_after"   : true,
                  "label"             : "Duplicate",
                  "action"            : false,    
                  },

                "Create": {
                  "separator_before"  : false,
                  "separator_after"   : true,
                  label               : function(data){
                    var inst = $.jstree.reference(data.reference);
                    obj = inst.get_node(data.reference);
                    if(obj.type=="Conference"){
                      return "Create Track";
                    }else if(obj.type=="Track"){
                      return "Create Session";
                    }else{
                      return "Create Event";
                    }

                  },
                  "action"            : false,
                  "submenu" :{
                      "create_file" : {
                          "seperator_before" : false,
                          "seperator_after" : false,
                          "label" : "File",
                          action : function (data) {
                               var inst = $.jstree.reference(data.reference);
                               obj = inst.get_node(data.reference);
                               console.log(obj);
                            
                          },

                      },
                          
                  },
                },
          },
        },
         "dnd" : {
            "drop_finish" : function (data) { 
                alert("DROP"); 
            },
            "drag_check" : function (data) {
              alert('DRAG');
               var inst = $.jstree.reference(data.reference);
                               obj = inst.get_node(data.reference);
                               console.log(obj);
            },
            "drag_finish" : function () { 
                alert("DRAG OK"); 
            }
        },

        "plugins" : ["types", "contextmenu", "search", "dnd"]

      });


  var to = false;
  TreeRenderer.$searchField.keyup(function () {
    if(to) { clearTimeout(to); }
    to = setTimeout(function () {
      var v = TreeRenderer.$searchField.val();
      TreeRenderer.$treeDiv.jstree(true).search(v);
    }, 250);
  });


      TreeRenderer.$treeDiv
        .bind("loaded.jstree", $.proxy(function (e, data)
        {
            //TODO show only a default session at initialisation
            data.instance.select_node(mainConfEvent.id,false,true);
          
        }))
        .bind("changed.jstree", $.proxy(function (e, data)
        {
          $(TreeRenderer).trigger("TreeRenderer.updated", [ data.instance.get_selected() ]);
        }))
        .bind("select_node.jstree", $.proxy(function (e, data)
        {
          /**** Bind de la selection d'un node**/
          /*Return Id of the Node Selected*/
          console.log(TreeRenderer.$treeDiv.jstree(true).get_selected());
        }))
        ;
    }
    else
    {
      TreeRenderer.$treeDiv.jstree({
        'core' : {
          'data' : events
        }
      });
      //TODO make this work
      TreeRenderer.$treeDiv.jstree("reload");
       
    }
   },

  };