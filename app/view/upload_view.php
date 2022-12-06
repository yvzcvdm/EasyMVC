<div id="container">
    <img class="logo" src="<?= $app_root ?>assets/img/logo.png" alt="Logo">
    <h1><?= $title ?></h1>

    <ul>
        <li><a href="<?= $app_root ?>">Home</a></li>
        <li><a href="<?= $app_root ?>corporate/">Corporate</a></li>
        <li><a href="<?= $app_root ?>contact">Contact</a></li>
        <li><a href="<?= $app_root ?>upload">Upload</a></li>
    </ul>
    <ul>
        <li><a href="<?= $app_root ?>admin/">Admin</a></li>
        <li><a href="<?= $app_root ?>admin/users/">Users</a></li>
        <li><a href="<?= $app_root ?>admin/settings">Settings</a></li>
    </ul>
    <ul>
        <li><a href="<?= $app_root ?>admin/user">User</a></li>
        <li><a href="<?= $app_root ?>admin/user/add">Add</a></li>
        <li><a href="<?= $app_root ?>admin/user/edit">Edit</a></li>
    </ul>
    <div class="content">
        <div class="row">
            <div class="col">

                <form action="<?= $app_uri ?>" method="post" enctype="multipart/form-data">
                    <table>
                        <tr>
                            <td>Dosyalar Seç</td>
                            <td><input type="file" name="files[]" multiple></td>
                        </tr>
                        <tr>
                            <td>Dosyalar Seç</td>
                            <td><input type="file" name="avatars[]" multiple></td>
                        </tr>
                        <tr>
                            <td>Dosya Seç</td>
                            <td><input type="file" name="avatar"></td>
                        </tr>
                        <tr>
                            <td>Dosya Seç</td>
                            <td><input type="file" name="file"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" value="Test Send"></td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>

    </div>
    <div class="content"><? highlight_string("<?php " . var_export($list_upload, true) . "; ?>"); ?></div>
</div>