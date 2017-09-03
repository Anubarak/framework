<?php
/**
 * Created by PhpStorm.
 * User: anuba
 * Date: 03.09.2017
 * Time: 14:16
 */

namespace anu;


class positionField extends fieldService
{
    /**
     * @param $model        baseModel|entryModel
     * @param $attributes   array
     * @param $data         array
     * @param $key          string
     */
    public function onPopulate($model, $attributes, $data, $key)
    {

    }


    /**
     * @param $entry
     * @param $key
     * @param $attributes
     * @param $values
     * @param null $oldEntry
     */
    public function onUpdate($entry, $key, $attributes, &$values, $oldEntry = null)
    {
        if(property_exists($entry, $key)){
            $relatedField = array_key_exists('relatedField', $attributes)? $attributes['relatedField'] : null;
            //no position change -> increase value by one if there is a relation change
            if($entry->$key === null){
                //save from edit field -> no direct position change by user
                if($relatedField){
                    if($oldEntry->$relatedField->getStoredIds() != $entry->$relatedField){
                        //just a relation change -> set position = last
                        if((is_array($entry->$relatedField) && count($entry->$relatedField)) || ($entry->$relatedField instanceof elementCriteriaModel && $ids = $entry->$relatedField->ids())){
                            if($ids){
                                $relationId = $ids[0];
                            }else{
                                $relationId = $entry->$relatedField[0];
                            }
                            $relField = $attributes[$relatedField]['relatedTo']['field'];
                        }else{
                            $relationId = 'nothing';
                            $relField = $relatedField;
                        }

                        $position = anu()->entry->setNewPosition($key, $relField, $relationId, $entry->id);
                        $entry->$key = $position;
                        //save old Values to fill the empty one
                        //old Parent
                        $entry->oldIds = $oldEntry->$relatedField->getStoredIds();

                        $entry->oldPosition = $oldEntry->$key;
                        anu()->entry->changePositions($entry, $key, $relatedField);
                    }else{
                        $entry->$key = $oldEntry->$key;
                    }
                }else{
                    $entry->$key = $oldEntry->$key;
                }
            }else{
                //save from list -> position changes every time
                anu()->entry->changePositions($entry, $key, $relatedField);
            }
        }
        $values[$key] = $entry->$key;
    }

    public function onInsert($entry, $key, $attributes, &$relationsToSave, &$values)
    {
        $relatedField = (array_key_exists('relatedField', $attributes))? $attributes['relatedField'] : 'nothing';
        if($relatedField !== 'nothing') {
            if(!$entry->$relatedField || count($entry->$relatedField) == 0) {
                //no relation.. just get last one and increase by one...
                $position = anu()->entry->setNewPosition($key, $relatedField);
                $entry->$key = $position;
            }else {
                $className = $entry->class;
                $parent = anu()->$className->getEntryById($entry->$relatedField[0]);
                $entry->$key = $parent->$key + 1;
            }
        }else{
            $criteria = new elementCriteriaModel($this);
            $criteria->ORDER = $key;
            $criteria->enabled = 'all';
            $last = $criteria->last();
            $entry->$key = $last->$key + 1;
        }

        $values[$key] = $entry->$key;
    }

}