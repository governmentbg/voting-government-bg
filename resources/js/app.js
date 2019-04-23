
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

$(document).on('change', '.js-file-input', function (e) {
    for (var i = 0; i < $('[data-consec-num]').length; i++) {
        var $el= $('[data-consec-num]').eq(i);

        if ($el.val() != '') {
            $el.prev('span').text($el.val().split('\\').pop());
        }
    }
});

$(document).on('click', '.js-plus-file-upl', function (e) {
    e.preventDefault();

    var html = $(this).parent().parent().prev().prop('outerHTML');

    $('.plus-file-container').before(html);

    for (var i = 0; i < $('[data-consec-num]').length; i++) {
        $el = $('[data-consec-num]').eq(i);
        $el.attr('data-consec-num', i);

        if ($el.val() != '') {
            $el.prev('span').text($el.val().split('\\').pop());
        } else {
            $el.prev('span').text('Select file');
        }
    }
});

$('.js-showTerms').on('click', function() {
    $('#info').modal('show');
});

var $th = $('.tableFixHead').find('thead th')
$('.tableFixHead').on('scroll', function() {
    $th.css('transform', 'translateY('+ this.scrollTop + 'px)');
    $th.css('background-color', '#3e7ea9');
});

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

$(document).ready(function() {
    if ($('.flash-message').length) {
        $('.flash-message').fadeOut(10000);
    }
});

$('#refresh').click(function(){
    $.ajax({
       type:'GET',
       url:'refreshcaptcha',
       success:function(data){
          $(".captcha span").html(data);
       }
    });
});

$('.js-focusout-submit').on('focusout', function() {
    $(this).closest('form').submit();
});

$('.js-change-submit').on('change', function() {
    $(this).closest('form').submit();
});
