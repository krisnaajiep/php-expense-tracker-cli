<?php

$file = json_decode(file_get_contents('expenses.json'), true);

/**
 * Add new expense
 */
function add(array $argv): string
{
  for ($i = 2; $i <= 7; $i++) {
    if (!isset($argv[$i]) || $argv[2] != '--category' && $argv[4] != '----description' && $argv[6] != '--amount')
      return "# Invalid input\n# Usage: php index.php add --category <category> --description <description> --amount <amount>";
  }

  if (!is_numeric($argv[7]) || $argv[7] < 1)
    return "# Invalid amount\n# Amount must be numeric and positive";

  global $data;
  global $file;

  $summary = intval(explode('$', summary($data, [null, null, '--month', intval(date('m'))]))[1]) + $argv[7];
  $maxBudget = intval($file[0]['budgetPerMonth']);

  if ($summary > $maxBudget)
    return "# Exceeded budget per month\n# Max budget per month: $$maxBudget\n# Summary after addition: $$summary";

  $id = count($data) + 1;

  foreach ($data as $expense) {
    if ($expense['id'] == $id) $id++;
  }

  array_push($data, [
    'id' => $id,
    'category' => $argv[3],
    'description' => $argv[5],
    'amount' => $argv[7],
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
  ]);

  $file[0]['data'] = $data;
  file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

  return "# Expense added successfully (ID: $id)";
}

/**
 * Update an expense or budget per month
 */
function update(array $argv): string
{
  global $file;

  if (isset($argv[2]) && $argv[2] == '--budget') {
    if (!is_numeric($argv[3]) || $argv[3] < 1) return "# Invalid budget\n# Budget must be numeric and positive";

    $file[0]['budgetPerMonth'] == $argv[3];

    return "# Your budget per month is updated to $$argv[3]";
  }

  for ($i = 2; $i <= 5; $i++) {
    if (!isset($argv[$i]) || $argv[2] != '--id' && $argv[4] != '--category' && $argv[4] != '--description' && $argv[4] != '--amount')
      return "# Invalid input\n# Usage:\n# php index.php update --id <id> --category <category>\n# php index.php update --id <id> --description <description>\n# php index.php update --id <id> --amount <amount>\n# php index.php update --budget <budget>";
  }

  if ($argv[4] == '--amount' && (!is_numeric($argv[5]) || $argv[5] < 1))
    return "# Invalid amount\n# Amount must be numeric and positive";

  global $data;

  foreach ($data as $key => $value) {
    if ($value['id'] == $argv[3]) {
      $id = $argv[3];
      if ($argv[4] == '--category') $data[$key]['category'] = $argv[5];
      if ($argv[4] == '--description') $data[$key]['description'] = $argv[5];
      if ($argv[4] == '--amount') {
        $data[$key]['amount'] = $argv[5];
        $summary = intval(explode('$', summary($data, [null, null, '--month', intval(date('m'))]))[1]);
        $maxBudget = intval($file[0]['budgetPerMonth']);

        if ($summary > $maxBudget)
          return "# Exceeded budget per month\n# Max budget per month: $$maxBudget\n# Summary after change: $$summary";
      }

      $data[$key]['updated_at'] = date('Y-m-d H:i:s');

      $file[0]['data'][$key] = $data[$key];
      file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

      return "# Expense updated successfully (ID: $id)";
    }
  }

  return '# Id not found.';
}

/**
 * Delete an expense
 */
function delete(array $argv): string
{
  if (!isset($argv[2]) || $argv[2] != '--id')
    return "# Task id required\n# Usage: php index.php delete --id <id>";

  global $data;
  global $file;

  foreach ($data as $key => $value) {
    if ($value['id'] == $argv[3]) {
      $id = $argv[3];
      unset($data[$key]);
      $data = array_values($data);
      $file[0]['data'] = $data;
      file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

      return "# Expense deleted successfully (ID: $id)";
    }
  }

  return '# Expense id not found.';
}

/**
 * List all expense
 */
function read(array $argv): string
{
  global $data;

  $list = ["# " . str_pad('ID', 5) . str_pad('Date', 15) . str_pad('Category', 15) . str_pad('Description', 25) . str_pad('Amount', 10)];

  foreach ($data as $expense) {
    if (isset($argv[2]) && $argv[2] == '--category') {
      if ($expense['category'] == $argv[3])
        array_push($list, fetch($expense));
    } else {
      array_push($list, fetch($expense));
    }
  }

  return implode("\n", $list);
}

/**
 * Fetch expense from data
 */
function fetch(array $expense): string
{
  $date = explode(' ', $expense['created_at'])[0];
  $description = strlen($expense['description']) > 22
    ? substr($expense['description'], 0, 20) . '...'
    : $expense['description'];
  $category = strlen($expense['category']) > 12
    ? substr($expense['category'], 0, 10) . '...'
    : $expense['category'];
  $row = "# " . str_pad($expense['id'], 5) . str_pad($date, 15) . str_pad($category, 15) . str_pad($description, 25) . '$' . str_pad($expense['amount'], 10);

  return $row;
}

/**
 * View a summary of all expenses or by specific month
 */
function summary(array $data, array $argv): string
{
  $summary = 0;
  $forMonth = '';

  if (!is_null($data)) {
    foreach ($data as $expense) {
      if (isset($argv[2]) && $argv[2] == '--month') {
        $date = explode(' ', $expense['created_at'])[0];
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];

        if ($argv[3] > 12) return "# Invalid month";

        if ($year == date('Y') && intval($month) == $argv[3])
          $summary += intval($expense['amount']);

        $forMonth = ' for ' . date_format(date_create_from_format('n-d', $argv[3] . '-01'), 'F');
      } else {
        $summary += intval($expense['amount']);
      }
    }
  }

  return "# Total expenses$forMonth: $$summary";
}

/**
 * Export expenses data to csv file
 */
function export(array $data): string
{
  if (!empty($data)) {
    $file = fopen('expenses.csv', 'w');
    fputcsv($file, array_keys($data[0]), ';');
    foreach ($data as $row) fputcsv($file, $row, ';');
    fclose($file);

    return "# Data has been exported to expenses.csv";
  }

  return "# Empty expenses data";
}

/*
  Guide for using PHP Expense Tracker CLI
*/
function help(): string
{
  $yellow = "\033[33m";
  $green = "\033[32m";
  $reset = "\033[0m";

  $help =
    $green . "\nPHP Expense Tracker CLI \n" . $reset . PHP_EOL .
    $yellow . 'Usage:' . $reset . PHP_EOL .
    "  php expense-tracker.php command <flag> <arguments>\n\n" .
    $yellow . "Available commands and arguments:" . $reset . PHP_EOL .
    '  set --budget <budget>                                                       Set monthly budget
  add --category <category> --description <description> --amount <amount>     Add new expense
  update --budget <budget>                                                    Update monthly budget
  update --id <id> --category <category>                                      Update expense category
  update --id <id> --description <description>                                Update expense description
  update --id <id> --amount <amount>                                          Update expense amount
  delete --id <id>                                                            Delete an expense
  list                                                                        List all expenses
  list --category <category>                                                  List expenses by category
  summary                                                                     View a summary of all expenses
  summary --month <month>                                                     View a summary of expenses for a specific month (of current year)
  export                                                                      Export expenses data into csv file
  ';

  return $help;
}
