<?php
/** @var \SoftUni\Models\BindingModels\UserLoginBindingModel $model */

//define page title
$title = 'Login';
//include header template
require('Shared/header.php');
?>
<div class="container hero-unit">
    <form class="form-horizontal" action='' method="POST">
        <fieldset>
            <div id="legend">
                <legend class="">Login</legend>
            </div>
            <div class="control-group">
                <!-- Username -->
                <label class="control-label"  for="username">Username</label>
                <div class="controls">
                    <input type="text" id="username" name="username" placeholder="" class="input-xlarge" value="<?= $model ? htmlspecialchars($model->getUsername()) : '' ?>">
                </div>
            </div>

            <div class="control-group">
                <input type="hidden" name="formToken" value="<?php if ($_SESSION['formToken'] != null) { $token = $_SESSION['formToken']; echo $token;}?>"/>
                <!-- Password-->
                <label class="control-label" for="password">Password</label>
                <div class="controls">
                    <input type="password" id="password" name="password" placeholder="" class="input-xlarge" value="<?= $model ? htmlspecialchars($model->getPassword()) : '' ?>">
                </div>
            </div>

            <div class="control-group">
                <!-- Button -->
                <div class="controls">
                    <button class="btn btn-success">Login</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<?php
    require('Shared/footer.php');
?>