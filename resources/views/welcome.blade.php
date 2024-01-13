<?php
require_once base_path('resources/views/sveltehelpers.php');



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>


<script>
     let asseturl = <?php echo json_encode(asset('/')); ?>;
     let baseurl = <?php echo json_encode(url('/')); ?>;
</script>

<?= vite('main.js') ?>

<div id="app"></div>

    
</body>
</html>