<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class BackupController extends Controller
{
    const BACKUP_DIR = 'backups';

    public function index()
    {
        $backupDir = storage_path('app/' . self::BACKUP_DIR);
        $files = glob($backupDir . '/padelsync_backup_*.sql') ?: [];
        rsort($files);

        $backups = collect($files)->map(function ($path) {
            $name = basename($path);
            preg_match('/padelsync_backup_(\d{4}-\d{2}-\d{2})_(\d{2}-\d{2}-\d{2})\.sql/', $name, $m);
            $date = isset($m[1])
                ? Carbon::parse($m[1] . ' ' . str_replace('-', ':', $m[2]))
                : null;
            $sizeBytes = filesize($path);
            $sizeLabel = $sizeBytes >= 1048576
                ? round($sizeBytes / 1048576, 2) . ' MB'
                : round($sizeBytes / 1024, 2) . ' KB';

            return [
                'name'  => $name,
                'date'  => $date,
                'size'  => $sizeLabel,
            ];
        });

        return view('admin.backups.index', compact('backups'));
    }

    public function restore(Request $request)
    {
        $request->validate(['file' => 'required|string']);

        $fileName = basename($request->input('file'));
        $filePath = storage_path('app/' . self::BACKUP_DIR . '/' . $fileName);

        if (!file_exists($filePath) || !str_starts_with($fileName, 'padelsync_backup_')) {
            return back()->with('error', 'Archivo de backup no encontrado.');
        }

        $exitCode = Artisan::call('db:restore', [
            'file'    => $fileName,
            '--force' => true,
        ]);

        if ($exitCode === 0) {
            return back()->with('success', "Base de datos restaurada correctamente desde {$fileName}.");
        }

        return back()->with('error', 'Error durante la restauración. Revisa los logs del servidor.');
    }
}
