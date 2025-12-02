<?php
declare(strict_types=1);

$fromFile = $argv[1];
if (!file_exists($fromFile)) {
    throw new InvalidArgumentException('File does not exist');
}

$fromRows = [];
if (($fp = fopen($fromFile, "r")) !== FALSE) {
    while (($data = fgetcsv($fp, 0, ",", '"', '')) !== FALSE) {
        $fromRows[$data[0]] = [
            'source' => $data[0],
            'target' => $data[1],
            'component_type' => $data[2],
            'component_name' => $data[3],
        ];
    }
}
fclose($fp);

echo "Source CSV contains ".count($fromRows)." records\n";

$toFile = __DIR__ . '/nl_NL.csv';
$toRows = [];
if (($fp = fopen($toFile, "r")) !== FALSE) {
    while (($data = fgetcsv($fp, 0, ",", '"', '')) !== FALSE) {
        $toRows[$data[0]] = [
            'source' => $data[0],
            'target' => $data[1],
            'component_type' => $data[2],
            'component_name' => $data[3],
        ];
    }
}
fclose($fp);


echo "Target CSV contains ".count($toRows)." records\n";

$newCount = 0;
foreach ($fromRows as $fromSource => $fromRow) {
    if (empty(trim((string)$fromSource))) {
        continue;
    }

    if (false === array_key_exists($fromSource, $toRows)) {
        $toRows[$fromSource] = $fromRow;
        $newCount++;
    }
}

if (!$newCount > 0) {
    echo "Nothing to add\n";
    exit;
}

echo "Added ".$newCount." new records\n";

usort($toRows, function(array $row1, array $row2) {
    return strcmp($row1['source'], $row2['source']);
});

$fp = fopen($toFile, 'w');
foreach ($toRows as $toRow) {
    fputcsv($fp, $toRow, ',', '"', '');
}

fclose($fp);
