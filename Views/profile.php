<?php
/** @var \SoftUni\Models\BindingModels\UserBindingModel $model */

//define page title
$title = 'Edit Profile';
//include header template
require('Shared/header.php');
?>
<div class="container hero-unit">
    <form class="form-horizontal" action='' method="POST">
        <fieldset>
            <div id="legend">
                <legend class=""><?= $model->getUsername(); ?>'s Profile</legend>
            </div>
            <div class="control-group">
                <!-- Username -->
                <label class="control-label"  for="username">Username</label>
                <div class="controls">
                    <input type="text" id="username" name="username" placeholder="" class="input-xlarge" value="<?= $model->getUsername(); ?>">
                    <p class="help-block">Username can contain any letters or numbers, without spaces</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Current Password-->
                <label class="control-label" for="currentPassword">Current Password</label>
                <div class="controls">
                    <input type="password" id="currentPassword" name="currentPassword" placeholder="" class="input-xlarge">
                    <p class="help-block">Password should be at least 4 characters</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Password-->
                <label class="control-label" for="password">New Password</label>
                <div class="controls">
                    <input type="password" id="password" name="password" placeholder="" class="input-xlarge">
                    <p class="help-block">Password should be at least 4 characters</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Confirm Password-->
                <label class="control-label" for="confirm">Confirm Password</label>
                <div class="controls">
                    <input type="password" id="confirm" name="confirm" placeholder="" class="input-xlarge">
                    <p class="help-block">Password should be at least 4 characters</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Button -->
                <div class="controls">
                    <button class="btn btn-success" value="Edit">Edit Profile</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>