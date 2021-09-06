<?php

$file = "/var/www/laabs/src/ext/workflow/data/samples/jeu_demo_24/FAC_FOUR_DSI/bdd3.csv";
$csv= file_get_contents($file);
$all_bills = array_map("str_getcsv", explode("\n", $csv));
$temp_bills = [];
foreach ($all_bills as $key => $value) {
    if (!empty($value[0])) {
        if ($key != 0) {
            $temp_bills[] = [
                "digitalResources" => [
                    [
                        "fileName" => $value[0] . '.pdf'
                    ]
                ],
                "descriptionObject" => [
                    "documentId" => $value[0],
                    "taxIdentifier" => $value[5]
                    // "supplier" => $value[1],
                    // "netPayable" => $value[7],
                    // "dueDate" => '',
                    // "orderNumber" => $value[0],
                    // "salePerson" => stripAccents($value[2]) . ' ' . stripAccents($value[3]),
                ],
                "archiveName" => $value[0],
                "archivalProfileReference" => "FACACH",
                "originatorOrgRegNumber" => "FOUR",
                // "originatingDate" => changeDateFormat($value[15])
            ];
        }
    }
}

$json_bills =  json_encode($temp_bills, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$file = fopen('json_bill.txt', 'w+');
fwrite($file, $json_bills);
fclose($file);
function stripAccents($str)
{
    return strtr(
        utf8_decode($str),
        utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
        'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
    );
}

function changeDateFormat($date)
{
    $exploded_date = explode('/', $date);
    $formatted_date = '20' . trim($exploded_date[2]) . '/' . trim($exploded_date[1]) . '/' . trim($exploded_date[0]);
    return $formatted_date;
}
