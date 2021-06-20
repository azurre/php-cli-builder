# Simple CLI commands builder

## Installation

```bash
composer require azurre/php-cli-builder
```

## Usage 
```php
require __DIR__ . '/vendor/autoload.php';
use \Azurre\Component\Cli\Cmd;
$cmd = new Cmd('/usr/bin/rsync');
$cmd
    ->addOption('avz')
    ->addLongParameter('password-file', '/path/to/password file')
    ->addArgument('/path/to/source')
    ->addArgument('rsync://user@host:/destination/path');

echo (string)$cmd;
```

Output:
```
/usr/bin/rsync -avz --password-file='/path/to/password file' /path/to/source rsync://user@host:/destination/path
```

