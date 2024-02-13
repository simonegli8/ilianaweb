<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<?php gpOutput::GetHead(); ?>
</head>

<body>

<div id="header">
<?php gpOutput::GetExtra('Header'); ?>
</div>

<div id="menu">
<?php gpOutput::GetMenu(); ?>
</div>

<div id="content">
<?php $page->GetContent(); ?>
</div>

<div id="column">
<?php gpOutput::GetAllGadgets() ?>
</div>

<div id="footer">
<?php gpOutput::GetExtra('Footer'); ?>
<?php gpOutput::GetAdminLink(); ?>
</div>

</body>
</html>
