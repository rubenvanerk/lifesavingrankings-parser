require('./bootstrap');

function highlight(walloftext, regex) {
    try {
        let x = new RegExp(eval(regex), 'g');
        return walloftext.replace(x, '<span class="highlight">$&</span>');
    } catch (e) {
        console.log('invalid regex');
        return walloftext;
    }
}

$('#regexTester button').click(function () {

    $('.highlight').replaceWith(function () {
        return $(this).html();
    })

    $('div.raw-data').html(function () {
        return highlight($(this).html(), $('#regexTester input').val());
    })
});
