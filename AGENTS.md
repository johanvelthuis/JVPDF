# AGENTS Instructions

This project contains a PHP library with tests located under the `tests/` directory.

## Running the tests
1. Install dependencies with Composer:
   ```bash
   #composer install #this is already in the setup script
   ```
2. Execute the test suite:
   ```bash
   phpunit tests/UnitConverterTest.php
   ```
   Running this command should execute `tests/UnitConverterTest.php` along with any other tests in the folder.

## Coding style
- The code base uses tabs for indentation.
- New tests should be placed in the `tests/` directory following the existing naming conventions.

## Commit messages
- Keep commit messages concise and descriptive.
- Ensure tests pass before committing changes.
