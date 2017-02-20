function displayResponsiveList() {
    $('.ghList, .ghTile').each(function () {
        if(Foundation.utils.is_small_only()) {
            var elementsToShow = $(this).attr('data-small');
        }
        if(Foundation.utils.is_medium_only()) {
            var elementsToShow = $(this).attr('data-medium');
        }
        if(Foundation.utils.is_large_up()) {
            var elementsToShow = $(this).attr('data-large');
        }

        $(this).find('.item').each(function(index, el) {
            if(index <= elementsToShow) {
                $(el).show();
            } else {
                $(el).hide();
            }
        });
    });
}

$(document).ready(function () {
    displayResponsiveList();

    var elementsToShow = '';

    $('.showAll, .hideAll').click(function () {
        if(Foundation.utils.is_small_only()) {
            elementsToShow = $(this).parent().prev().attr('data-small');
        }
        if(Foundation.utils.is_medium_only()) {
            elementsToShow = $(this).parent().prev().attr('data-medium');
        }
        if(Foundation.utils.is_large_up()) {
            elementsToShow = $(this).parent().prev().attr('data-large');
        }
        console.log(elementsToShow);
    });

    $('.showAll').click(function () {
        $(this).parent().prev().find('.item').each(function (el) {
            if($(this).is(":hidden")) {
                MotionUI.animateIn($(this), 'hinge-in-from-top');
                $(this).show();
            }
        });
    });

    $('.hideAll').click(function () {
        $(this).parent().prev().find('.item').each(function (index, el) {
            if(index > elementsToShow) {
                console.log(index + ' > ' + elementsToShow);
                MotionUI.animateOut($(this), 'hinge-in-from-top');
                //$(this).hide();
            }
        });
    });

});

// Throttled resize function
$(window).on('resize', Foundation.utils.throttle(function(e){
    displayResponsiveList();
}, 300));