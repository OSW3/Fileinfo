<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OSW3\Fileinfo;

$file = new Fileinfo('./files/unicorn.jpg');
?>

<pre><?php 
    print_r($file->info()); 
    // print_r($file->header());
    // print_r($file->content());
    // print_r($file->image());
?></pre>

<!-- <img src="<?php /** echo $file->getData64(); /**/ ?>" alt=""> -->

<?php $file = new Fileinfo('./files/jupiter.jpg'); ?>
<pre><?php print_r($file->info()); ?></pre>


<?php $file = new Fileinfo('./files/StartupScreen.mp3'); ?>
<pre><?php print_r($file->info()); ?></pre>
