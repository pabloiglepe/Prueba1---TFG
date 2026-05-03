<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SECRETO DEL ENDPOINT DEL SCHEDULER EXTERNO
    |--------------------------------------------------------------------------
    |
    | Clave secreta que debe enviar cron-job.org en el header X-Cron-Secret
    | para poder ejecutar el scheduler desde el exterior. Se configura como
    | variable de entorno CRON_SECRET en Railway y en el .env local.
    |
    | Usar config('padelsync.cron_secret') en el código de la aplicación.
    |
    */
    'cron_secret' => env('CRON_SECRET'),

];