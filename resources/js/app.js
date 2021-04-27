require('./bootstrap');

let toast = $('#liveToast');
toast.toast({'delay': 5000})

function showToast(message) {
    toast.find('.toast-body').text(message);
    toast.toast('show');
}

function getRegexFlags(regex) {
    let flags = regex.match('(?<=\\/).$');
    if (Array.isArray(flags)) {
        return 'g' + flags.shift();
    } else {
        return 'g';
    }
}

function highlight(rawData, regex) {
    try {
        let flags = getRegexFlags(regex);
        console.log(flags);
        let regExp = new RegExp(eval(regex), flags);
        let matches = rawData.match(regExp);
        if (Array.isArray(matches)) {
            let matchCount = rawData.match(regExp).length;
            if (matchCount > 10000) {
                $('.toast').toast();
                showToast('Too many matches: ' + rawData.match(regExp).length);
                return rawData;
            } else {
                showToast(matchCount + ' matches');
            }
        } else {
            showToast('No matches');
        }
        return rawData.replace(regExp, '<mark>$&</mark>');
    } catch (e) {
        showToast(e.message);
        return rawData;
    }
}

$('input, select').on('keyup change', function () {
    toast.toast('dispose');
    let regex = $(this).val();

    $('mark').replaceWith(function () {
        return $(this).html();
    })

    $('div.raw-data').html(function () {
        return highlight($(this).html(), regex);
    })
});
