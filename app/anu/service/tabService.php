<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 10:01
 */

namespace Anu;

class tabService extends baseService
{
    protected   $table = 'assets';
    protected   $primary_key = 'id';
    public      $tableName = 'assets';

    /**
     * @param $tabs
     * @param $record       baseRecord|entryRecord
     * @param $entryType
     */
    public function updateTabsForEntryType($tabs, $record, $entryType){
        // save tabs
        $oldTabs = anu()->field->getAllTabsForEntry($record, $entryType);
        $position = 1;
        $arrNewTabIds = array();
        foreach ($tabs as $tab){
            $tab['position'] = $position;
            unset($tab['fields']);
            if(!$tab['id']){
                anu()->database->insert('fieldlayouttabs', $tab);
                $tab['id'] = anu()->database->id();
            }else{
                anu()->database->update('fieldlayouttabs', $tab, array('id' => $tab['id']));
                anu()->database->debugError();
            }
            $position++;
            $arrNewTabIds[] = $tab['id'];
        }

        $tabsToDelete = array_filter($oldTabs, array(new InArrayFilter($arrNewTabIds), 'inArray'));

        if(is_array($tabsToDelete) && count($tabsToDelete)){
            $deleteIds = array();
            foreach ($tabsToDelete as $delTab){
                $deleteIds[] = $delTab['id'];
            }
            anu()->database->delete('fieldlayouttabs', array(
                'id' => $deleteIds
            ));
            anu()->database->debugError();
        }
        return $arrNewTabIds;
    }

}

class InArrayFilter{
    private $arrayIds;

    function __construct($arrayIds) {
        $this->arrayIds = $arrayIds;
    }

    function inArray($el) {
        return !in_array($el['id'], $this->arrayIds);
    }
}