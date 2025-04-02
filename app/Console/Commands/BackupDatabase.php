<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un respaldo de la base de datos MySQL usando mysqldump';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
{
    try {
        // Verifica si la carpeta de respaldo existe, si no, crearla
        $backupDir = storage_path('app/backup');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        // Ruta al ejecutable de mysqldump
        $mysqldumpPath = 'C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe';
        
        // Datos de conexión
        $user = 'root';
        $password = '1234'; // Reemplaza con tu contraseña de MySQL
        $database = 'restaurant';
        $backupPath = $backupDir . '/restaurant_' . date('Y-m-d_H-i-s') . '.sql';

        // Comando mysqldump
        $command = escapeshellcmd("$mysqldumpPath -u $user -p$password $database");

        // Ejecutar el comando
        exec($command . ' > ' . escapeshellarg($backupPath), $output, $resultCode);

        if ($resultCode === 0) {
            $this->info("Respaldo realizado con éxito: $backupPath");
        } else {
            $this->error('Hubo un error al realizar el respaldo.');
        }
    } catch (\Exception $e) {
        $this->error('Ocurrió un error: ' . $e->getMessage());
    }
}

}
