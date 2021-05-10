<?php

return [
    /*
     * The disk on which to store added files and derived images by default. Choose
     * one or more of the disks you've configured in config/filesystems.php.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),
    
    

    'max_file_size' => 1024 * 1024 * 20, // 20MB
];
