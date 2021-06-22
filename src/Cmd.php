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
    const TO_NULL = '/dev/null';
    const TO_STDOUT = '&1';

    const ARG_PURE = 0;
    const ARG_OPTION = 1;
    const ARG_LONG_OPTION = 2;
    const ARG_PARAMETER = 3;
    const ARG_LONG_PARAMETER = 4;

    /** @var string|null */
    protected $executable;
    protected $stdOut = '';
    protected $stdErr = '';
    protected $escapeChar = '"';
    protected $parameterSeparator = ['=', '='];
    protected $arguments = [];

    public function __construct(string $executable = null)
    {
        $this->executable = $executable;
    }

    public static function create(...$args)
    {
        return new static(...$args);
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

    public function stdOutTo(string $to, bool $append = false)
    {
        $this->stdOut = [$to, $append];
        return $this;
    }

    public function stdErrTo(string $to, bool $append = false)
    {
        $this->stdErr = [$to, $append];
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

    public function getParameterSeparator(): array
    {
        return $this->parameterSeparator;
    }

    /**
     * @param string|array $separator
     * @return $this
     */
    public function setParameterSeparator($separator, $longSeparator = null)
    {
        if (is_array($separator) && count($separator) === 2) {
            $this->parameterSeparator = $separator;
        } elseif (is_string($separator)) {
            $longSeparator = is_string($longSeparator) ? $longSeparator : $separator;
            $this->parameterSeparator[0] = $longSeparator;
            $this->parameterSeparator[1] = $separator;
        } else {
            throw new \InvalidArgumentException('Invalid separator format');
        }
        return $this;
    }

    public function getParameterShortSeparator(): string
    {
        return $this->parameterSeparator[1];
    }

    /**
     * @param string $separator
     * @return $this
     */
    public function setParameterShortSeparator(string $separator)
    {
        $this->parameterSeparator[1] = $separator;
        return $this;
    }

    public function getParameterLongSeparator(): string
    {
        return $this->parameterSeparator[0];
    }

    /**
     * @param string $separator
     * @return $this
     */
    public function setParameterLongSeparator(string $separator)
    {
        $this->parameterSeparator[0] = $separator;
        return $this;
    }

    protected function prepareParameterValue(array $param): string
    {
        list($type, $key, $value) = $param;
        $short = $type === static::ARG_PARAMETER;
        if (strpos($value, ' ') !== false) {
            $value = "'$value'";
        }
        return '-' . ($short ? '' : '-') . $key . $this->parameterSeparator[(int)$short] . $value;
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
                case static::ARG_LONG_PARAMETER:
                    $cmd[] = $this->prepareParameterValue($argument);
                    break;
            }
        }
        if ($this->stdOut) {
            $append = $this->stdOut[1] ? '>>' : '>';
            $cmd[] = "1{$append}{$this->stdOut[0]}";
        }
        if ($this->stdErr) {
            $append = $this->stdErr[1] ? '>>' : '>';
            $cmd[] = "2{$append}{$this->stdErr[0]}";
        }
        return implode(' ', $cmd);
    }
}
