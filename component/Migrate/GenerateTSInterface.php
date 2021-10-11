<?php

namespace Neoan3\Component\Migrate;


use Neoan3\Provider\FileSystem\Native;

class GenerateTSInterface
{
    private array $migration;
    private array $tables = [];
    public function __construct($migration)
    {
        $this->migration = $migration;
        foreach ($this->migration as $table => $any){
            $this->tables[] = $table;
        }

    }
    private function generateHead($table): string
    {
        return 'interface ' . ucfirst($table) . "{\n\t";
    }
    private function generateType($name, $item): string
    {
        switch (preg_replace('/\([0-9]+\)/', '', $item['type'])) {
            case 'binary':
                return ($item['key'] === 'primary' ? 'readonly ':'') . $name . "?: string,\n\t";
            case 'timestamp':
            case 'datetime':
                return "readonly " . $name . "_st: number,\n\t" .
                    $name . ($item['nullable'] ? '?' : '') . ": string,\n\t";
            case 'varchar':
            case 'text':
                return $name . ($item['nullable'] ? '?' : '') . ": string,\n\t";
            default:
                return $name . ($item['nullable'] ? '?' : '') . ": number,\n\t";

        }
    }
    private function includeSubs(&$interface)
    {
        $subs = array_slice($this->tables,1);
        foreach ($subs as $sub){
            $interface[] = $sub .': Array<' . ucfirst($sub) . ">,\n\t";
        }
    }
    public function generate()
    {
        $string = '';
        $i = 0;
        foreach ($this->migration as $table => $desc) {
            $interface = [$this->generateHead($table)];
            foreach ($desc as $name => $item) {
                $interface[] = $this->generateType($name, $item);
            }
            if($i == 0){
                $this->includeSubs($interface);
            }
            $string .= substr(implode('',$interface),0,-1) . "}\n";
            $i++;
        }
        $tables = array_map(function ($val){ return ucfirst($val);}, $this->tables);
        $string .= "\nexport {" . implode(', ', $tables)  . "}";
        return $string;
    }
}