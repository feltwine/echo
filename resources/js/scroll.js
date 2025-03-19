import $ from 'jquery';

$(document).ready(function() {
    let page = 1;
    let loading = false;

    console.log("Load More script initialized");

    // Load more button click event
    $('#loadMoreButton').click(function() {
        console.log("Load More button clicked");
        if (!loading) {
            page++;
            loadMoreData(page);
        }
    });

    function loadMoreData(page) {
        loading = true;
        $.ajax({
            url: '/home?page=' + page,
            method: 'GET',
            beforeSend: function () {
                $('#loading').show();
                console.log("Loading posts...");
            },
            success: function (data) {
                console.log("Data received:", data);
                if (data.html.trim() === "") {
                    $('#loading').html('No more posts to load.');
                    $('#loadMoreButton').hide();
                    return;
                }
                $('#loading').hide();
                $('#postFeed').append(data.html);
                if (data.nextPage) {
                    page = new URL(data.nextPage).searchParams.get('page');
                }
                loading = false;
            },
            error: function (xhr) {
                $('#loading').html('An error occurred while loading more posts.');
                loading = false;
                console.log("Error:", xhr.responseText);
            }
        });
    }
});
