# PHP Expense Tracker
> Simple expense tracker CLI (Command Line Interface) App built with PHP.

## Table of Contents
* [General Info](#general-information)
* [Technologies Used](#technologies-used)
* [Features](#features)
* [Setup](#setup)
* [Usage](#usage)
* [Project Status](#project-status)
* [Acknowledgements](#acknowledgements)

## General Information
PHP Expense Tracker is a simple CLI (Command Line Interface) application that allows users to manage their finances. This project is designed to explore and practice logic building, working with the Command Line Interface (CLI), and filesystem operations in PHP.

## Technologies Used
- PHP - version 8.3.6

## Features
- **Set Budget**: Set montly budget for expenses.
- **Add a New Expense**: Create a new expense with a category, description, and amount.
- **List All Expense**: Display a list of all expense with their details.
- **List Tasks By Category**: Display a list of all tasks by Category.
- **Update an Expense**: Modify the category, description, or amount of an existing task.
- **Update Budget**: Modify montly budget for expenses.
- **Delete an Expense**: Remove an expense from the list.
- **View a Summary**: View a summary of all expenses or for a specific month (of current year).
- **Export Into CSV**: Export expenses data into a csv file.

## Setup
To run this CLI tool, you’ll need:
- **PHP**: Version 8.3 or newer

How to install:
1. Clone the repository
   ```bash
   git clone https://github.com/krisnaajiep/php-expense-tracker-cli.git
   ```

2. Change the current working directory
   ```bash
   cd path/php-expense-tracker-cli
   ```

3. Run the task tracker
   ```bash
   php expense-tracker.php
   ```

## Usage
`php expense-tracker.php <command> [options] <arguments>`

Available commands, options, and arguments:
| Commands  | Options                               | Description                                                        |
| --------- | ------------------------------------- | ------------------------------------------------------------------ |
| `set`     | `--budget`                            | Set monthly budget                                                 |
| `show`    | `--budget`                            | Show montly budget                                                 |
| `add`     | `--category, --description, --amount` | Add new expense                                                    |
| `update`  | `--id, --category`                    | Update expense category                                            |
| `update`  | `--id, --description`                 | Update expense description                                         |
| `update`  | `--id, --amount`                      | Update expense amount                                              |
| `delete`  | `--id`                                | Deleting an expense                                                |
| `list`    |                                       | List all expenses                                                  |
| `list`    | `--category`                          | Listing all expense by category                                    |
| `summary` |                                       | View a summary of all expenses                                     |
| `summary` | `--month`                             | View a summary of expenses for a specific month (of current year)  |
| `export`  |                                       | Export expenses data into csv file                                 |

Example:
- Set montly budget
  ```bash
  php expense-tracker.php set --budget 2000
  ```

- Adding a new expense
  ```bash
  php expense-tracker.php add --category"Food" --description "Lunch" --amount 20
  ```

- Updating an expense
  ```bash
  php expense-tracker.php update --id 1 --category "Electronic"
  ```
  ```bash
  php expense-tracker.php update --id 1 --description "Radio"
  ```
  ```bash
  php expense-tracker.php update --id 1 --amount 60
  ```

- Deleting an expense
  ```bash
  php expense-tracker.php delete --id 1
  ```

- Listing all expenses
  ```bash
  php expense-tracker.php list
  ```

- Listing all expenses by specific category
  ```bash
  php expense-tracker.php list --category "Electronic"
  ```

- View a summary of all expenses
  ```bash
  php expense-tracker.php summary
  ```

- Listing all expenses by specific category
  ```bash
  php expense-tracker.php summary --month 6
  ```

- Export data into csv file
  ```bash
  php expense-tracker.php export
  ```

## Project Status
Project is: _complete_.

## Acknowledgements
This project was inspired by [roadmap.sh](https://roadmap.sh/projects/expense-tracker).
