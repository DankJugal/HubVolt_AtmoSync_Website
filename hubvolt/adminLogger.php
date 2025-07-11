<?php

class AdminLogger {
    private $logDirectory;
    private $logFile;
    private $retentionDays = 15; // Retain logs for 15 days

    public function __construct() {
        $this->logDirectory = __DIR__ . '/../logs/hubvolt_logs/admin_panel_logs/';
        $date = date('d-m-Y');
        $this->logFile = $this->logDirectory . $date . '.log';

        if (!is_dir($this->logDirectory)) {
            if (!mkdir($this->logDirectory, 0777, true) && !is_dir($this->logDirectory)) {
                throw new Exception("Failed to create log directory: " . $this->logDirectory);
            }
        }

        $this->cleanupOldLogs(); // Rotate logs based on filename dates
    }

    private function log($level, $message) {
        $timestamp = date('d-m-Y H:i:s');
        $logLine = "$timestamp [$level]: $message" . PHP_EOL;
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
    }

    public function info($message) {
        $this->log('INFO', $message);
    }

    public function warning($message) {
        $this->log('WARNING', $message);
    }

    public function error($message) {
        $this->log('ERROR', $message);
    }

    private function cleanupOldLogs() {
        $files = glob($this->logDirectory . '*.log');
        $now = time();

        foreach ($files as $file) {
            $filename = basename($file, '.log');
            // Expecting format: dd-mm-YYYY
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $filename)) {
                $fileDate = DateTime::createFromFormat('d-m-Y', $filename);
                if ($fileDate !== false) {
                    $diff = $now - $fileDate->getTimestamp();
                    if ($diff > ($this->retentionDays * 86400)) {
                        unlink($file); // Delete old log
                    }
                }
            }
        }
    }
}
