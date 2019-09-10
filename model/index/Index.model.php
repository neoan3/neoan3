<?php

namespace Neoan3\Model;

class IndexModel
{

    static function first($ask)
    {
        if (!empty($ask)) {
            return $ask[0];
        } else {
            return [];
        }
    }

    static function flatten($modelName, $deepModel)
    {
        $separate = [];
        foreach ($deepModel as $columnOrTable => $value) {
            if (is_array($value)) {
                $separate[$columnOrTable] = $value;
            } else {
                $separate[$modelName][$columnOrTable] = $value;
            }
        }
        return $separate;
    }


    /**
     * @param $obj
     * @param $transformer
     *
     * @return mixed
     * @throws \Exception
     */
    static function _create($obj, $transformer)
    {
        foreach ($transformer as $tableOrField => $info) {
            // if missing
            if (isset($info['required']) && $info['required'] && !isset($obj[$tableOrField])) {
                throw new \Exception('Missing: ' . $tableOrField);
            }
            // is table or field?
            if (isset($info['depth'])) {
                // table
                if (isset($info['required_fields'])) {
                    foreach ($info['required_fields'] as $field) {
                        if ($info['depth'] == 'one') {
                            if (!isset($obj[$tableOrField][$field])) {
                                throw new \Exception('Missing or malformed: ' . $field);
                            }
                        } else {
                            if ($info['depth'] == 'many') {
                                if (empty($obj[$tableOrField])) {
                                    throw new \Exception('Missing or malformed: ' . $field);
                                }
                                foreach ($obj[$tableOrField] as $oneInMany) {
                                    if (!isset($oneInMany[$field])) {
                                        throw new \Exception('Missing or malformed: ' . $field);
                                    }
                                }
                            }
                        }
                    }
                }
                if (isset($info['on_creation'])) {
                    foreach ($info['on_creation'] as $field => $transform) {
                        if ($info['depth'] == 'one') {
                            $value = isset($obj[$tableOrField][$field]) ? $obj[$tableOrField][$field] : false;
                            $obj[$tableOrField][$field] = $transform($value);
                        } else {
                            foreach ($obj[$tableOrField] as $i => $oneInMany) {
                                $value = isset($oneInMany[$field]) ? $oneInMany[$field] : false;
                                $obj[$tableOrField][$i][$field] = $transform($value);
                            }
                        }
                    }
                }
            } else {
                // value
                if (isset($info['on_creation'])) {
                    $obj[$tableOrField] =
                        $info['on_creation'](isset($obj[$tableOrField]) ? $obj[$tableOrField] : false);
                }
            }
            // translate?
            if (isset($info['translate']) && $info['translate']) {
                $obj[$info['translate']] = $obj[$tableOrField];
                unset($obj[$tableOrField]);
            }
        }

        return $obj;
    }

    static function getMigrateStructure($model)
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . $model . DIRECTORY_SEPARATOR . 'migrate.json';
        $structure = [];
        if (file_exists($path)) {
            $pattern = json_decode(file_get_contents($path), true);
            foreach ($pattern as $table => $fields) {
                if ($table !== $model) {
                    $structure[$table] = $fields;
                } else {
                    foreach ($fields as $key => $field) {
                        $structure[$key] = $field;
                    }
                }
            }
            return $structure;
        } else {
            throw new \Exception('no migrate json found');
        }
    }
}
