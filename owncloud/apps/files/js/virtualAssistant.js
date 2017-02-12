$(document).ready(function () {

    watchFileList([vaGotoNextStep, detectSaveButtonExist]);
    showVirtualAssistanceButton();

    if ($("#assistantCompleted").length) {
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
                "Never show this in the future": function () {
                    hideVirtualAssistance();
                    $(this).dialog("close");
                }
            }
        });
    } else {
        $("#virtualAssistant").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "I need assistance": function () {
                    $(this).dialog("close");
                    showVirtualAssistance();
                }
            }
        });
    }

    function showVirtualAssistanceButton(){
        $("#header").prepend("<div id='virtualAssistanceButton' onclick='showVirtualAssistance();'>Assistant</div>");
    }

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

var va = new Va({
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
            onShown: function (va) {
                var stepElement = getVaElement(va);
                $(stepElement).after($('.tour-step-background'));
                $(stepElement).after($('.tour-backdrop'));
            }
        },
        {
            element: "#fileList tr[data-type='file']:first-child",
            title: "2. Share & edit & delete",
            content: "Mouseover and click 'share' to share this file, Click on the row to edit file and click the cross in the end to delete this file",
            placement: 'bottom',
            template: getTemplate
        },
        {
            element: "#editor_save",
            title: "3. Save",
            content: "Click to save the changes",
            placement: "buttom",
            onShown: function (va) {
                var stepElement = getVaElement(va);
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
            onShown: function (va) {
                var stepElement = getVaElement(va);
                $(stepElement).after($('.tour-step-background'));
                $(stepElement).after($('.tour-backdrop'));
            },
            template: getTemplate,
        }
    ]
});

function showVirtualAssistance() {
    va.init();
    va.start();
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
        "</div>";
}

function getVaElement(va) {
    return va._options.steps[va._current].element
}

function vaGotoNextStep() {
    va.next();
}

function vaEnd() {
    va.end();
    var path = OC.filePath('files', 'ajax', 'completeVirtualAssistance.php')
    $.post(path, null, function () {

    });
}