<h1>Hello, <?= $model->getUsername(); ?></h1>
<h3>
    Resources:
    <p>Gold: <?= $model->getGold(); ?></p>
    <p>Food: <?= $model->getFood(); ?></p>
</h3>

<?php if(isset($model->error)): ?>
    <h2>An error occurred</h2>
<?php elseif(isset($model->success)): ?>
    <h2>Successfully updated profile</h2>
<?php endif; ?>


<form method="post">
    <div>
        <input type="text" name="username" value="<?=$model->getUsername();?>" />
        <input type="password" name="password" />
        <input type="password" name="confirm" />
        <input type="submit" name="edit" value="Edit" />
    </div>
</form>

Go to:
<div class="menu">
    <a href="buildings.php">Buildings</a>
</div>
