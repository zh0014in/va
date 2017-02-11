$(document).ready(function () {

    watchFileList([vaGotoNextStep, detectSaveButtonExist]);

    var buttons = {
        "I need assistance": function () {
            $(this).dialog("close");
            showVirtualAssistance();
        }
    };
    if ($("#assistantCompleted").length) {
        buttons = {
            "I need assistance": function () {
                $(this).dialog("close");
                showVirtualAssistance();
            },
            "Never show this in the future": function () {
                hideVirtualAssistance();
                $(this).dialog("close");
            }
        }
    }
    $("#virtualAssistant").dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: buttons
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
        detectElementExist('editor_save', [vaGotoNextStep, detectSaveButtonClicked, detectCloseButtonClicked])
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

    function detectSaveButtonClicked() {
        $('#editor_save').on('click', function () {
            vaGotoNextStep();
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
    backdropPadding: 1,
    steps: [
        {
            element: "#va-upload",
            title: "1. Upload File",
            content: "Click here to upload a file",
            container: "body",
            placement: 'bottom',
            template: getTemplate,
            onShown: function (tour) {
                var stepElement = getTourElement(tour);
                $(stepElement).after($('.tour-step-background'));
                $(stepElement).after($('.tour-backdrop'));
            }
        },
        {
            element: "#fileList",
            title: "2. Share & edit",
            content: "Mouseover and click 'share' to share this file, Click on the row to edit file",
            placement: 'bottom',
            template: getTemplate
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
            },
            template: getTemplate,
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
            },
            template: getTemplate,
        }
    ]
});

function showVirtualAssistance() {
    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
}

function getTemplate(i, step) {
    return "<div class='popover tour'>" +
        "<div class='arrow' ></div>" +
        "<h3 class='popover-title'></h3>" +
        "<div class='popover-content'></div>" +
        "<div class='va-progress'>" +
        "<div class='va-progressbar' style='width:" + ((i + 1) * 100 / 4) + "%'>" +
        "</div>" +
        "</div>" +
        "<div class='popover-navigation'>" +
        "<button class='btn btn-default' data-role='prev'>« Prev</button>" +
        "<button class='btn btn-default' data-role='next'>Next »</button>" +
        "</div>" +
        "</div>";
}

function getTourElement(tour) {
    return tour._options.steps[tour._current].element
}

function vaGotoNextStep() {
    tour.next();
}

function vaEnd() {
    tour.end();
    var path = OC.filePath('files', 'ajax', 'completeVirtualAssistance.php')
    $.post(path, null, function () {

    });
}