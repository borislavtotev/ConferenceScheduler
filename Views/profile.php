<h1>Hello, <?= $model->getUsername(); ?></h1>
<h3>
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

<a href="/user/logout">Logout</a>