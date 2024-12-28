<?php

$file = json_decode(file_get_contents('expenses.json'), true);

function add(array $argv): string
{
  for ($i = 2; $i <= 5; $i++) {
    if (!isset($argv[$i]) || $argv[2] != '--description' && $argv[4] != '--amount')
      return "# Invalid input\n# Usage: php expense-tracker.php add --description <description> --amount <amount>\n";
  }

  if (!is_numeric($argv[5]) || $argv[5] < 1)
    return "# Invalid amount\n# Amount must be numeric and positive\n";

  global $data, $file;

  $id = count($data) + 1;

  foreach ($data as $expense) {
    if ($expense['id'] == $id) $id++;
  }

  array_push($data, [
    'id' => $id,
    'description' => $argv[3],
    'amount' => $argv[5],
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
  ]);

  $file[0]['data'] = $data;
  file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

  return "# Expense added successfully (ID: $id)\n";
}
