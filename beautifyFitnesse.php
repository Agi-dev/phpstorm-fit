#!/usr/bin/php

<?php

function getHeader(&$data)
{
    $content = '';
    foreach ($data as $line) {
        if (preg_match('/!3/', $line)) {
            break;
        }
        $content .= array_shift($data) . PHP_EOL;
    }

    return $content;
}

function goNextBlock(&$data)
{
    $content = '';
    foreach ($data as $line) {
        if (preg_match('/!\|/', $line)) {
            break;
        }
        $content .= array_shift($data) . PHP_EOL;
    }

    return $content;
}

function alignBlock(&$data)
{
    $content = array_shift($data).PHP_EOL;
    $block = [];
    $maxCol = [];
    foreach ($data as $line) {
        if (empty(trim($line))) {
            break;
        }
        $row = explode('|', array_shift($data));
        foreach ($row as $index => $col) {
            $col = trim($col);
            $row[$index] = $col;

            if (empty($col)) {
                continue;
            }

            $lg = strlen(trim($col));
            if (!isset($maxCol[$index])) {
                $maxCol[$index] = $lg;
                continue;
            }

            if ($maxCol[$index] < $lg) {
                $maxCol[$index] = $lg;
            }
        }
        $block[] = $row;
    }

    foreach ($block as $row) {
        foreach ($row as $index => $col) {
            if (isset($maxCol[$index])) {
                $content .= sprintf("| %-".$maxCol[$index]."s ", $col);
            }
        }
        $content .= '| ' . PHP_EOL;
    }

    return $content;
}

if ($argc !== 2) {
    echo 'beautifyFitnesse <fitnesse file>'.PHP_EOL;
    return 1;
}
$fileToBeautify = $argv[1];
if (!file_exists($fileToBeautify)) {
    echo 'File '.$fileToBeautify.' not found '.PHP_EOL;
    return 1;
}

$data = file($fileToBeautify, FILE_IGNORE_NEW_LINES);

echo PHP_EOL . 'Beautify ' . $fileToBeautify . ' ... ';

$content = getHeader($data);
do{
    $content .= goNextBlock($data);
    $content .= alignBlock($data);
} while ($data);

file_put_contents($fileToBeautify, $content);
echo 'Ok' . PHP_EOL;

return 0;