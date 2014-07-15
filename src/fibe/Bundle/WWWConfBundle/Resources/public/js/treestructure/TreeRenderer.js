  function TreeRendererView(){
    this.render = render;
    this.on     = on; //listen to event


    var eventCallback = {} //register event with on
    ;

    var eventManager = new EventManager();
    var initted = false;
    $treeDiv = undefined;
    if(!$treeDiv)
      $treeDiv = $('#tree-structure');
      $searchField = $('#tree-structure-search');
    if(!initted)
    {
      initted = true;
      
      $treeDiv.jstree({
        "core" : {
          "check_callback" : true,

          /*******Themes Plugin***/
          "themes" : {
            "variant" : "large"
          },

          /********Data Load*******/
          //'data' : events
        },
        /**********Types Plugin*****/
        "types" : {
           /*Racine*/
          "#" : {
                 "valid_children" : ["Conference event"]
          },
          /*Conference*/
          "Conference event" : {
                 "icon" : "glyphicon glyphicon-file",
                 "valid_children": ["Track event"]
          },
           /*Track*/
          "Track event" : {
                 "icon" : "http://jstree.com/tree.png",
                 "valid_children": ["Session event"]
          },
           /*Session*/
          "Session event" : {
                 "icon" : "glyphicon glyphicon-flash",
                 "valid_children": ["Talk event"]
          },
          "Talk event" : {
             "icon" : "glyphicon glyphicon-leaf",
             "valid_children": [],

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
                  "icon"              : "http://docs.oracle.com/cd/E27300_01/E27309/html/images/duplicate.png",
                  action              : function(data){
                        

                        console.log("duplicate");
                        //Get node to duplicate
                        var inst = $.jstree.reference(data.reference);
                        obj = inst.get_node(data.reference);
                         //get parent
                        parent = inst.get_parent(obj);

                        //Copy object to duplicate
                        inst.copy(obj);
                        //Copy in parent
                        inst.paste(parent);
                        
                    }, 
                  _disabled         : function(data){
                        var inst = $.jstree.reference(data.reference);
                        obj = inst.get_node(data.reference);
                        if(obj.type == "Conference event"){
                          return true;
                        }else{
                          return false;
                        }

                    },   
                  },

                "Create": {
                  "separator_before"  : false,
                  "separator_after"   : true,
                  label               : function(data){
                    var inst = $.jstree.reference(data.reference);
                    obj = inst.get_node(data.reference);
                    if(obj.type=="Conference event"){
                      return "Create Track";
                    }else if(obj.type=="Track event"){
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
                          "label" : "Create",
                          "icon"  : "https://support.webhost.co.nz/themes/client_default/icon_addtofav.gif",
                          "_disabled"     : function (data) {
                               var inst = $.jstree.reference(data.reference);
                               obj = inst.get_node(data.reference);

                               if(obj.type=="Talk event"){
                                  return true;
                               }else{
                                  return false;
                               }
                          },
                          action : function (data) {

                               var inst = $.jstree.reference(data.reference);
                               obj = inst.get_node(data.reference);
                           
                               newObj = inst.create_node(obj, {}, "last", function (new_node) {
                                  setTimeout(function () { inst.edit(new_node); },0);
                                  return new_node;
                                });
                               newObj.parent = obj.id;
                               newObj.categories = [10];
                               console.log(newObj);
                     
                               //Create new node in database 
                               //trigger("create",newObj); 
                               
                            
                          },

                      },
                          
                  },
                },

              "remove" : {
                  "separator_before"  : false,
                  "icon"        : "http://iconizer.net/files/Splashyfish/orig/remove.png",
                  "separator_after" : false,
                  "_disabled"     : function(data){
                    var inst = $.jstree.reference(data.reference);
                    obj = inst.get_node(data.reference);
                    if(obj.type=="Conference event"){
                      return true;
                    }else{
                      return false;
                    }
                  },
                  "label"       : "Delete",
                  "action"      : function (data) {

                    var inst = $.jstree.reference(data.reference),
                    obj = inst.get_node(data.reference);

                    if(inst.is_selected(obj)) {
                      inst.delete_node(inst.get_selected());
                    }
                    else {
                      inst.delete_node(obj);
                    }

                  }
              },

              "rename" : {
                "separator_before"  : false,
                "separator_after" : false,
                "_disabled"     : false, //(this.check("rename_node", data.reference, this.get_parent(data.reference), "")),
                "label"       : "Rename",
                "icon"        : "http://publib.boulder.ibm.com/infocenter/repmhelp/v1r0m0/topic/com.ibm.rational.epm.uireference.doc/images/rename.gif",
                /*
                "shortcut"      : 113,
                "shortcut_label"  : 'F2',
                "icon"        : "glyphicon glyphicon-leaf",
                */
                action      : function (data) {
                  var inst = $.jstree.reference(data.reference),
                  obj = inst.get_node(data.reference);
                  inst.edit(obj);
                }
              },
              "ccp" : {
                "separator_before"  : true,
                "icon"        : false,
                "separator_after" : false,
                "label"       : "Edit",
                "icon"        : "http://png-4.findicons.com/files/icons/2226/matte_basic/16/edit.png",
                "action"      : false,
                "submenu" : {
                  "cut" : {
                    "separator_before"  : false,
                    "separator_after" : false,
                    "label"       : "Cut",
                    "icon"        : "http://www.mricons.com/store/png/3543_4011_edit-cut_16x16.png",
                    "action"      : function (data) {
                      var inst = $.jstree.reference(data.reference),
                        obj = inst.get_node(data.reference);
                      if(inst.is_selected(obj)) {
                        inst.cut(inst.get_selected());
                      }
                      else {
                        inst.cut(obj);
                      }
                    }
                  },
                  "copy" : {
                    "separator_before"  : false,
                    "icon"        : "http://tavmjong.free.fr/INKSCAPE/MANUAL_v14/images/ICONS_STOCK/stock-copy.png",
                    "separator_after" : false,
                    "label"       : "Copy",
                     action      : function (data) {
                      var inst = $.jstree.reference(data.reference),
                      obj = inst.get_node(data.reference);
                      if(inst.is_selected(obj)) {
                        inst.copy(inst.get_selected());
                      }
                      else {
                        inst.copy(obj);
                      }
                    }
                  },
                  "paste" : {
                    "separator_before"  : false,
                    "icon"        : "http://www.garf.org/zapatec/zpmenu/themes/icon/icon_paste.gif",
                    "_disabled"     : function (data) {
                      return !$.jstree.reference(data.reference).can_paste();
                    },
                    "separator_after" : false,
                    "label"       : "Paste",
                  }
                },
             },
            },
          },
        /* "dnd" : {
            "drop_finish" : function (data) { 
                console.log(data);
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
        },*/

         "checkbox" : {
          /* Pour recuperer tous les noeuds checker il faut faire  
          var inst = $.jstree.reference(data.reference),
            inst.get_checked();
            J'ai pas trouver pour faire en sorte que les feuilles n'est pas de checkbox
          */

       },

        "plugins" : ["types", "contextmenu", "search", "dnd", "checkbox"]

      });


      var to = false;
      $searchField.keyup(function () {
        if(to) { clearTimeout(to); }
        to = setTimeout(function () {
          var v = $searchField.val();
          $treeDiv.jstree(true).search(v);
        }, 250);
      });


      $treeDiv
        .bind("loaded.jstree", $.proxy(function (e, data)
        {
            //TODO show only a default session at initialisation
            data.instance.select_node(mainConfEvent.id,false,true);
          
        }))
        .bind("changed.jstree", $.proxy(function (e, data)
        {
           //$treeDiv.jstree(true).settings.core.data = events;
           //$treeDiv.jstree(true).refresh();
        }))
        .bind("select_node.jstree", $.proxy(function (e, data)
        {
          /**** Bind de la selection d'un node**/
          /*Return Id of the Node Selected*/
          console.log(data.node.original);
        }))
        .on("rename_node.jstree", $.proxy(function (e, data)
        {  
          //Uniquement si le noeud ne vient pas d etre creer 
          if(data.old != ""){
            //Current title 
           obj = data.node;
          trigger("update",obj); 
          }
          
        }))
        .on("move_node.jstree", $.proxy(function (e, data)
        {
          //Current title 
           obj = data.node;
           console.log("move");
           console.log(obj);
          //Set new parent 
           obj.parent = data.parent;
          // trigger("update",obj); 

        }))
        .on("delete_node.jstree", $.proxy(function (e, data)
        {
          //TODO delete in EventManager
          //eventManager.delete(data.node);

        }))
        .on("copy_node.jstree", $.proxy(function (e, data)
        {
          

        }))
        .on("paste.jstree", $.proxy(function (e, data)
        {
          console.log(data.mode);
          if(data.mode == "copy_node"){
           data.node.parent = data.parent;
           // newObj = trigger("create",node);
            data.node.id = 280;
            
          }else{
             data.node.parent = data.parent;
            // trigger("update",node);
          }
          console.log(data.node);

        }))
        ;
    }
    else
    {
      /*$treeDiv.jstree({
        'core' : {
          'data' : events
        }
      });
      //TODO make this work
      $treeDiv.jstree("reload");*/
       
    }

    function render(events)
  {
      $treeDiv.jstree(true).settings.core.data = events;
      $treeDiv.jstree(true).refresh();
       
   
  };

   function on(eventName,callBack)
  {
    if (!eventCallback[eventName])
    {
      eventCallback[eventName]=[callBack];
    }
    else
    {
      eventCallback[eventName].push(callBack);
    }
  };
  
  function trigger(eventName,param)
  {
    if(!eventCallback[eventName])
    {
      console.log("no listener for event "+ eventName);
      return;
    }
    for (var i in eventCallback[eventName])
    {
      eventCallback[eventName][i](param);
    }
  }

  };


 