var imp = imp || {};
imp.profile = imp.profile || {};
imp.profile.index = imp.profile.index || {};

(function() {
    var p = imp.profile.index;
    
    // User-specific properties defined in view
    p.userId = p.userId || 0;
    
    // Last ID fetched for the feed
    p.lastInteractionId = p.lastInteractionId || 0;
    
    // This is the page container that should scope this page
    p.container = '';
    
    /**
     * Sets up key elements on the UI
     */
    p.setup = function(container) {
        // Reset the last interaction ID
        p.lastInteractionId = 0;
        p.container = container;
        $('#' + p.container + ' .interaction-feed').empty();
        
        p.fetchAndLoadInteractions();
        p.attachListeners();
    };
    
    /**
     * Attaches listeners to key elements on the UI
     */
    p.attachListeners = function() {
        // Load more interactions when clicking the View More link
        $('#' + p.container + ' .view-more a').click(function(evt) {
            p.fetchAndLoadInteractions();
        });
        
        // Listen for when the user starts typing in a student name to search for
        $('#' + p.container + ' .search-listview').on('listviewbeforefilter', function(e, data) {
            var $ul = $(this),
                $input = $(data.input),
                value = $input.val(),
                html = '';
            $ul.html('');
            if (value && value.length > 1) {
                $ul.html('<li><div class="ui-loader"><span class="ui-icon ui-icon-loading"></span></div></li>');
                $ul.listview('refresh');
                $.ajax({
                    url: '/home/search',
                    type: 'POST',
                    data: {
                        q: value
                    },
                    dataType: 'json'
                }).done(function(data) {
                    var users = data.users,
                        families = data.families,
                        regex = new RegExp('(' + value.toLowerCase() + ')', 'i');
                    
                    // Clear the result list
                    $ul.empty();
                    
                    // Insert each family result into the list
                    $.each(families, function(i, family) {
                        var li = $('<li>'),
                            familyLink = $('<a>', {
                                'href': '/home/index/familyid/' + family.familyid,
                                'data-family-id': family.familyid,
                                'data-family-name': family.familyname,
                                'data-ajax': 'false'
                            }).html('<img src="/assets/img/family_42x42.png" class="mentor-profile-img"></i>' + family.familyname.replace(regex, "<strong>$1</strong>") + '<br /><span class="additional-details"><span class="role">Student</span> | ' + family.housename + ' House</span>').appendTo(li);
                        
                        $ul.append(li);
                    });
                    
                    // Insert each user result into the list
                    $.each(users, function(i, user) {
                        var li = $('<li>'),
                            userLink = $('<a>', {
                                'href': '/profile/index/id/' + user.userid,
                                'data-user-id': user.userid,
                                'data-user-name': user.fullname,
                                'data-ajax': 'false'
                            }).html('<img src="' + user.img_url + '?sz=42" class="mentor-profile-img" />' + user.fullname.replace(regex, "<strong>$1</strong>") + '<br /><span class="additional-details"><span class="role">Mentor</span> | ' + user.housename + ' House</span>').appendTo(li);
                        
                        $ul.append(li);
                    });
                    
                    // Refresh the list
                    $ul.listview('refresh');
                    $ul.trigger('updatelayout');
                });
            }
        });
    };
    
    /**
     * Fetches and loads interactions onto the page
     */
    p.fetchAndLoadInteractions = function() {
        var url = '/home/getinteractions',
            headers = {
                'Cache-Control': 'no-store, no-cache, must-revalidate'
            };
        
        // Run an AJAX request to fetch recent interactions
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                limit: 20,
                lastid: p.lastInteractionId,
                userid: p.mentorId
            },
            dataType: 'json',
            headers: headers
        }).done(function(data) {
            var interactions = data.interactions;
            
            // Iterate through all interactions and append them to the feed
            for (var i = 0; i < interactions.length; i++) {
                $('#' + p.container + ' .interaction-feed').append(p.buildFeedItem(interactions[i]));
            }
            
            // Store the ID of the last interaction fetched
            if (interactions.length > 0) {
                p.lastInteractionId = interactions[interactions.length - 1].id;
            } else {
                // Indicate that there are no more interactions to load
                $('#' + p.container + ' .view-more a').html('There are no more interactions to show.');
                $('#' + p.container + ' .view-more a').off('click');
            }
        });
    };
    
    /**
     * Builds and returns a DOM element for the feed item
     * @param interaction Array An array returned by an Ajax call to fetch interactions
     */
    p.buildFeedItem = function(interaction) {
        var container = $('<div>', {'class': 'interaction-feed-item', 'data-interaction-id': interaction['id']});
        container.append(p.buildFeedItemCore(interaction));
        container.append(p.buildFeedItemResponses(interaction));
        return container;
    };
    
    /**
     * Helper method for building core UI elements in the feed item
     */
    p.buildFeedItemCore = function(interaction) {
        var interactionDate = interaction.dateinteracted_formatted,
            postedDate = interaction.dateposted_formatted,
            core = $('<div>', {'class': 'interaction-feed-item-core'}),
            details = $('<div>', {'class': 'interaction-feed-item-details'}),
            feedback = $('<div>', {'class': 'nxj-grid interaction-feed-item-feedback'}),
            likeContainer = $('<div>', {'class': 'nxj-unit size1of4 text-center like'}),
            likeButton,
            commentContainer = $('<div>', {'class': 'nxj-unit size1of4 text-center comment'}),
            commentButton,
            description = interaction['description'],
            descriptionMarkup = $('<div>'),
            continueReadingLink;
        
        // Truncating the description length if it exceeds 200 characters
        if (description.length > 200) {
            continueReadingLink = $('<a>', {
                'data-interaction-id': interaction['id'],
                'href': '#'
            }).html('...Continue Reading');
            continueReadingLink.click(function(evt) {
                $(this).hide();
                $('#' + p.container + ' span[data-interaction-id="' + interaction['id'] + '"]').show();
            });
            
            descriptionMarkup.append(description.substring(0, 200));
            descriptionMarkup.append(continueReadingLink);
            descriptionMarkup.append('<span data-interaction-id="' + interaction['id'] + '" style="display: none;">' + description.substring(200, description.length) + '</span>');
        } else {
            descriptionMarkup.append(description);
        }
        
        // Build out the details section
        details.append('<div class="interaction-mentor"><div class="mentor-profile-image"><a href="/profile/index/id/' + interaction['user']['id'] + '"><img src="' + interaction['user']['imgurl'] + '?sz=50" /></a></div><div class="mentor-details"><a href="/profile/index/id/' + interaction['user']['id'] + '"><h2 class="mentor-name">' + interaction['user']['fname'] + ' ' + interaction['user']['lname'] + '</h2></a><div class="nxj-grid interaction-family"><div class="nxj-unit size3of5 interaction-family-detail"><i class="icon-sm icon-family"></i> <div class="detail-wrapper"><p class="family-name">' + interaction['family']['name'] + '</p><p class="house-name">' + interaction['house']['name'] + ' House</p></div></div><div class="nxj-unit size2of5 interaction-family-detail"><i class="icon-sm icon-duration"></i><div class="detail-wrapper"><p class="duration">' + interaction['duration'] + '</p><p class="minutes">minute' + (interaction['duration'] == 1 ? '' : 's') + '</p></div></div></div></div><div class="clear"></div></div>');
        details.append($('<div>', {
            'class': 'interaction-description'
        }).append(descriptionMarkup));
        
        // Build out the feedback items (i.e. Like/Comment)
        // Interaction Date
        feedback.append('<div class="nxj-unit size1of2 date"><div>Interaction: ' + interactionDate + '</div><div class="subdued">Posted: ' + postedDate + '</div>');
        
        // Like
        likeButton = $('<a href="#" data-interaction-id="' + interaction['id'] + '"><i class="icon-sm icon-like ' + (interaction['hasliked'] ? 'active' : '') + '"></i></a>');
        likeButton.click(function(evt) {
            var likeButton = $(this),
                interactionId = $(this).attr('data-interaction-id'),
                isLiked = $('.icon-like', likeButton).hasClass('active'),
                likeSection = $('#' + p.container + ' .response-likes[data-interaction-id="' + interactionId + '"]'),
                likeCount = parseInt($('#' + p.container + ' .response-likes[data-interaction-id="' + interactionId + '"] .like-count').html());;
            
            if (!isLiked) {
                // Submit a Like for this interaction
                $.ajax({
                    url: '/home/reply',
                    type: 'POST',
                    data: {
                        interactionid: interactionId,
                        islike: 1,
                        likestate: 1,
                        commenttext: ''
                    },
                    dataType: 'json'
                });
                
                // Increment the number of Likes for the interaction
                $('#' + p.container + ' .response-likes[data-interaction-id="' + interactionId + '"] .like-count').html(likeCount + 1 + '');
                $('#' + p.container + ' .response-likes[data-interaction-id="' + interactionId + '"] .like-word').html((likeCount + 1) == 1 ? 'Like' : 'Likes');
                
                // If the Likes box was originally hidden, unhide it
                if (!likeSection.is(':visible')) {
                    likeSection.show();
                }
                
                // Update the status of the button
                $('.icon-like', likeButton).addClass('active');
            } else {
                // Unlike this interaction
                $.ajax({
                    url: '/home/reply',
                    type: 'POST',
                    data: {
                        interactionid: interactionId,
                        islike: 1,
                        likestate: 0,
                        commenttext: ''
                    },
                    dataType: 'json'
                });
                
                // Decrement the number of Likes for the interaction
                $('#' + p.container + ' .response-likes[data-interaction-id="' + interactionId + '"] .like-count').html(likeCount - 1 + '');
                $('#' + p.container + ' .response-likes[data-interaction-id="' + interactionId + '"] .like-word').html((likeCount - 1) == 1 ? 'Like' : 'Likes');
                
                // If the Likes box was originally shown, hide it (if necessary)
                if ((likeCount - 1) <= 0 && likeSection.is(':visible')) {
                    likeSection.hide();
                }
                
                // Update the status of the button
                $('.icon-like', likeButton).removeClass('active');
            }
        });
        likeContainer.append(likeButton);
        feedback.append(likeContainer);
        
        // Comment
        commentButton = $('<a href="#"><i class="icon-sm icon-comment' + (!interaction['commentsenabled'] ? ' disabled' : '') + '"></i> <strong class="comment-count" data-interaction-id="' + interaction['id'] + '">' + (interaction['commentscount'] > 0 ? interaction['commentscount'] : '') + '</strong></a>');
        commentButton.click(function(evt) {
            if (!interaction.commentsenabled) {
                // If the user cannot comment on this interaction, let them know
                $('#' + p.container + ' .error-popup p').html('You can only comment on interactions within your own house')
                $('#' + p.container + ' .error-popup').popup('open');
            } else {
                $('#' + p.container + ' .comment-dialog').on('popupbeforeposition', function(evt, ui) {
                    // Reset the dialog
                    $('.comments', $('#' + p.container + ' .comment-dialog')).empty();
                    $('#post-button', $('#' + p.container + ' .comment-dialog')).off('click');
                    
                    // Prepare the dialog box with details about this interaction
                    $('.comments', $('#' + p.container + ' .comment-dialog')).append(p.buildFeedItemCommentSection(interaction, false));
                    
                    // Attach a listener to the Post button to post a comment for this interaction
                    $('#post-button', $('#' + p.container + ' .comment-dialog')).click(function(evt) {
                        var comment = $('#comment', $('#' + p.container + ' .comment-dialog')).val(),
                            interactionId = interaction['id'];
                        
                        if (!comment) {
                            return;
                        }
                        
                        // Run an ajax call to post the comment
                        $.ajax({
                            url: '/home/reply',
                            type: 'POST',
                            data: {
                                interactionid: interactionId,
                                islike: 0,
                                likestate: 1,
                                commenttext: comment
                            },
                            dataType: 'json'
                        }).done(function(data) {
                            $.mobile.loading('hide');
                            var comment = data.reply,
                                commentCount = parseInt($('#' + p.container + ' .comment-count[data-interaction-id="' + interactionId + '"]').html());
                            
                            // Error handling for non-numbers
                            commentCount = isNaN(commentCount) ? 0 : commentCount;
                            
                            // Insert the comment into the feed
                            $('#' + p.container + ' .interaction-feed-item[data-interaction-id="' + interactionId + '"] .response-comments').append(p.buildFeedItemComment(comment, false));
                            
                            // Increment the comment count
                            $('#' + p.container + ' .comment-count[data-interaction-id="' + interactionId + '"]').html(commentCount + 1);
                            
                            // Close the popup
                            $('#' + p.container + ' .comment-dialog').popup('close');

                            // Reset textarea in popup
                            $('#comment', $('#' + p.container + ' .comment-dialog')).val('');
                            $('#comment', $('#' + p.container + ' .comment-dialog')).css('height', '50px');
                        });
                        $.mobile.loading( 'show', {
                            text: '',
                            textVisible: false,
                            theme: 'z',
                            html: ""
                        });
                    });
                });
                $('#' + p.container + ' .comment-dialog').popup('open');
            }
        });
        commentContainer.append(commentButton);
        feedback.append(commentContainer);
        
        // Append the sections to the core and return the core
        core.append(details);
        core.append(feedback);
        return core;
    };
    
    /**
     * Helper method for building feedback UI elements in the feed item
     */
    p.buildFeedItemResponses = function(interaction) {
        var likeCount = interaction['likecount'],
            responses = $('<div>', {'class': 'interaction-feed-item-responses'}),
            likeSection = $('<div>', {'class': 'response-likes', 'data-interaction-id': interaction['id']}),
            likesLink;
        
        // Build out the Likes section
        likesLink = $('<a href="#"><strong class="like-count">' + likeCount + '</strong> <strong class="like-word">Like' + (likeCount == 1 ? '' : 's') + '</strong></a>');
        likesLink.click(function (evt) {
            // When clicking on the number of likes, populate and open a popup dialog with who had liked the interaction
            $('#' + p.container + ' .likes-dialog').on('popupbeforeposition', function(evt, ui) {
                // Reset the dialog
                $('.response-likes', $('#' + p.container + ' .likes-dialog')).empty();
                
                // Update the number of Likes in the title bar
                $('#' + p.container + ' .likes-dialog .like-count').html(likeCount);
                $('#' + p.container + ' .likes-dialog .like-word').html(likeCount == 1 ? 'Like' : 'Likes');
                $.each(interaction.likes, function(i, like) {
                    // Create a wrapper for each like item
                    var likeDetails = $('<div>', {
                        'class': 'response-like'
                    });
                    
                    // Append the user's profile image
                    likeDetails.append('<div class="like-img"><a href="/profile/index/id/' + like['user']['id'] + '"><img src="' + like['user']['imgurl'] + '?sz=40" /></a></div>');
                    
                    // Append details about the user
                    likeDetails.append('<div class="like-body"><a href="/profile/index/id/' + like['user']['id'] + '"><h3 class="mentor-name">' + like['user']['fname'] + ' ' + like['user']['lname'] + '</h3></a><div class="like-text"></div><div class="like-date">' + p.formatDate(new Date(like.datetimets_sec * 1000)) + '</div></div><div class="clear"></div>');
                    
                    // Insert the entry into the popup
                    $('#' + p.container + ' .likes-dialog .response-likes').append(likeDetails);
                });
            });
            $('#' + p.container + ' .likes-dialog').popup('open');
        });
        likeSection.append(likesLink);
        if (likeCount <= 0) {
            likeSection.hide();
        }
        
        // Append the sections to the responses and return the respones
        responses.append(likeSection);
        responses.append(p.buildFeedItemCommentSection(interaction, true));
        return responses;
    };
    
    /**
     * Helper method for building the comment section
     */
    p.buildFeedItemCommentSection = function(interaction, showLink) {
        var comments = interaction['comments'],
            commentSection = $('<div>', {'class': 'response-comments'}),
            viewCommentsLnk;

        // Build out the Comments section
        if (comments.length > 3 && showLink) {
            // Create a link to allow the user to view previous comments (hidden on load)
            viewCommentsLnk = $('<a>', {'class': 'view-comments-lnk ui-link', 'text' : 'View ' + (comments.length - 3) + ' previous comment' + (comments.length - 3 == 1 ? '' : 's'), 'href' : '#'});
            viewCommentsLnk.click(function() {
                $('.response-comment[data-interaction-id="' + interaction['id'] + '"]').show();
                $(this).hide();
            });
            
            // Insert an additional entry to view previous comments
            commentSection.append($('<div>', {
                'class': 'response-comment'
            }).append($('<div>', {
                'class': 'comment-body'
            }).append(viewCommentsLnk)));
            
            // Insert some comments, default them to being hidden
            for (var i = 0; i < comments.length - 3; i++) {
                commentSection.append(p.buildFeedItemComment(comments[i], interaction['id'], true));
            }
            // Insert the rest of the comments
            for (var i = comments.length - 3; i < comments.length; i++) {
                commentSection.append(p.buildFeedItemComment(comments[i], interaction['id'], false));
            }
        } else {
            // Insert the rest of the comments
            for (var i = 0; i < comments.length; i++) {
                commentSection.append(p.buildFeedItemComment(comments[i], interaction['id'], false));
            }
        }
        
        return commentSection;
    };
    
    /**
     * Helper method for building a feed comment
     */
    p.buildFeedItemComment = function(comment, interactionId, isHidden) {
        var commentDate = new Date(comment.datetimets_sec * 1000),
            container = $('<div>', {
                'class': 'response-comment',
                'data-comment-id': comment['id'],
                'data-interaction-id': interactionId,
                'style': (isHidden ? 'display: none;' : '')
            });
        container.append('<div class="comment-img"><a href="/profile/index/id/' + comment['user']['id'] + '"><img src="' + comment['user']['imgurl'] + '?sz=40" /></a></div>');
        container.append('<div class="comment-body"><a href="/profile/index/id/' + comment['user']['id'] + '"><h3 class="mentor-name">' + comment['user']['fname'] + ' ' + comment['user']['lname'] + '</h3></a><div class="comment-text">' + comment['text'] + '</div><div class="comment-date">' + p.formatDate(commentDate) + '</div></div>');
        return container;
    };
    
    /**
     * Helper method for formatting dates into M j, Y format
     * @param date Date Date object
     */
    p.formatDate = function(date) {
        var months = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug.', 'Sept.', 'Oct.', 'Nov.', 'Dec.'];
        return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
    };
})();
