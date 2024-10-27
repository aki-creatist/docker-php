<?php require '../header.php'; ?>
<p>アップロードするファイルを指定してください。</p>
<form action="../../../../../Downloads/php%202/chapter5/upload-output.php" method="post" enctype="multipart/form-data">
<p><input type="file" name="file"></p>
<p><input type="submit" value="アップロード"></p>
</form>
<?php require '../footer.php'; ?>
