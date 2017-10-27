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
            if($entry->position === $oldEntry->position){
                //return true;
            }
            echo "<pre>";
            var_dump($entry->$key);
            echo "</pre>";
            die();
            $oldPosition = $oldEntry->position;
            $position = $entry->$key;
            $values[$key] = $position;
            $record = Anu::getRecordByName($entry->class);
            $criteria = new elementCriteriaModel($record);
            $childrenIds = $criteria->find([
                'relatedTo' => array(
                    'field' => "child",
                    'model' => $entry->class,
                    'id' => $entry->id
                )
            ], true);
            $movedIds = $childrenIds;
            $movedIds[] = $entry->id;
            $childPosition = $position++;
            if(is_array($childrenIds) && count($childrenIds)){
                foreach($childrenIds as $childId){
                    anu()->database->update($record->tableName, array($key => $childPosition), array($record->primary_key => $childId));
                    echo anu()->database->last() . "<br>";
                    $childPosition++;
                }
            }
            $movedPositions = count($movedIds);
            $where = array(
                $record->primary_key . "[!]" => $movedIds
            );
            if($oldPosition > $position){
                $where[$key . "[<=]"] = $oldPosition;
                $where[$key . "[>=]"] = $position;
                anu()->database->update($record->tableName, array("#" . $key => "$key+$movedPositions"), $where);
            }else{
                $where[$key . "[<=]"] = $position;
                $where[$key . "[>=]"] = $oldPosition;
                anu()->database->update($record->tableName, array("#" . $key => "$key-$movedPositions"), $where);
            }
            echo anu()->database->last() . "<br>";

            return true;
        }
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

    public function onInstall($record, $field){
        anu()->database->alterTableAddColumn($record->tableName, $field->slug, "INT NULL default '0'");
    }

}