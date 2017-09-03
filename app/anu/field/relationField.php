<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:16
 */

namespace anu;


class relationField extends fieldService
{
    /**
     * @param $model        baseModel|entryModel
     * @param $attributes   array
     * @param $data         array
     * @param $key          string
     */
    public function onPopulate($model, $attributes, $data, $key)
    {
        if(isset($attributes['relatedTo']) && $relation = $attributes['relatedTo']){
            $relation['class'] = $model->class;
            $model->$key = $this->getBaseCriteriaModelForPopulatedEntry($model, $data, $relation, $key);
        }
        return $model;
    }


    public function onUpdate($entry, $key, $attributes, &$values, $oldEntry = null)
    {
        if(property_exists($entry, $key)){
            if(!isset($attributes['relatedTo'])){
                throw new \Exception("Error: missing relatedTo Attribute in " . $entry->class . " Service");
            }

            $relation = $attributes['relatedTo'];
            $relation['class'] = $entry->class;
            $relations = anu()->entry->getRelationsFromEntryByKey($entry, $key);
            if($relations){
                anu()->entry->updateRelations($entry, $key, $relation, $relations);
            }
        }
    }

    public function onInsert($entry, $key, $attributes, &$relationsToSave, &$values)
    {
        if (property_exists($entry, $key)) {
            if (!isset($attributes['relatedTo'])) {
                throw new Exception("Error: missing relatedTo Attribute in " . Anu::getClassName($this) . " Service");
            }
            $relations = anu()->entry->getRelationsFromEntryByKey($entry, $key);
            $relation = $attributes['relatedTo'];
            $relation['class'] = $entry->class;
            foreach ($relations as $rel) {
                $relationsToSave[] = anu()->entry->getRelationData($relation, $key, $rel);
            }
        }
    }

}