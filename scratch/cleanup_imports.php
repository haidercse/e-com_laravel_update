<?php

$dirs = ['app/Http/Controllers', 'app/Utility', 'app/Http/Middleware', 'app/Models', 'app/Http'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($files as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            $lines = explode("\n", $content);
            $newLines = [];
            $seen = [];
            $modified = false;

            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (preg_match('/^use (.*;)$/', $trimmed, $matches)) {
                    $import = $matches[1];
                    // Normalize import (remove leading backslash if any, though usually not there)
                    $import = ltrim($import, '\\');
                    
                    if (isset($seen[$import])) {
                        $modified = true;
                        echo "Removed duplicate import $import in " . $file->getFilename() . "\n";
                        continue; // Skip duplicate
                    }
                    
                    // Special case: if we have "use Illuminate\Support\Facades\Auth;" 
                    // and we see "use Auth;", remove the latter.
                    if (preg_match('/^use ([a-zA-Z0-9]+);$/', $import, $m)) {
                        $alias = $m[1];
                        $facadeMatch = false;
                        foreach($seen as $existingImport => $val) {
                            if (strpos($existingImport, "Illuminate\Support\Facades\\$alias") !== false) {
                                $facadeMatch = true;
                                break;
                            }
                        }
                        if ($facadeMatch) {
                            $modified = true;
                            echo "Removed redundant alias $import in " . $file->getFilename() . "\n";
                            continue;
                        }
                    }

                    $seen[$import] = true;
                }
                $newLines[] = $line;
            }

            if ($modified) {
                file_put_contents($file->getPathname(), implode("\n", $newLines));
            }
        }
    }
}
