<!-- Search Box -->
<div style="height: 35px; margin-top: 10px; position: relative;">
    <div class="search-container">
        <ul class="search-listview" data-role="listview" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Search mentors or students" data-mini="true"></ul>
    </div>
</div>

<!-- Mentor Info -->
<div id='mentor-profile-info'>
    <div class='mentor-basic-info'>
        <div class="mentor-image">
            <img src="<?=$mentorInfo['imgurl']?>?sz=110" width="110" />
        </div>
        <div class="mentor-details">
            <div class='details-wrapper'>
                <h2 class="mentor-name"><?=$mentorInfo['fname']." ".$mentorInfo['lname']?></h2>
                <div class='mentor-join-date'>
                    Joined: <?=date('F j, Y', $mentorInfo['datecreatedts_sec'])?>
                </div>
                <div class='upper-border-line'></div>
                <div class='lower-border-line'></div>
                <div class='mentor-email'>
                    <i class='icon-sm icon-email'></i>
                    <a href="mailto:<?= $mentorInfo['email'] ?>"><?= $mentorInfo['email'] ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class='upper-border-line'></div>
    <div class='lower-border-line'></div>
    <div class='mentor-interaction-summary nxj-grid'>
        <div class='nxj-unit size1of3 text-center mentor-family'>
            <i class='icon-lg icon-family'></i>
            <?=$mentorInfo['family']['name']?>
        </div>
        <div class='nxj-unit size1of3 text-center mentor-house'>
            <i class='icon-lg icon-house'></i>
            <?=$mentorInfo['house']['name']?>
        </div>
        <div class='nxj-unit size1of3 text-center mentor-minutes'>
            <i class='icon-lg icon-duration-thirty'></i>
            <?=number_format($minutesLogged)?> Minutes
        </div>
    </div>
    <div class='upper-border-line'></div>
    <div class='lower-border-line'></div>
</div>

<!-- Interaction Feed -->
<div class="interaction-feed"></div>

<!-- View More link -->
<div class="view-more text-center">
    <a href="#">View More Interactions</a>
</div>

<script type="text/javascript">
// Dynamic JS
var imp = imp || {};
imp.profile = imp.profile || {};
imp.profile.index = imp.profile.index || {};
imp.profile.index.mentorId = <?= json_decode($mentorId) ?>;
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

<!-- Error popoup -->
<div data-role="popup" data-overlay-theme="a" data-theme="a" data-transition="fade" class="error-popup">
    <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
    <p class="text-center"></p>
</div>