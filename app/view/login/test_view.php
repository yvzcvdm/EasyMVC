<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>

<body>
    <h1>Login Test File</h1>
    <hr>
    Toplam Sayı : <?=$user["dataCount"]?>
    <hr>
    <?foreach($user["data"] as $item){
        echo $item["user_name"].'<br>';
    }?>
</body>

</html>