<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo $title; ?></title>
</head>
<body style="margin: 0; padding: 20px;">
    <a href="<?php echo $bc_link; ?>"><?php echo "View bingo card"; ?></a>
    <br/>
    <?php if (!empty($rp_link)): ?>
    <a href="<?php echo $rp_link; ?>"><?php echo "Reset password"; ?></a>
    <?php endif; ?>
</body>
</html>