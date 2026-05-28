<?php

return [

    'temporary_file_upload' => [
        'disk' => 'local',        // Keep this local since MinIO is internal
        'rules' => 'max:102400',   // 100MB max file size limit
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => ['png', 'gif', 'jpg', 'jpeg', 'mp4', 'mov'],
        'chunk_size' => 20480,    // <--- Change from 5120 (5MB) to 20480 (20MB)
    ],

];