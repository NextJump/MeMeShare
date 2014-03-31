<div class='admin-accordian' data-role="collapsible-set" data-theme="e" data-corners="false" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d">
    <div data-role="collapsible" data-collapsed="false">
        <h3>Interactions</h3>
        <div id='export-container'>
            <label for="startDate">Start Date: </label>
            <input type="date" placeholder="Enter Start Date (mm/dd/yyyy)" data-clear-btn="false" name="startDate" id="startDate" value="<?=date('Y-m-d', (time() - 2592000))?>">
            <label for="endDate">End Date: </label>
            <input type="date" placeholder="Enter End Date (mm/dd/yyyy)" data-clear-btn="false" name="endDate" id="endDate" value="<?=date('Y-m-d', time())?>">
            <input class='admin-button' type="button" value="Export Interactions" data-theme="e" id="exportButton">
        </div>
    </div>
    <div data-role="collapsible">
        <h3>Families</h3>
        <div id='admin-family-container'>
            <div class='nxj-grid'>
                <div class='nxj-unit size3of8'>
                    <h1>Families</h1>
                </div>
                <div class='nxj-unit size1of12'></div>
                <div class='nxj-unit size1of4'>
                    <input class='admin-button' type="button" value="Export Data" data-theme="e" id="exportFamilyButton">
                </div>
                <div class='nxj-unit size1of24'></div>
                <div class='nxj-unit size1of4'>
                    <input class='admin-button' type="button" value="Add New" data-theme="e" id="addFamilyButton">
                </div>
            </div>
            <table width='100%' cellspacing='0' class='admin-family-list admin-list'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>House</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($families as $f) { ?>
                    <tr data-family-id="<?=$f['id']?>" data-house-id="<?=$f['houseid']?>" class='admin-family-item'>
                        <td><h3 class='family-name'><?=$f['name']?></h3></td>
                        <td><span class='family-email'><b><?=$f['email']?></b></span></td>
                        <td><span class='family-house'><b><?=$f['housename']?></b></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div data-role="collapsible">
        <h3>Mentors</h3>
        <div id='admin-mentor-container'>
            <div class='nxj-grid'>
                <div class='nxj-unit size3of8'>
                    <h1>Mentors</h1>
                </div>
                <div class='nxj-unit size1of12'></div>
                <div class='nxj-unit size1of4'></div>
                <div class='nxj-unit size1of24'></div>
                <div class='nxj-unit size1of4'>
                    <input class='admin-button' type="button" value="Export Data" data-theme="e" id="exportMentorButton">
                </div>
            </div>
            <table width='100%' cellspacing='0'  class='admin-mentor-list admin-list'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Family</th>
                        <th>House</th>
                        <th>Admin?</th>
                    </tr>
                </thead>
                <?php foreach ($mentors as $m) { ?>
                <tr data-mentor-id="<?=$m['id']?>" class='admin-mentor-item' id='admin-mentor-item-<?=$m["id"]?>'>
                    <td><h3 class='mentor-name'><?=($m['fname'].' '.$m['lname'])?></h3></td>
                    <td><span class='mentor-email'><b><?=$m['email']?></b></span></td>
                    <td>
                        <span id='mentor-family-<?=$m["id"]?>' class='mentor-family'><b><a href='#'><?=$m['familyname']?></a></b></span>
                        <select data-house-name="<?=$m['housename']?>" data-role="none" data-mentor-id="<?=$m['id']?>" class='mentor-family-select' id='mentor-family-select-<?=$m["id"]?>'>
                            <?php foreach($families as $fam) { ?>
                            <option data-house-name="<?=$fam['housename']?>" value='<?=$fam['id']?>' <?php if ((int)$fam['id'] === (int)$m['familyid']) { ?>selected<?php } ?>><?=$fam['name']?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td><span id="mentor-house-<?=$m['id']?>" class='mentor-house'><b><?=$m['housename']?></b></span></td>
                    <td>
                        <span id="mentor-is-admin-<?=$m['id']?>" class='mentor-is-admin'><b><a href='#'><?=(((int)$m['isadmin'] === 1) ? 'Yes' : 'No')?></a></b></span>
                        <select data-role="none" data-mentor-id="<?=$m['id']?>" class='mentor-is-admin-select' id='mentor-is-admin-select-<?=$m["id"]?>'>
                            <option value='1' <?php if ((int)$m['isadmin'] === 1) { ?>selected<?php } ?>>Yes</option>
                            <option value='0' <?php if ((int)$m['isadmin'] !== 1) { ?>selected<?php } ?>>No</option>
                        </select>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <div data-role="collapsible">
        <h3>Houses</h3>
        <div id='admin-house-container'>
            <div class='nxj-grid'>
                <div class='nxj-unit size3of8'>
                    <h1>Houses</h1>
                </div>
                <div class='nxj-unit size1of12'></div>
                <div class='nxj-unit size1of4'></div>
                <div class='nxj-unit size1of24'></div>
                <div class='nxj-unit size1of4'>
                    <input class='admin-button' type="button" value="Export Data" data-theme="e" id="exportHouseButton">
                </div>
            </div>
            <table width='100%' cellspacing='0'  class='admin-house-list admin-list'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <?php foreach ($houses as $h) { ?>
                <tr data-house-id="<?=$h['id']?>" class='admin-house-item'>
                    <td><h3 class='house-name'><?=$h['name']?></h3></td>
                    <td><span class='house-email'><b><?=$h['email']?></b></span></td>
                    <td><span class='house-location'><b><?=$h['location']?></b></span></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

<!-- Family dialog box -->
<div data-role="popup" data-overlay-theme="b" data-theme="none" data-corners="false" data-position-to="window" class="popup-dialog family-dialog">
    <div data-role="header" data-theme="none" class="ui-bar-imp">
        <h1 class="ui-title">Add New Family</h1>
        <a data-rel="back" data-iconpos="notext" data-theme="none" data-shadow="false" data-icon="delete" class="ui-btn-right"></a>
    </div>
    <div data-role="content" data-theme="d">
        <div class="add-family-container">
            <form>
                <input id="adminFamilyStudentFname" placeholder="Student First Name" type="text" value="">
                <input id="adminFamilyStudentLname" placeholder="Student Last Name" type="text" value="">
                <input id="adminFamilyEmail" placeholder="Family Email" type="text" value="">
                <input id="adminFamilyCohort" placeholder="Cohort Name" type="text" value="">
                <input id="adminFamilyHouse" type="text" data-house-id="" placeholder="Select house" value="">
                <div class="admin-search-container" style="display: none;">
                    <ul class="admin-search-listview" data-role="listview" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Search for a house"></ul>
                </div>
                <div class='add-family-error text-center'></div>
                <a href="#" id="addFamilySubmitBtn"><h3>Submit</h3></a>
            </form>
        </div>
    </div>
</div>

<div>
    <a id="dateErrorPopupLnk" style="display:none;" href="#dateErrorPopup" data-role="button" data-inline="true" data-rel="popup" data-position-to="window">#</a>
    <div data-role="popup" id="dateErrorPopup" data-overlay-theme="a" class="ui-content" data-theme="a" data-transition="fade">
        <p></p>
    </div>
</div>