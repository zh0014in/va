$(document).ready(function () {

    watchFileList([vaGotoNextStep, detectCloseButtonExist]);

    $("#virtualAssistant").dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "I need assistance": function () {
                $(this).dialog("close");
                showVirtualAssistance();
            },
            Cancel: function () {
                hideVirtualAssistance();
                $(this).dialog("close");
            }
        }
    });

    function hideVirtualAssistance() {
        var path = OC.filePath('files', 'ajax', 'hideVirtualAssistance.php')
        $.post(path, null, function () {

        });
    }

    function watchFileList(callbacks){
        var initialCount = $("#fileList tr").length;
        var interval = setInterval(function(){
            if(detectChildrenAdded('fileList', 'tr', initialCount) === true){
                clearInterval(interval);
                for(var i = 0; i < callbacks.length; i++){
                    callbacks[i]();
                }
            }
        },100);
    }

    function detectChildrenAdded(parentId, childElem, initialCount){
        var currentCount = $("#"+parentId + " " + childElem).length;
        if(currentCount > initialCount){
            return true;
        }
        return currentCount;
    }

    function detectCloseButtonExist(){
        detectElementExist('editor_close', [vaGotoNextStep, detectCloseButtonClicked]);
    }

    function detectElementExist(elemId, callbacks){
        var interval = setInterval(function(){
            if($("#"+elemId).length){
                clearInterval(interval);
                for(var i = 0; i < callbacks.length; i++){
                    callbacks[i]();
                }
            }
        },100);
    }

    function detectCloseButtonDisappears(){
        detectElementDisappears('editor_close', vaEnd);
    }

    function detectCloseButtonClicked(){
        // $('#editor_close').on('click', function(){
        //     vaEnd();
        // });
    }

    function detectElementDisappears(elemId, callback){
        var interval = setInterval(function(){
            if(!$('#'+elemId).length){
                clearInterval(interval);
                callback();
            }
        },100);
    }
});

// Instance the tour
var tour = new Tour({
    name: 'va',
    backdrop: true,
    storage: false,
    backdropPadding: 1
});

function showVirtualAssistance() {

    tour.addSteps([
        {
            element: "#va-upload",
            title: "Title of my step",
            content: "Content of my step",
            container: "body",
            placement: 'bottom',
            onShown: function (tour) {
                var stepElement = getTourElement(tour);
                $(stepElement).after($('.tour-step-background'));
                $(stepElement).after($('.tour-backdrop'));
            }
        },
        {
            element: "#fileList tr:first-child",
            title: "Title of my step",
            content: "Content of my step",
            placement: 'bottom'
        },
        {
            element: "#editor_close",
            title: "Click to close",
            content: "Click to return to list",
            placement: "buttom",
            onShown: function (tour) {
                var stepElement = getTourElement(tour);
                $(stepElement).after($('.tour-step-background'));
                $(stepElement).after($('.tour-backdrop'));
            }
        }
    ]);

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
}

function getTourElement(tour) {
    return tour._options.steps[tour._current].element
}

function vaGotoNextStep() {
    tour.next();
}

function vaEnd(){
    tour.end();
}