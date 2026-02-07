<?php

namespace App\Classes;

use CURLFile;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class DbBackup
{

    public function  backup()
    {
        $dbHost = '127.0.0.1';
        $dbUser = 'root';
        $dbPass = '@Sosmost666';
        $dbName = 'aniroob';
        $outputDir = __DIR__ . '/backups';
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $backupPath = $this->backupDatabase($dbHost, $dbUser, $dbPass, $dbName, $outputDir);

        if (!$backupPath) {
            die("خطا در گرفتن بک‌آپ");
        }

        $zipPath = $this->zipFile($backupPath);
        if (!$zipPath) {
            die("خطا در فشرده‌سازی");
        }

        $response = $this->sendToBale($zipPath);
    }

    private function sendToBale($zipPath)
    {
        $token='182541559:9RLotUnxp4Z7kX7qFHHwf6eEmjjkj6PtsNQAkOc8';
        $channelId='6094691689';
        $url = "https://tapi.bale.ai/bot{$token}/sendDocument";
        $postFields = [
            'chat_id' => $channelId,
            'document' => new CURLFile($zipPath)
        ];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields
        ]);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
           dd($error);
        }

        return $response;
    }

    private function zipFile($filePath)
    {
        $zipPath = $filePath . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($filePath, basename($filePath));
            $zip->close();
            return $zipPath;
        } else {
            return false;
        }
    }

    private function backupDatabase(string $dbHost, string $dbUser, string $dbPass, string $dbName, string $outputDir)
    {
        $date = date('Y-m-d_H-i-s');
        $fileName = "{$dbName}_{$date}.sql";
        $filePath = "{$outputDir}/{$fileName}";
        $command = "mysqldump -h$dbHost -u$dbUser -p$dbPass $dbName > $filePath";
        system($command, $resultCode);

        if ($resultCode === 0) {
            return $filePath;
        } else {
            return false;
        }
    }
}
