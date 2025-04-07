<?php
session_start();
session_unset();
session_destroy();

header('Location: index.php'); // کاتێ لۆگاوت ئەکەی ئەجێتەوە بۆ لۆگین پەیج
exit;

