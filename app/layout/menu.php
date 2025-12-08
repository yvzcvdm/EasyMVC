<ul>
    <li><a href="<?= $app["root"] ?>">Home</a></li>
    <li><a href="<?= $app["root"] ?>corporate/">Corporate</a></li>
    <li><a href="<?= $app["root"] ?>contact">Contact</a></li>
    <li><a href="<?= $app["root"] ?>upload">Upload</a></li>
    <li><a href="<?= $app["root"] ?>login">Login</a>
        <ul>
            <li><a href="<?= $app["root"] ?>login/register">Add</a></li>
            <li><a href="<?= $app["root"] ?>login/iforgot">Edit</a></li>
        </ul>
    </li>
    <li>
        <a href="<?= $app["root"] ?>admin">Admin</a>
        <ul>
            <li><a href="<?= $app["root"] ?>admin/">Admin</a></li>
            <li><a href="<?= $app["root"] ?>admin/logo/">Logo</a></li>
            <li><a href="<?= $app["root"] ?>admin/settings">Settings</a></li>
            <li><a href="<?= $app["root"] ?>admin/user/">User</a>
                <ul>
                    <li><a href="<?= $app["root"] ?>admin/user">User</a></li>
                    <li><a href="<?= $app["root"] ?>admin/user/add">Add</a></li>
                    <li><a href="<?= $app["root"] ?>admin/user/edit">Edit</a></li>
                </ul>

            </li>
        </ul>
    </li>
</ul>