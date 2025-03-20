import $ from 'jquery';

$(document).ready(function() {
    let page = 1;
    let loading = false;
    // Get the current URL path to determine which page we're on
    const currentPath = window.location.pathname;

    // Only initialize if we have the necessary elements
    if ($('#postFeed').length && $('#loadMoreButton').length) {
        console.log("Infinite scroll script initialized for: " + currentPath);

        // Handle the load more button click
        $('#loadMoreButton').click(function() {
            console.log("Load More button clicked");
            if (!loading) {
                page++;
                loadMoreData(page);
            }
        });
    }

    function loadMoreData(page) {
        loading = true;

        // Determine the correct container based on the page
        let contentContainer = $('#postFeed');

        // Keep the current query parameters (if any) and add/update the page parameter
        let url = new URL(window.location.href);
        url.searchParams.set('page', page);

        $.ajax({
            url: url.toString(),
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Mark as AJAX request
            },
            beforeSend: function () {
                $('#loading').show();
                $('#loadMoreButton').prop('disabled', true);
                console.log("Loading content...");
            },
            success: function (data) {
                console.log("Data received:", data);

                // Check if we received HTML directly or as part of a JSON response
                let htmlContent = '';
                let nextPageUrl = null;
                let hasMoreContent = true;

                if (typeof data === 'object' && data.html) {
                    // JSON response with HTML property (from AJAX calls)
                    htmlContent = data.html;
                    nextPageUrl = data.nextPage;
                    hasMoreContent = !!nextPageUrl;
                } else {
                    // Direct HTML response (unlikely but handling as fallback)
                    htmlContent = data;
                }

                if (htmlContent.trim() === "") {
                    $('#loading').html('No more content to load.');
                    $('#loadMoreButton').hide();
                    return;
                }

                $('#loading').hide();
                contentContainer.append(htmlContent);

                // Reset loading state and button
                loading = false;
                $('#loadMoreButton').prop('disabled', false);

                // Hide button if no more content
                if (!hasMoreContent) {
                    $('#loadMoreButton').hide();
                    $('#loading').html('All content loaded.');
                }
            },
            error: function (xhr) {
                $('#loading').html('An error occurred while loading more content.');
                $('#loadMoreButton').prop('disabled', false);
                loading = false;
                console.log("Error:", xhr.responseText);
            }
        });
    }
});
