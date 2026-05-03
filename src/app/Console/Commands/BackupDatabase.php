<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * NOMBRE Y FIRMA DEL COMANDO
     *
     * @var string
     */
    protected $signature = 'db:backup
                            {--path= : Ruta completa donde guardar el backup (opcional)}';

    /**
     * DESCRIPCIÓN DEL COMANDO
     *
     * @var string
     */
    protected $description = 'Genera un backup SQL de la base de datos y lo guarda en storage/app/backups/';

    /**
     * DIRECTORIO BASE DE LOS BACKUPS DENTRO DE storage/app/
     */
    const BACKUP_DIR = 'backups';

    /**
     * TABLAS QUE SE EXCLUYEN DEL BACKUP (CACHÉ Y DATOS TEMPORALES)
     */
    const EXCLUDED_TABLES = [
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'sessions',
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
    ];

    /**
     * EJECUTA EL COMANDO
     */
    public function handle(): int
    {
        $this->info('Iniciando backup de la base de datos...');

        // DETERMINAR RUTA DE DESTINO
        $filename  = 'padelsync_backup_' . Carbon::now()->format('Y-m-d_His') . '.sql';
        $outputPath = $this->option('path')
            ? $this->option('path')
            : storage_path('app/' . self::BACKUP_DIR . '/' . $filename);

        // CREAR EL DIRECTORIO SI NO EXISTE
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $sql = $this->generateSqlDump();

            file_put_contents($outputPath, $sql);

            $sizeKb = round(filesize($outputPath) / 1024, 2);

            $this->info("✓ Backup completado correctamente.");
            $this->line("  Archivo : {$outputPath}");
            $this->line("  Tamaño  : {$sizeKb} KB");

            // LIMPIAR BACKUPS ANTIGUOS (CONSERVAR LOS ÚLTIMOS 7)
            $this->purgeOldBackups();

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("Error durante el backup: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('db:backup — excepción inesperada', [
                'error' => $e->getMessage(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * GENERA EL VOLCADO SQL COMPLETO DE LA BASE DE DATOS
     *
     * ITERA TODAS LAS TABLAS DE LA BD (EXCEPTO LAS EXCLUIDAS) Y GENERA
     * SENTENCIAS CREATE TABLE + INSERT PARA CADA UNA
     *
     * @return string
     */
    private function generateSqlDump(): string
    {
        $lines   = [];
        $lines[] = '-- ============================================================';
        $lines[] = '-- PadelSync — Backup de base de datos';
        $lines[] = '-- Generado : ' . Carbon::now()->toDateTimeString();
        $lines[] = '-- Base de datos : ' . config('database.connections.mysql.database');
        $lines[] = '-- ============================================================';
        $lines[] = '';
        $lines[] = 'SET FOREIGN_KEY_CHECKS = 0;';
        $lines[] = '';

        // OBTENER TODAS LAS TABLAS DE LA BASE DE DATOS ACTIVA
        $tables = $this->getTables();

        foreach ($tables as $table) {

            // SALTAMOS LAS TABLAS EXCLUIDAS
            if (in_array($table, self::EXCLUDED_TABLES)) {
                continue;
            }

            $this->line("  → Exportando tabla: {$table}");

            $lines[] = "-- ------------------------------------------------------------";
            $lines[] = "-- Tabla: {$table}";
            $lines[] = "-- ------------------------------------------------------------";

            // OBTENER DDL DE LA TABLA
            $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
            if (!empty($createResult)) {
                $createSql    = $createResult[0]->{'Create Table'};
                $lines[]      = "DROP TABLE IF EXISTS `{$table}`;";
                $lines[]      = $createSql . ";";
                $lines[]      = '';
            }

            // OBTENER FILAS Y GENERAR INSERTS
            $rows = DB::table($table)->get();

            if ($rows->isEmpty()) {
                $lines[] = "-- (tabla vacía)";
                $lines[] = '';
                continue;
            }

            // INSERTAR EN BLOQUES DE 100 FILAS PARA NO GENERAR LÍNEAS ENORMES
            $chunks = $rows->chunk(100);

            foreach ($chunks as $chunk) {
                $columns      = array_keys((array) $chunk->first());
                $columnList   = '`' . implode('`, `', $columns) . '`';
                $valueSets    = [];

                foreach ($chunk as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return "'" . addslashes($value) . "'";
                    }, (array) $row);

                    $valueSets[] = '(' . implode(', ', $values) . ')';
                }

                $lines[] = "INSERT INTO `{$table}` ({$columnList}) VALUES";
                $lines[] = implode(",\n", $valueSets) . ";";
                $lines[] = '';
            }
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS = 1;';
        $lines[] = '';
        $lines[] = '-- Fin del backup';

        return implode("\n", $lines);
    }

    /**
     * OBTIENE LA LISTA DE TABLAS DE LA BASE DE DATOS ACTIVA
     *
     * @return array
     */
    private function getTables(): array
    {
        $results = DB::select('SHOW TABLES');
        $key     = 'Tables_in_' . config('database.connections.mysql.database');

        return array_map(fn($row) => $row->$key, $results);
    }

    /**
     * ELIMINA LOS BACKUPS MÁS ANTIGUOS CONSERVANDO SOLO LOS ÚLTIMOS 7
     *
     * @return void
     */
    private function purgeOldBackups(): void
    {
        $backupDir = storage_path('app/' . self::BACKUP_DIR);

        if (!is_dir($backupDir)) {
            return;
        }

        // OBTENER TODOS LOS ARCHIVOS .sql ORDENADOS DEL MÁS RECIENTE AL MÁS ANTIGUO
        $files = glob($backupDir . '/padelsync_backup_*.sql');

        if (!$files || count($files) <= 7) {
            return;
        }

        rsort($files);
        $toDelete = array_slice($files, 7);

        foreach ($toDelete as $file) {
            unlink($file);
            $this->line("  ✗ Backup antiguo eliminado: " . basename($file));
        }
    }
}