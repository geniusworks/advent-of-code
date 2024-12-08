<?php

class DataImporter
{
    public static function importFromFile($filename, $flags = 0): false|array
    {
        try {
            if (!file_exists($filename)) {
                throw new Exception("File '$filename' not found");
            }

            return file($filename, $flags);
        } catch (Exception $e) {
            return ["Error: " . $e->getMessage()];
        }
    }

    public static function importFromFileContents($filename): string
    {
        return file_get_contents($filename);
    }

    public static function importFromFileWithDefaultFlags($filename): false|array
    {
        return self::importFromFile($filename, FILE_IGNORE_NEW_LINES);
    }
}