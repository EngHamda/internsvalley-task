(function($) {
    //getul,count child
    //second-list
    previousPageNumber = 0;//$("#first-list").find("li").length;
    currentPageNumber = previousPageNumber + 1 ;
    previousPageLinks = [];
    $("button").on( "click", function(event) {
        requestData = {'taskType': event.target.id,'currentPageNumber': currentPageNumber};
        if(previousPageNumber >= currentPageNumber){
            alert("No more links");
            return false;
        }
        if('simple-task'==event.target.id){
            listId = "#first-list";
            pId = "#first-error-msg";

        } else if ('advanced-task'==event.target.id) {
            listId = "#second-list";
            pId = "#second-error-msg";
        }
        $.ajax({
            url: "get-links.php", // Url to which the request is send
            type: "POST",
            dataType: "json",
            data: requestData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            success: function (data) {
                // A function to be called if request succeeds
                if (data.error === true){
                    $(listId).remove();
                    $(pId).empty().html(data.msg);
                    //exit from while
                    return false;
                } else{ 
                    if(previousPageNumber > 0 &&  JSON.stringify(previousPageLinks) === JSON.stringify(data.content.links) ){
                        previousPageNumber = currentPageNumber;
                    } else{
                        previousPageLinks = data.content.links;
                        previousPageNumber++;
                        currentPageNumber++;

                        list = '<ul>';
                        $.each(data.content.links, function( index, value ) {
                            list += "<li><a href='" + value + "'>" + value + "</a></li>";
                        });
                        list +='</ul>';
                        $(listId).append( "<h4>PageNumber "+data.content.currentPageNumber+"</h4>"+list );
                    }//endIF
                }//endIF success
            }//end success
        });//endAJAX
    });//endEvent
}(jQuery));
