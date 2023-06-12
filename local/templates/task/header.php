<!DOCTYPE html>
<html lang="ru">
<head>
  <title><?= $APPLICATION->ShowTitle() ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="utf-8">
  <link rel="shortcut icon" href="/images/favicon.604825ed.ico" type="image/x-icon">
  <?php
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/common.css");
  ?>
</head>
<body>
  <?php $APPLICATION->ShowHead(); ?>
  <div id="panel"><?= $APPLICATION->ShowPanel() ?></div>
