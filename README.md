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

```php
use \Azurre\Component\Cli\Cmd;
$cmd = Cmd::create('/usr/bin/tar')
    ->addLongOption('create')
    ->addLongOption('dereference')
    ->addLongOption('absolute-names')
    ->addLongParameter('files-from', '/path/to/list')
    ->stdErrTo('/path/to/std-errors');
echo (string)$cmd;
```
Output:
```
/usr/bin/tar --create --dereference --absolute-names --files-from=/path/to/list 2>/path/to/std-errors 
```

```php
use \Azurre\Component\Cli\Cmd;
$cmd = Cmd::create('/usr/bin/7za')
    ->setParameterSeparator('')
    ->addOption('bd')
    ->addOption('mx7')
    ->addParameter('si', 'test.tar')
    ->addArgument('a')
    ->addArgument('/path/to/test.tar.7z')
    ->stdErrTo(Cmd::TO_STDOUT)
    ->stdOutTo(Cmd::TO_NULL);
echo (string)$cmd;
```

Output:
```
/usr/bin/7za -bd -mx7 -sitest.tar a /path/to/test.tar.7z 1>/dev/null 2>&1
```
