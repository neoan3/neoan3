<?php

namespace Neoan3\Component\Migrate;

use Neoan3\Core\Serve;
use Neoan3\Provider\FileSystem\File;
use Neoan3\Provider\FileSystem\Native;

class GenerateWrapper extends Serve
{
    private Native $fileSystem;
    private string $modelName;
    private array $subTables;
    private array $mainTable;

    function __construct(string $modelName, Native $fileSystem = null)
    {
        parent::__construct();
        $this->modelName = ucfirst($modelName);
        $this->fileSystem = $this->assignProvider('file', $fileSystem, function () {
            return new File();
        });
        $migration = $this->fileSystem->getContents(path . '/model/' . $this->modelName . '/migrate.json');
        $this->subTables = json_decode($migration, true);
        $this->mainTable = array_shift($this->subTables);
    }

    private function transformName($propertyName): string
    {
        return str_replace('_', '', ucwords($propertyName, '_'));
    }

    private function generateHead(): string
    {
        return implode("\n", [
            "<?php\nnamespace Neoan3\\Model\\{$this->modelName};\n",
            "use Neoan3\Provider\Model\ModelWrapper;",
            "use Neoan3\Provider\Model\ModelWrapperTrait;\n",
            "class {$this->modelName}ModelWrapper extends {$this->modelName}Model implements ModelWrapper\n{",
            "\tuse ModelWrapperTrait;\n\n"
        ]);
    }

    private function generateProperties(): string
    {
        $lines = [];
        // main
        foreach ($this->mainTable as $name => $definition) {
            $definition['type'] = preg_replace('/\([0-9]+\)/', '', $definition['type']);
            $type = 'string';
            if (in_array($definition['type'], ['boolean', 'tinyint', 'int'])) {
                $type = 'int';
            }
            if ($definition['key'] === 'primary' || $definition['nullable']) {
                $type = '?' . $type;
            }
            $lines[] = "\tprivate $type \$" . $name . ($definition['nullable'] && !$definition['default'] ? ' = null;' : ';') . "";
        }
        // sub
        foreach ($this->subTables as $tableName => $properties) {
            $lines[] = "\tprivate array \$$tableName = [];";
        }
        return implode("\n", $lines) ."\n\n";
    }

    private function generateSettersAndGetters(): string
    {
        $lines = [];
        foreach ($this->mainTable as $name => $definition) {
            $namePart = $this->transformName($name);
            $lines[] = "\tpublic function get$namePart(): mixed\n\t{";
            $lines[] = "\t\treturn \$this->$name;\n\t}\n";
            $lines[] = "\tpublic function set$namePart(\$input): static\n\t{";
            $lines[] = "\t\t\$this->$name = \$input;";
            $lines[] = "\t\treturn \$this;\n\t}\n";
        }
        foreach ($this->subTables as $tableName => $properties) {
            $namePart = $this->transformName($tableName);
            $lines[] = "\tpublic function get$namePart(): array\n\t{";
            $lines[] = "\t\treturn \$this->$tableName;\n\t}\n";
            $lines[] = "\tpublic function add$namePart(array \$newSub): static\n\t{";
            $lines[] = "\t\t\$this->$tableName" . "[] = \$newSub;";
            $lines[] = "\t\treturn \$this;\n\t}\n";

            $lines[] = "\tpublic function remove$namePart(string \$id): static\n\t{";
            $lines[] = "\t\tforeach (\$this->$tableName as \$i => \$any){";
            $lines[] = "\t\t\tif(\$any['id'] === \$id){";
            $lines[] = "\t\t\t\t\$this->$tableName" . "[\$i]['delete_date'] = null;\n\t\t\t}\n\t\t}";
            $lines[] = "\t\treturn \$this;\n\t}\n";

        }
        return implode("\n", $lines) . "\n";
    }

    function generate(): void
    {
        $class = $this->generateHead();
        $class .= $this->generateProperties();
        $class .= $this->generateSettersAndGetters() . '}';
        $this->fileSystem->putContents(path . '/model/' . $this->modelName . '/' . $this->modelName . 'ModelWrapper.php', $class);

    }
}