function WssaveModel(form, url) {
    var fd = new FormData();

    var $inputs = $( "#"+form+" :input");
    $inputs.each(function() {
        if (this.type == "file") {
            var file = $(this).prop('files')[0];
            fd.append(this.name, file);
        } else {
            fd.append(this.name, $(this).val());
        }
    });

    $.ajax({
        type: "POST",
        url: url,
        contentType: false,
        processData: false,
        data: fd,
        error: function (request, status, error) {
            alert(request.responseText);
        },
        success:function(data) {
        },
    }).done(function(result) {
        $("#"+form).find("div#form_status").text("Values are saved into model.");
        // TODO: replace this with ajax call that will only reload grid that was
        // changed
        window.setTimeout(function(){location.reload();},100);
    }).fail(function() {
        alert("Sorry. Server unavailable.");
    });

    return false;
}


function WsdeleteModelID(form_id, model, id, url, title, yes_text, no_text)
{
    UIkit.modal.confirm(
            "Delete item: <strong>" + id +"</strong> from <strong>" + model + "</strong>?",
            function(){
        $.ajax({
            type: "POST",
            url: url,
            data: {
                model: model,
                id: id
            },
            error: function (request, status, error) {
                window.setTimeout(function(){location.reload();},100);
            },
            cache: false,
            dataType: "text xml"
        }).done(function(result) {
            window.setTimeout(function(){location.reload();},100);
        });
    });
}


function WseditModelID(form_id, model, id, url)
{
    $.ajax({
        type: "POST",
        url: url,
        data: {
            form_id: form_id,
            model: model,
            id: id
        },
        error: function (request, status, error) {
            alert(request.responseText);
        },
        cache: false
    }).done(function(result) {
        $("#"+form_id).html(result);
    }).fail(function() {
        alert("Sorry. Server unavailable.");
    });
}


function WschangeModelPagination(
    id,
    action,
    model_name,
    noDataText,
    itemsPerPage,
    showEdit,
    order,
    formId,
    pageId,
    aId,
    edit_action,
    delete_action
)
{
    if(!$("#search_"+id).val()) {
        var search_string = '';
    } else {
        var search_string = $("#search_"+id).val();
    }

    $.ajax({
        type: "POST",
        url: action,
        data: {
            gridId : id,
            model: model_name,
            noDataText: noDataText,
            itemsPerPage: itemsPerPage,
            pageId: pageId,
            showEdit: showEdit,
            order: order,
            formId: formId,
            searchStr: search_string,
            editAction: edit_action,
            deleteAction: delete_action
        },
        cache: false
    }).done(function(result) {
        $(".flash").hide();
        $(".pagination a").removeClass("active") ;
        $("#"+aId).addClass("active");
        $("#"+id).empty().append(result);
        $var = pageId;
    }).fail(function()  {
        alert("Sorry. Server unavailable. ");
    });
};


function randomColorFactor() {
    return Math.round(Math.random() * 255);
};
function randomColor(opacity) {
    return "#"
        + randomColorFactor().toString(16)
        + randomColorFactor().toString(16)
        + randomColorFactor().toString(16);

    //
    //return 'rgba(' + randomColorFactor()
    //   + ',' + randomColorFactor()
    //    + ',' + randomColorFactor()
    //    + (opacity || '.3') + ');';
    //    */
};


function WsViewThumbnail(imageurl) {
    $("#ws_image_preview").dialog({
        autoOpen: false,
        show: {
            effect: "blind",
            duration: 300
        },
        hide: {
            effect: "explode",
            duration: 300
        }
    });

    var img = "<img src='" + imageurl + "' />";
    var div = document.getElementById("ws_image_preview");
    div.innerHTML = img;

    $("#ws_image_preview").dialog("open");
}
