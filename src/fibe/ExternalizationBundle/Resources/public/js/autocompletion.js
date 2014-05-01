function autoComplete(targetDiv,sourceArray) { 
 
    
//Pick autocomplete input value each keyup
    targetDiv.autocomplete({
        autoFocus: true,
        source: sourceArray, 
        minLength: 1, 
        select: function (event, ui) { 
            $("#fibe_bundle_wwwconfbundle__xpropertytype_xvalue").val(ui.item.uri).addClass("inputSuccess");
            $('#form_xvalue').val(ui.item.uri);
            $("#uriLink div").removeClass("warning").addClass("success");
            $("#submitLinkButton").removeAttr("disabled").removeClass("disabled") ;
        }
    });
 }
