var TreeRenderer = {

  initTreeStructure : function(){
      debugger;
        $('#tree-structure').jstree({
          "core" : {
            "themes" : {
              "variant" : "large"
            },

          'data' : [
             { "id" : "ajson1", "parent" : "#", "text" : "Simple root node" },
             { "id" : "ajson2", "parent" : "#", "text" : "Root node 2" },
             { "id" : "ajson3", "parent" : "ajson2", "text" : "Child 1" },
             { "id" : "ajson4", "parent" : "ajson2", "text" : "Child 2" },
          ]
          },
          "checkbox" : {
            "keep_selected_style" : false
          },
          "plugins" : [ "checkbox" ]
      });
 }







}