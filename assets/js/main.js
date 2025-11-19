$(function () {
    $('a[data-scroll]').on('click', function (e) {
        e.preventDefault();
        const alvo = $($(this).attr('href'));
        if (alvo.length) {
            $('html, body').animate({ scrollTop: alvo.offset().top - 80 }, 600);
        }
    });
});
