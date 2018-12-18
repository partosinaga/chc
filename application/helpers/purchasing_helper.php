<?php

///PURCHASING RELATED

class Purchasing
{
    const ITEM_MATERIAL = 1;
    const ITEM_SERVICE = 2;
    const DIRECT_PURCHASE = '999999';
    const PROJECT_DWIJAYA = 'DWJ';
    const PROJECT_MIT = 'MIT';

    const DEPT_FIN = 'FIN';
    const DEPT_PRC = 'PRC';
    const DEPT_GEN = 'GEN';

    const CURR_IDR = 'IDR';

    /*COA Class Type*/
    function item_type_name($item_type = 0){
        $result = '';

        switch ($item_type)
        {
            case Purchasing::ITEM_MATERIAL:
                $result = 'Material';
                break;
            case Purchasing::ITEM_SERVICE:
                $result = 'Service';
                break;
            default:
                exit;
        }
        return $result;
    }
}

class AP
{
    const INV_TYPE_GRN = 1;
    const INV_TYPE_AC = 2;

    const NO_TAX = 'NT';
}

function decimal_curr($curr = '') {
    if ($curr == Purchasing::CURR_IDR) {
        $result = 0;
    } else {
        $result = 2;
    }
    return $result;
}

?>