$(document).ready(function(){
   $("#abas").tabs();
   
   var treeData = [
    {title: "Matemática", isFolder: true, key: "1",
      children: [
        {title: "Aritimética", isFolder: true, key: "1-1",
          children: [
            {title: "Básica", key: "1-1-1" },
            {title: "Avançada", key: "1-1-2" }
          ]
        },
        {title: "Aritimética", isFolder: true, key: "1-2",
          children: [
            {title: "Básica", key: "1-2-1" },
            {title: "Avançada", key: "1-2-2" }
          ]
        }
      ]
    }
  ];
  
   $("#arvoreAssuntos").dynatree({
      checkbox: true,
      selectMode: 3,
      children: treeData,
      onSelect: function(select, node) {
        // Get a list of all selected nodes, and convert to a key array:
        var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
          return node.data.key;
        });
        //console.log(selKeys);
        console.log("T1 - " + selKeys.join(","));
      },
      onDblClick: function(node, event) {
        node.toggleSelect();
      },
      onKeydown: function(node, event) {
        if( event.which == 32 ) {
          node.toggleSelect();
          return false;
        }
      },
      // The following options are only required, if we have more than one tree on one page:
//        initId: "treeData",
      cookieId: "dynatree-Cb3",
      idPrefix: "dynatree-Cb3-"
    });
});