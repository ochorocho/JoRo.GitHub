function displayResponsiveList() {
    $('.ghList, .ghTile').each(function () {
        var elementsToShow = '';
        if(Foundation.utils.is_small_only()) {
            elementsToShow = $(this).attr('data-small');
        }
        if(Foundation.utils.is_medium_only()) {
            elementsToShow = $(this).attr('data-medium');
        }
        if(Foundation.utils.is_large_up()) {
            elementsToShow = $(this).attr('data-large');
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

    $('.show-all, .hide-all').click(function () {
        if(Foundation.utils.is_small_only()) {
            elementsToShow = $(this).parent().prev().attr('data-small');
        }
        if(Foundation.utils.is_medium_only()) {
            elementsToShow = $(this).parent().prev().attr('data-medium');
        }
        if(Foundation.utils.is_large_up()) {
            elementsToShow = $(this).parent().prev().attr('data-large');
        }
    });

    $('.show-all').click(function () {
        $(this).parent().prev().find('.item').each(function (el) {
            if($(this).is(":hidden")) {
                MotionUI.animateIn($(this), 'hinge-in-from-top');
                $(this).show();
            }
        });
        $(this).hide().next().show();
    });


    $('.hide-all').click(function () {
        $(this).parent().prev().find('.item').each(function (index, el) {
            if(index > elementsToShow) {
                MotionUI.animateOut($(this), 'hinge-out-from-top');
            }
        });
        $(this).hide().prev().show();
    });

});

// Throttled resize function
$(window).on('resize', Foundation.utils.throttle(function(e){
    displayResponsiveList();
}, 300));