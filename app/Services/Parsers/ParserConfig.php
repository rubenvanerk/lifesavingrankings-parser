<?php

namespace App\Services\Parsers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Arr;

class ParserConfig
{
    private $fileName;
    private $resultsFile;
    private $config;
    public $template;

    private const FILE_EXTENSION_TEMPLATE_MAPPINGS = [
        'pdf' => 'text_template.yaml',
        'lxf' => 'lenex_template.yaml',
    ];

    public function __construct($resultsFileName)
    {
        $this->resultsFile = $resultsFileName;
        $this->fileName = $resultsFileName . '.yaml';
        $this->loadConfig();
        $this->template = Yaml::parse(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'parser_template.yaml'));
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

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return Arr::get($this->config, $name);
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
        Arr::set($this->config, $name, $value);
    }

    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    public function save(): void
    {
        Storage::put($this->fileName, Yaml::dump($this->config));
    }

    public function remove($name): void
    {
        Arr::forget($this->template, $name);
    }

    public function getType($name): ?string
    {
        return Arr::get($this->template, $name . '.type');
    }

    public function getLabel($name): ?string
    {
        $label = Arr::get($this->template, $name . '.label');
        if (!$label) {
            $name = Str::afterLast($name, '.');
            $label = ucfirst(str_replace('_', ' ', $name));
        }
        return $label;
    }

    public function getOptions($name): ?array
    {
        return Arr::get($this->template, $name . '.options');
    }

    public function getValueIfCustom($name): ?string
    {
        $value = $this->$name;
        return $this->valueIsCustom($name) ? $value : null;
    }

    public function valueIsCustom($name): bool
    {
        $value = $this->$name;
        $options = array_keys($this->getOptions($name));
        return !empty($value) && !is_array($value) && !in_array($value, $options, false);
    }

    public function allowCustom($name): bool
    {
        $allowCustom = Arr::get($this->template, $name . '.custom');
        return is_null($allowCustom) || $allowCustom;
    }

    public function getTextAreaAsArray($name): array
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
