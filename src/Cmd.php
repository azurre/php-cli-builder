<?php
/**
 * @author Alex Milenin
 * @email  admin@azrr.info
 * @copyright Copyright (c)Alex Milenin (https://azrr.info/)
 */

namespace Azurre\Component\Cli;
/**
 * Class Cmd
 */
class Cmd
{
    const ARG_PURE = 0;
    const ARG_OPTION = 1;
    const ARG_LONG_OPTION = 2;
    const ARG_PARAMETER = 3;
    const ARG_LONG_PARAMETER = 4;

    /** @var string|null */
    protected $executable;

    protected $escapeChar = '"';

    protected $arguments = [];

    public function __construct(string $executable = null)
    {
        $this->executable = $executable;
    }

    public function addOption(string $key, bool $isLong = false)
    {
        $this->arguments[] = [$isLong ? static::ARG_LONG_OPTION : static::ARG_OPTION, $key];
        return $this;
    }

    public function addLongOption(string $key)
    {
        return $this->addOption($key, static::ARG_LONG_OPTION);
    }

    public function addParameter(string $key, $value, bool $isLong = false)
    {
        $this->arguments[] = [$isLong ? static::ARG_LONG_PARAMETER : static::ARG_PARAMETER, $key, $value];
        return $this;
    }

    public function addLongParameter(string $key, $value)
    {
        return $this->addParameter($key, $value, static::ARG_LONG_OPTION);
    }

    public function addArgument(string $value)
    {
        $this->arguments[] = [static::ARG_PURE, $value];
        return $this;
    }

    public function getEscapeChar(): string
    {
        return $this->escapeChar;
    }

    public function setEscapeChar(string $escapeChar)
    {
        $this->escapeChar = $escapeChar;
        return $this;
    }

    protected function prepareParameterValue(string $value): string
    {
        if (strpos($value, ' ') !== false) {
            return "'$value'";
        }
        return $value;
    }

    public function __toString(): string
    {
        $cmd = [(string)$this->executable];
        foreach ($this->arguments as $argument) {
            switch ($argument[0]) {
                case static::ARG_PURE:
                    $cmd[] = $argument[1];
                    break;
                case static::ARG_OPTION:
                    $cmd[] = "-$argument[1]";
                    break;
                case static::ARG_LONG_OPTION:
                    $cmd[] = "--$argument[1]";
                    break;
                case static::ARG_PARAMETER:
                    $cmd[] = "-$argument[1]=" . $this->prepareParameterValue($argument[2]);
                    break;
                case static::ARG_LONG_PARAMETER:
                    $cmd[] = "--$argument[1]=" . $this->prepareParameterValue($argument[2]);
                    break;
            }
        }
        return implode(' ', $cmd);
    }
}
