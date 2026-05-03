<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestoreDatabase extends Command
{
    /**
     * NOMBRE Y FIRMA DEL COMANDO
     *
     * @var string
     */
    protected $signature = 'db:restore
                            {file? : Nombre del archivo de backup (sin ruta). Si se omite, restaura el más reciente}
                            {--force : Omite la confirmación interactiva}';

    /**
     * DESCRIPCIÓN DEL COMANDO
     *
     * @var string
     */
    protected $description = 'Restaura la base de datos desde un backup SQL generado con db:backup';

    /**
     * DIRECTORIO BASE DE LOS BACKUPS (DEBE COINCIDIR CON BackupDatabase)
     */
    const BACKUP_DIR = 'backups';

    /**
     * EJECUTA EL COMANDO
     */
    public function handle(): int
    {
        $backupDir = storage_path('app/' . self::BACKUP_DIR);

        // RESOLVER QUÉ ARCHIVO USAR
        $targetFile = $this->argument('file');

        if ($targetFile) {
            // ARCHIVO ESPECIFICADO EXPLÍCITAMENTE
            $filePath = $backupDir . '/' . $targetFile;

            if (!file_exists($filePath)) {
                $this->error("No se encontró el archivo: {$filePath}");
                return self::FAILURE;
            }
        } else {
            // SIN ARGUMENTO -> USAR EL BACKUP MÁS RECIENTE
            $files = glob($backupDir . '/padelsync_backup_*.sql');

            if (!$files) {
                $this->error("No se encontraron backups en: {$backupDir}");
                $this->line("Genera uno primero con: php artisan db:backup");
                return self::FAILURE;
            }

            rsort($files);
            $filePath   = $files[0];
            $targetFile = basename($filePath);
        }

        // MOSTRAR INFO Y PEDIR CONFIRMACIÓN
        $sizeKb = round(filesize($filePath) / 1024, 2);

        $this->warn("⚠️  ADVERTENCIA: Esta operación REEMPLAZARÁ todos los datos actuales de la BD.");
        $this->line("  Archivo  : {$filePath}");
        $this->line("  Tamaño   : {$sizeKb} KB");
        $this->line("  Base de datos destino: " . config('database.connections.mysql.database'));
        $this->newLine();

        // CONFIRMACIÓN INTERACTIVA (OMISIBLE CON --force)
        if (!$this->option('force')) {
            if (!$this->confirm('¿Deseas continuar con la restauración?')) {
                $this->info('Restauración cancelada.');
                return self::SUCCESS;
            }
        }

        $this->info('Iniciando restauración...');

        try {
            $sql = file_get_contents($filePath);

            if (empty($sql)) {
                $this->error("El archivo de backup está vacío.");
                return self::FAILURE;
            }

            // EJECUTAR EL SQL DESACTIVANDO TEMPORALMENTE LOS FOREIGN KEY CHECKS
            DB::unprepared('SET FOREIGN_KEY_CHECKS = 0;');

            // DIVIDIR POR SENTENCIAS Y EJECUTAR UNA A UNA
            // SE FILTRAN LAS LÍNEAS DE COMENTARIO Y LAS LÍNEAS VACÍAS
            $statements = $this->parseSqlStatements($sql);

            $executed = 0;
            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    DB::unprepared($statement);
                    $executed++;
                }
            }

            DB::unprepared('SET FOREIGN_KEY_CHECKS = 1;');

            $this->info("✓ Restauración completada correctamente.");
            $this->line("  Sentencias ejecutadas: {$executed}");
            $this->line("  Archivo usado: {$targetFile}");

            return self::SUCCESS;

        } catch (\Throwable $e) {
            // REACTIVAR FK CHECKS AUNQUE HAYA FALLADO
            try {
                DB::unprepared('SET FOREIGN_KEY_CHECKS = 1;');
            } catch (\Throwable $ignore) {}

            $this->error("Error durante la restauración: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('db:restore — excepción inesperada', [
                'file'  => $targetFile,
                'error' => $e->getMessage(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * LISTA LOS BACKUPS DISPONIBLES EN EL DIRECTORIO DE BACKUPS
     *
     * @return void
     */
    public function listBackups(): void
    {
        $backupDir = storage_path('app/' . self::BACKUP_DIR);
        $files     = glob($backupDir . '/padelsync_backup_*.sql');

        if (!$files) {
            $this->line('No hay backups disponibles.');
            return;
        }

        rsort($files);

        $this->line('Backups disponibles:');
        foreach ($files as $index => $file) {
            $label = $index === 0 ? ' (más reciente)' : '';
            $kb    = round(filesize($file) / 1024, 2);
            $this->line("  [{$index}] " . basename($file) . " — {$kb} KB{$label}");
        }
    }

    /**
     * PARSEA EL CONTENIDO SQL Y DEVUELVE UN ARRAY DE SENTENCIAS INDIVIDUALES
     *
     * ELIMINA COMENTARIOS Y DIVIDE POR PUNTO Y COMA RESPETANDO LOS
     * BLOQUES MULTILÍNEA DE LOS INSERT ... VALUES
     *
     * @param  string $sql
     * @return array
     */
    private function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $current    = '';
        $lines      = explode("\n", $sql);

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // IGNORAR LÍNEAS DE COMENTARIO Y LÍNEAS VACÍAS
            if (str_starts_with($trimmed, '--') || $trimmed === '') {
                continue;
            }

            $current .= $line . "\n";

            // UNA SENTENCIA TERMINA CUANDO LA LÍNEA ACABA EN PUNTO Y COMA
            if (str_ends_with($trimmed, ';')) {
                $statements[] = trim($current);
                $current      = '';
            }
        }

        // AÑADIR CUALQUIER RESTO QUE NO HAYA TERMINADO CON PUNTO Y COMA
        if (!empty(trim($current))) {
            $statements[] = trim($current);
        }

        return $statements;
    }
}