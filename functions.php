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

function update(array $argv): string
{
  global $data, $file;

  for ($i = 2; $i <= 5; $i++) {
    if (!isset($argv[$i]) || $argv[2] != '--id' || !in_array($argv[4], ['--description', '--amount']))
      return "# Invalid input\n\n# Usage:\n# php index.php update --id <id> --description <description>\n# php index.php update --id <id> --amount <amount>\n";
  }

  if ($argv[4] == '--amount' && (!is_numeric($argv[5]) || $argv[5] < 1))
    return "# Invalid amount\n# Amount must be numeric and positive\n";

  foreach ($data as $key => $value) {
    if ($value['id'] == $argv[3]) {
      $id = $argv[3];
      if ($argv[4] == '--description') $data[$key]['description'] = $argv[5];
      if ($argv[4] == '--amount') $data[$key]['amount'] = $argv[5];

      $data[$key]['updated_at'] = date('Y-m-d H:i:s');

      $file[0]['data'][$key] = $data[$key];
      file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

      return "# Expense updated successfully (ID: $id)\n";
    }
  }

  return "# Id not found\n";
}

function delete(array $argv): string
{
  if (!isset($argv[2]) || $argv[2] != '--id')
    return "# Task id required\n# Usage: php index.php delete --id <id>\n";

  global $data, $file;

  foreach ($data as $key => $value) {
    if ($value['id'] == $argv[3]) {
      $id = $argv[3];
      unset($data[$key]);
      $data = array_values($data);
      $file[0]['data'] = $data;
      file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

      return "# Expense deleted successfully (ID: $id)\n";
    }
  }

  return "# Id not found\n";
}
