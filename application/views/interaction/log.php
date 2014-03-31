<div id='logContainer'>
    <div class='log-form'>
        <div class="family-container">
            <input id="logFamilyId" type="text" data-family-id="<?= $family['familyid'] ?>" value="<?= $family['familyname'] ?>">
            <div class="search-container" style="display: none;">
                <ul class="search-listview" data-role="listview" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Search for a student"></ul>
            </div>
        </div>
        
        <div class='date-label'>Enter date of interaction and duration (minutes):</div>
        <div class="ui-grid-a" id="logTimingsContainer">
            <div class="ui-block-a" style="position:relative">
                <input type="date" placeholder="Enter Date (yyyy-mm-dd)" data-clear-btn="false" name="logDate" id="logDate" value="<?=date('Y-m-d', time())?>" max="<?=date('Y-m-d', time())?>">
            </div>
            <div class="ui-block-b">
                <input type="number" placeholder="Duration (minutes)" data-clear-btn="true" name="logMinutes" pattern="[0-9]*" id="logMinutes" value="" min=0>
            </div>
        </div>

        <div class='type-label'>Select category and sub-category of interaction:</div>
        <div class='log-types'>
            <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                <?php $i=0;?>
                <?php foreach ($types as $key=>$type) { ?>
                <input data-theme="c" type="radio" name="log-types-choice" id="logTypesChoice<?=$key?>" value="<?=$key?>" <?=($i===0) ? "checked='checked'" : ""?>>
                <label for="logTypesChoice<?=$key?>"><?=$type['name']?></label>
                <?php $i++; ?>
                <?php } ?>
            </fieldset>
        </div>
        <div class='log-subtypes' data-role="">
            <select id="logSubtypesDropdown">
                <?php foreach ($types[1]['sub_types'] as $sub) { ?>
                <option value="<?=$sub['id']?>"><?=$sub['name']?></option>
                <?php } ?>
            </select>
        </div>
        <textarea maxlength='5000' cols="40" rows="8" id="logDesc" placeholder="Describe Your Interaction"></textarea>
        <span class='chars-remaining'>You have 5000 characters remaining</span>
        <div id='logBtnContainer'>
            <input type="button" value="Submit" data-theme="e" id="logSubmitButton">
        </div>
        <div>
            <a id="errorPopupLnk" style="display:none;" href="#errorPopup" data-role="button" data-inline="true" data-rel="popup" data-position-to="window">#</a>
            <div data-role="popup" id="errorPopup" data-overlay-theme="a" class="ui-content" data-theme="a" data-transition="fade">
                <p></p>
            </div>
            <a id="successPopupLnk" style="display:none;" href="#successPopup" data-role="button" data-inline="true" data-rel="popup" data-position-to="window">#</a>
            <div data-role="popup" id="successPopup" data-overlay-theme="a" class="ui-content" data-theme="e" data-transition="fade">
                <p></p>
            </div>
        </div>
    </div>
</div>

<script>
    var imp = imp || {};
    imp.interaction = imp.interaction || {};

    imp.interaction.today = "<?=date('Y-m-d', time())?>";
    imp.interaction.types = <?=(json_encode($types))?>;
</script>