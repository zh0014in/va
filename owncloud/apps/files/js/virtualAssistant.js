$(document).ready(function () {

// var observeDOM = (function(){
//     var MutationObserver = window.MutationObserver || window.WebKitMutationObserver,
//         eventListenerSupported = window.addEventListener;

//     return function(obj, callback){
//         if( MutationObserver ){
//             // define a new observer
//             var obs = new MutationObserver(function(mutations, observer){
//                 if( mutations[0].addedNodes.length )// only when node is added
//                     callback();
//             });
//             // have the observer observe foo for changes in children
//             obs.observe( obj, { childList:true, subtree:true });
//         }
//         else if( eventListenerSupported ){
            
//             obj.addEventListener('DOMNodeInserted', callback, false);
//             obj.addEventListener('DOMNodeRemoved', callback, false);
//         }
//     }
// })();

// // Observe a specific DOM element:
// observeDOM( document.getElementById('fileList') ,function(){ 
//     console.log('file list changed');
// });
    watchFileList(vaGotoNextStep);

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

    function watchFileList(callback){
        var initialCount = $("#fileList tr").length;
        var interval = setInterval(function(){
            if(detectChildrenAdded('fileList', 'tr', initialCount) === true){
                clearInterval(interval);
                callback();
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
            onShown: function (tour) {
                var stepElement = getTourElement(tour);
                $(stepElement).after($('.tour-step-background'));
                $(stepElement).after($('.tour-backdrop'));
            },
            placement: 'bottom'
        },
        {
            element: "#fileList tr:first-child",
            title: "Title of my step",
            content: "Content of my step",
            placement: 'bottom'
        }
    ]);

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();


    // $("#va-upload").tooltip({
    //     content: "<div>" +
    //     "<p>Click here to upload</p>" +
    //     "<div class='va-progress'>" +
    //     "<div class='va-progressbar' style='width:20%'></div>" +
    //     "</div>" +
    //     "<a href='javascript:;' onclick='hideTooltip();'>ok</a>" +
    //     "</div>",
    //     position: {
    //         using: function (position, feedback) {
    //             $(this).css(position);
    //             $("<div>")
    //                 .addClass("arrow")
    //                 .addClass(feedback.vertical)
    //                 .addClass(feedback.horizontal)
    //                 .appendTo(this);
    //         }
    //     },
    //     open: function (event, ui) {
    //         $("<div>")
    //             .attr("id","va-backdrop")
    //             .addClass("va-backdrop")
    //             .appendTo('body');
    //     }
    // });

    // $("#va-upload").tooltip().mouseover();
}

function getTourElement(tour) {
    return tour._options.steps[tour._current].element
}

function hideTooltip() {
    $("#va-upload").tooltip("close");
    $("$va-backdrop").remove();
}

function vaGotoNextStep() {
    alert("next");
    tour.next();
}