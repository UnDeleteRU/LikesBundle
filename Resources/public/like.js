$(function () {
    var likeSubmit = false;

    $('a.like[href]').on('click', function (event) {
        event.preventDefault();

        if (likeSubmit) {
            return;
        }

        likeSubmit = true;
        var $like = $(this);

        $.ajax({
            'url': $(this).attr('href'),
            'complete': function (response) {
                likeSubmit = false;

                if (response.status == 403) {
                    $('body').trigger('needauth.like');
                    return;
                }

                if (response.status == 200) {
                    $like.find('span.count').text(response.responseJSON.count);

                    if (response.responseJSON.active) {
                        $like.addClass('active');
                        $like.trigger('like');
                    } else {
                        $like.removeClass('active');
                        $like.trigger('unlike');
                    }
                }
            }
        })
    });
});
