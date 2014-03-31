<!-- Search Box -->
<div style="height: 35px; margin-top: 10px; position: relative;">
    <div class="search-container">
        <ul class="search-listview" data-role="listview" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Search mentors or students" data-mini="true"></ul>
    </div>
</div>

<!-- Interaction Feed -->
<div class="interaction-feed"></div>

<!-- View More link -->
<div class="view-more text-center">
<?php if ($filters['interactionid'] > 0) { ?>
    <a href="#" data-mode="viewall">View All Interactions</a>
<?php } else { ?>
    <a href="#" data-mode="viewmore">View More Interactions</a>
<?php } ?>
</div>

<script type="text/javascript">
// Dynamic JS
var imp = imp || {};
imp.home = imp.home || {};
imp.home.index = imp.home.index || {};
imp.home.index.userId = <?= json_decode($userId) ?>;
imp.home.index.houseId = <?= json_decode($houseId) ?>;
imp.home.index.familyId = <?= json_decode($familyId) ?>;
imp.home.index.filters = <?= json_encode($filters) ?>;
</script>

<!-- Comment dialog box -->
<div data-role="popup" data-overlay-theme="b" data-theme="none" data-corners="false" data-position-to="window" class="popup-dialog comment-dialog">
    <div data-role="header" data-theme="none" class="ui-bar-imp">
        <h1 class="ui-title">Post Comment</h1>
        <a data-rel="back" data-iconpos="notext" data-theme="none" data-shadow="false" data-icon="delete" class="ui-btn-right"></a>
    </div>
    <div data-role="content" data-theme="d">
        <div class="comments"></div>
        <div class="post-comment-container">
            <form>
                <textarea name="comment" id="comment" class="comment" placeholder="Enter your comment here"></textarea>
                <a href="#" id="post-button"><h3>Post Comment</h3></a>
            </form>
        </div>
    </div>
</div>

<!-- Likes dialog box -->
<div data-role="popup" data-overlay-theme="b" data-theme="none" data-corners="false" data-position-to="window" class="popup-dialog likes-dialog">
    <div data-role="header" data-theme="none" class="ui-bar-imp">
        <h1 class="ui-title"><span class="like-count"></span> <span class="like-word"></span></h1>
        <a data-rel="back" data-iconpos="notext" data-theme="none" data-shadow="false" data-icon="delete" class="ui-btn-right"></a>
    </div>
    <div data-role="content" data-theme="d">
        <div class="response-likes"></div>
    </div>
</div>

<!-- FTU dialog box -->
<div id="ftu-dialog" data-role="popup" data-overlay-theme="b" data-theme="none" data-corners="false" data-position-to="window" data-dismissible="false" class="popup-dialog">
    <div data-role="header" data-theme="none" class="ui-bar-imp">
        <h1 class="ui-title">Search Students</h1>
    </div>
    <div data-role="content" data-theme="d">
        <p>Welcome!  Please search for and select your <strong>primary</strong> student below.  If you work with multiple students, select any student from your house.</p>
        <div class="search-container" style="position: relative;">
            <ul id="student-autocomplete" class="search-listview" data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="Enter your student's name" data-filter-theme="d"></ul>
        </div>
    </div>
</div>

<!-- Error popoup -->
<div data-role="popup" data-overlay-theme="a" data-theme="a" data-transition="fade" class="error-popup">
    <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
    <p class="text-center"></p>
</div>