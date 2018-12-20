/*
 * Show Subforum Images
 * phpBB Extension
 */

$(document).ready(function() {
    // Remove the subforums text that comes before the subforums
    $('.subforum').prev('strong').remove();

    // Remove the commas that separate the subforums
    $('.subforum').each(function() {
        var separatorText = $(this).get(0).nextSibling;
        if (separatorText.length < 3) {
            separatorText.remove();
        }
    });

    // Remove the existing subforums
    $('.subforum').remove();

    // Get the width of each forum image, and pad accordingly
    $('.list-inner .forum-image ~ .sfi-subforum-block').each(function() {
        var width = $(this).siblings('.forum-image').first().children('img').first().width();
        $(this).css('margin-left', (width - 20));
    });
});
