$(document).ready(function () {

    watchFileList([vaGotoNextStep, detectSaveButtonExist]);

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

    function watchFileList(callbacks) {
        var initialCount = $("#fileList tr").length;
        var interval = setInterval(function () {
            if (detectChildrenAdded('fileList', 'tr', initialCount) === true) {
                clearInterval(interval);
                for (var i = 0; i < callbacks.length; i++) {
                    callbacks[i]();
                }
            }
        }, 100);
    }

    function detectChildrenAdded(parentId, childElem, initialCount) {
        var currentCount = $("#" + parentId + " " + childElem).length;
        if (currentCount > initialCount) {
            return true;
        }
        return currentCount;
    }

    function detectCloseButtonExist() {
        detectElementExist('editor_close', [vaGotoNextStep, detectSaveButtonExist]);
    }

    function detectSaveButtonExist() {
        detectElementExist('editor_save', [vaGotoNextStep, detectSaveButtonClicked])
    }

    function detectElementExist(elemId, callbacks) {
        var interval = setInterval(function () {
            if ($("#" + elemId).length) {
                clearInterval(interval);
                for (var i = 0; i < callbacks.length; i++) {
                    callbacks[i]();
                }
            }
        }, 100);
    }

    function detectSaveButtonClicked(){
        $('#editor_save').on('click', function(){
            vaGotoNextStep();
            detectCloseButtonClicked();
        });
    }

    function detectCloseButtonClicked() {
        $('#editor_close').on('click', function () {
            vaEnd();
        });
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
            title: "1. Upload File",
            content: "Click here to upload a file",
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
            title: "2. Share & edit",
            content: "Mouseover and click 'share' to share this file, Click on the row to edit file",
            placement: 'bottom'
        },
        {
            element: "#editor_save",
            title: "3. Save",
            content: "Click to save the changes",
            placement: "buttom",
            onShown: function (tour) {
                var stepElement = getTourElement(tour);
                $(stepElement).after($('.tour-step-background'));
                $(stepElement).after($('.tour-backdrop'));
            }
        },
        {
            element: "#editor_close",
            title: "4. Close",
            content: "Click to close edit page and return to file list",
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

function vaEnd() {
    tour.end();
}