
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('bootstrap-table');
require('bootstrap-datepicker');

$('#vote_organisations').dblclick(function() {
    $('#vote_organisations option:selected').remove().appendTo($('#votefor'));
    checkVoteSize();
});

$('#votefor').dblclick(function(){
    $('#votefor option:selected').remove().appendTo($('#vote_organisations'));
    checkVoteSize();
});

$('#js-add-org').click(function() {
    $('#vote_organisations option:selected').remove().appendTo($('#votefor'));
    checkVoteSize();
});

$('#js-remove-org').click(function() {
    $('#votefor option:selected').remove().appendTo($('#vote_organisations'));
    checkVoteSize();
});

function checkVoteSize() {
    if ($('#votefor option').length == 14) {
        $('#vote_organisations').attr('disabled', true);
    } else {
        $('#vote_organisations').attr('disabled', false);
    }

    if ($('#votefor option').length < 1) {
        $('#votebtn').attr('disabled', true);
    } else {
        $('#votebtn').attr('disabled', false);
    }
}

$('#filter_org').on('keyup', function() {
    var input = document.getElementById('filter_org').value.toLowerCase();
    var output = document.getElementById('vote_organisations').options;

    for (var i = 0; i < output.length; i++) {
        if (output[i].text.toLowerCase().indexOf(input) < 0) {
            output[i].style.display = "none";
            output[i].setAttribute('style', 'display:none');
        } else {
            output[i].style.display = "";
            output[i].setAttribute('style', 'display:');
        }
    }
});

$('.js-drop-filter').on('change', function() {
    $('.js-drop-filter').closest('form').submit();
});

$('.js-search').on('keydown', function(e) {
    if (e.which == 13) {
        $('.js-search').closest('form').submit();
    }
});

$('[name="is_candidate"].checkbox-ams').on('change', function() {
    $('.for_org_candidates').toggle($(this).is(':checked'));
});

$(document).on('click', '.js-file-upl', function (e) {
    e.preventDefault();

    var input = $(this).parent().prev().find('.js-file-input');

    input.trigger('click');
});

$(document).on('click', '.js-plus-file-upl', function (e) {
    e.preventDefault();

    var html = $(this).parent().parent().prev().prop('outerHTML');
    $('.plus-file-container').before(html);
});

$('.js-showTerms').on('click', function() {
    $('#info').modal('show');
});
