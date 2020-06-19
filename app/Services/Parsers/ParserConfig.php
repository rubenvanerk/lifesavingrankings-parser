<?php

namespace App\Services\Parsers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Arr;

class ParserConfig
{
    /** @var string  */
    private $fileName;
    /** @var string */
    private $resultsFile;
    /** @var array */
    private $config;
    /** @var array */
    public $template;

    private const FILE_EXTENSION_TEMPLATE_MAPPINGS = [
        'pdf' => 'text_template.yaml',
        'lxf' => 'lenex_template.yaml',
    ];

    public function __construct(string $resultsFileName)
    {
        $this->resultsFile = $resultsFileName;
        $this->fileName = $resultsFileName . '.yaml';
        $this->loadConfig();
        $this->template = Yaml::parse(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'parser_template.yaml')) ?: [];
    }

    private function loadConfig(): void
    {
        $fileExtension = pathinfo($this->resultsFile, PATHINFO_EXTENSION);
        $empty = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . self::FILE_EXTENSION_TEMPLATE_MAPPINGS[$fileExtension]);
        if (Storage::missing($this->fileName)) {
            Storage::put($this->fileName, $empty);
        }
        $this->config = array_replace_recursive(Yaml::parse($empty), Yaml::parse(Storage::get($this->fileName)));
    }

    public function __get(string $name): string
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return Arr::get($this->config, $name);
    }

    public function __set(string $name, string $value): void
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
        Arr::set($this->config, $name, $value);
    }

    public function __isset(string $name): bool
    {
        return isset($this->config[$name]);
    }

    public function save(): void
    {
        Storage::put($this->fileName, Yaml::dump($this->config));
    }

    public function remove(string $name): void
    {
        Arr::forget($this->template, $name);
    }

    public function getType(string $name): ?string
    {
        return Arr::get($this->template, $name . '.type');
    }

    public function getLabel(string $name): ?string
    {
        $label = Arr::get($this->template, $name . '.label');
        if (!$label) {
            $name = Str::afterLast($name, '.');
            $label = ucfirst(str_replace('_', ' ', $name));
        }
        return $label;
    }

    public function getOptions(string $name): ?array
    {
        return Arr::get($this->template, $name . '.options');
    }

    public function getValueIfCustom(string $name): ?string
    {
        $value = $this->$name;
        return $this->valueIsCustom($name) ? $value : null;
    }

    public function valueIsCustom(string $name): bool
    {
        $value = $this->$name;
        $options = array_keys($this->getOptions($name));
        return !empty($value) && !is_array($value) && !in_array($value, $options, false);
    }

    public function allowCustom(string $name): bool
    {
        $allowCustom = Arr::get($this->template, $name . '.custom');
        return is_null($allowCustom) || $allowCustom;
    }

    public function getTextAreaAsArray(string $name): array
    {
        $fieldValue = $this->{$name};
        $lines = explode("\n", $fieldValue);
        $lines = preg_replace("/\r$/", '', $lines);
        $result = [];
        foreach ($lines as $line) {
            $keyValue = explode('>>', $line);
            $result[array_shift($keyValue)] = array_shift($keyValue);
        }
        return $result;
    }
}
