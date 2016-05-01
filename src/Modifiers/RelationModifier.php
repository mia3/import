<?php
namespace MIA3\Import\Modifiers;

trait RelationModifier
{
    /**
     * @param $oldKey
     * @param $mappingName
     * @return mixed
     */
    public function updateForeignKey($oldKey, $mappingName) {
        if (is_array($oldKey)) {
            $keys = $oldKey;
            foreach ($keys as $index => $oldKey) {
                $keys[$index] = $this->mappings[$mappingName]->getPrimaryKeyValue($oldKey);
            }
            return $keys;
        }
        return $this->mappings[$mappingName]->getPrimaryKeyValue($oldKey);
    }
}