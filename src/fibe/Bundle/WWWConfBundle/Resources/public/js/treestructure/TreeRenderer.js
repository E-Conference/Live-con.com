  var TreeRenderer = {
    initted : false,
    $treeDiv : undefined,
    updateTreeStructure : function(events){
    if(!TreeRenderer.$treeDiv)
      TreeRenderer.$treeDiv = $('#tree-structure');
    if(!TreeRenderer.initted)
    {
      TreeRenderer.initted = true;
      TreeRenderer.$treeDiv.jstree({
        "core" : {
          "themes" : {
            "variant" : "large"
          },

          'data' : events
        },
        "checkbox" : {
          "keep_selected_style" : false,
        },
        "plugins" : [ "checkbox" ]
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
   }
  };