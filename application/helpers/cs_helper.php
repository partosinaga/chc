<?php
    const RENT_BY_NIGHT = true;
    const INCOGNITO = 'Incognito';

    class UNIT_STATUS
    {
        const READY = 1;
        const NOT_READY = 2;
        const DIRTY = 3;
        const COMPLAIN = 4;
        const REPAIR = 5;

        function caption($status = 0){
            $result = '';

            if($status == UNIT_STATUS::NOT_READY){
                $result = 'Not Ready';
            }
            else if($status == UNIT_STATUS::DIRTY){
                $result = 'Dirty';
            }else if($status == UNIT_STATUS::COMPLAIN){
                $result = 'Complain';
            }else if($status == UNIT_STATUS::REPAIR){
                $result = 'Repair';
            }else {
                $result = 'Ready';
            }

            return $result;
        }
    }

    class BILLING_TYPE{
        const FULL_PAID = 10;
        const MONTHLY = 20;

        function caption($status = 0){
            $result = '';

            if($status == BILLING_TYPE::MONTHLY){
                $result = 'Monthly';
            }
            else if($status == BILLING_TYPE::FULL_PAID){
                $result = 'Full Payment';
            }

            return $result;
        }
    }

    class SRF_TYPE
    {
        const OUT_OF_SERVICE = 4;
        const OUT_OF_ORDER = 5;
		const MINOR_OUT_OF_ORDER = 0;

        function caption($status = 0){
            $result = '';
/*
            if($status == SRF_TYPE::OUT_OF_SERVICE){
                $result = HSK_STATUS::OS;
            }
            else if($status == SRF_TYPE::OUT_OF_ORDER){
                $result = HSK_STATUS::OO;
            }
		*/	
			 switch ($status) {
                case SRF_TYPE::OUT_OF_SERVICE:
                    $result = HSK_STATUS::OS;
                    break;
                case SRF_TYPE::OUT_OF_ORDER:
                     $result = HSK_STATUS::OO;
                    break;
                case SRF_TYPE::MINOR_OUT_OF_ORDER:
                     $result = HSK_STATUS::MO;
                    break;
                default:
                    $result = '';
                    break;
            }

            return $result;
        }
    }

    class ORDER_STATUS
    {
        const ONLINE_NEW = 21;
        const ONLINE_VALID = 22;
        const RESERVED = 77;
        const CHECKIN= 100;
        const CHECKOUT = 111;

        function get_status_name($status = 0, $is_badges = true){
            $result = '';

            if($status == ORDER_STATUS::ONLINE_NEW){
                if($is_badges){
                    $result = show_badge('badge-default', 'PENDING');
                }
                else {
                    $result = 'ONLINE PENDING';
                }
            }
            else if($status == ORDER_STATUS::ONLINE_VALID){
                if($is_badges){
                    $result = show_badge('badge-default', 'OL-RESERVED');
                }
                else {
                    $result = 'ONLINE RESERVED';
                }
            }
            else if($status == ORDER_STATUS::RESERVED){
                if($is_badges){
                    $result = show_badge('info', 'RESERVED');
                }
                else {
                    $result = 'RESERVED';
                }
            }
            else if($status == ORDER_STATUS::CHECKIN){
                if($is_badges){
                    $result = show_badge('bg-blue-hoki', 'CHECK-IN');
                }
                else {
                    $result = 'CHECK-IN';
                }
            }
            else if($status == ORDER_STATUS::CHECKOUT){
                if($is_badges){
                    $result = show_badge('bg-yellow-gold', 'CHECK-OUT');
                }
                else {
                    $result = 'CHECK-OUT';
                }
            }
            else if($status == STATUS_CANCEL){
                if($is_badges){
                    $result = show_badge('bg-red-thunderbird', 'CANCELED');
                }
                else {
                    $result = 'CANCELED';
                }
            }
            else if($status == STATUS_POSTED){
                if($is_badges){
                    $result = show_badge('bg-blue-steel', 'POSTED');
                }
                else {
                    $result = 'POSTED';
                }
            }
            else if($status == STATUS_CLOSED){
                if($is_badges){
                    $result = show_badge('bg-purple-plum', 'CLOSED');
                }
                else {
                    $result = 'CLOSED';
                }
            }
            else if($status == STATUS_DELETE){
                if($is_badges){
                    $result = show_badge('bg-red-thunderbird', 'DELETED');
                }
                else {
                    $result = 'DELETED';
                }
            }
            else if($status == STATUS_VIEW){
                if($is_badges){
                    $result = show_badge('badge-primary', 'VIEW');
                }
                else {
                    $result = 'VIEW';
                }
            }
            else if($status == STATUS_AUDIT){
                if($is_badges){
                    $result = show_badge('bg-grey-cascade', 'AUDIT');
                }
                else {
                    $result = 'AUDIT';
                }
            }
            else if($status == STATUS_PRINT){
                if($is_badges){
                    $result = show_badge('bg-grey-cascade', 'PRINT');
                }
                else {
                    $result = 'PRINT';
                }
            }

            return $result;
        }

        function code($status = 0){
            $result = '';

            if($status == ORDER_STATUS::ONLINE_NEW){
                $result = 'BK';
            }
            else if($status == ORDER_STATUS::ONLINE_VALID){
                $result = 'OR';
            }
            else if($status == ORDER_STATUS::RESERVED){
                $result = 'R';
            }
            else if($status == ORDER_STATUS::CHECKIN){
                $result = 'CI';
            }
            else if($status == ORDER_STATUS::CHECKOUT){
                $result = 'CO';
            }
            else if($status == STATUS_CANCEL){
                $result = 'CC';
            }

            return $result;
        }

        function css_icon($status = 0){
            $result = 'glyphicon ';

            if($status == ORDER_STATUS::ONLINE_NEW){
                $result .= ' glyphicon-screenshot';
            }
            else if($status == ORDER_STATUS::ONLINE_VALID){
                $result .= ' glyphicon-ok-sign';
            }
            else if($status == ORDER_STATUS::RESERVED){
                $result .= ' glyphicon-edit';
            }
            else if($status == ORDER_STATUS::CHECKIN){
                $result .= ' glyphicon-check';
            }
            else if($status == ORDER_STATUS::CHECKOUT){
                $result .= ' glyphicon-share';
            }

            return $result;
        }

        function vt_status_caption($status = '', $is_badges = true){
            $result = '';

            if(strtolower($status) == 'capture'){
                if($is_badges){
                    $result = show_badge('info', strtoupper($status));
                }
                else {
                    $result = strtoupper($status);
                }
            }
            else if(strtolower($status) == 'settlement'){
                if($is_badges){
                    $result = show_badge('bg-blue', strtoupper($status));
                }
                else {
                    $result = strtoupper($status);
                }
            }
            else if(strtolower($status) == 'authorize'){
                if($is_badges){
                    $result = show_badge('info', strtoupper($status));
                }
                else {
                    $result = strtoupper($status);
                }
            }else if(strtolower($status) == 'failed'){
                if($is_badges){
                    $result = show_badge('bg-red-thunderbird', strtoupper($status));
                }
                else {
                    $result = strtoupper($status);
                }
            }else if(strtolower($status) == 'deny'){
                if($is_badges){
                    $result = show_badge('bg-red', strtoupper($status));
                }
                else {
                    $result = strtoupper($status);
                }
            }
            else {
                if($status == ''){
                    $status = 'verifying';
                }
                if($is_badges){
                    $result = show_badge('bg-yellow', strtoupper($status));
                }
                else {
                    $result = strtoupper($status);
                }
            }

            return $result;
        }
    }

    class WIFI_ROUTER
    {
        const SERVER_IP = '192.168.100.1';
        const SERVER_USER = 'userapi';
        const SERVER_PASS = 'dwijaya@2015';
        const CUSTOMER = 'IT';
        const PROFILE_SUFFIX = 'hari';
    }

    class HSK_STATUS{
        const IS = 'IS';
        const ISC = 'ISC';
        const OD = 'OD';
        const OC = 'OC';
        const VD = 'VD';
        const VC = 'VC';
        const ED = 'ED';
        const ED_EA = 'ED/EA';
        const VD_EA = 'VD/EA';
        const VC_EA = 'VC/EA';
        const IS_EA = 'IS/EA';
        const OS = 'OS';
        const OO = 'OO';
        const MO = 'MO';

        function next_status($status = '')
        {
            switch ($status) {
                case HSK_STATUS::OD:
                    $next = HSK_STATUS::OC;
                    break;
                case HSK_STATUS::OC:
                    $next = HSK_STATUS::ISC;
                    break;
                case HSK_STATUS::VD:
                    $next = HSK_STATUS::VC;
                    break;
                case HSK_STATUS::VC:
                    $next = HSK_STATUS::IS;
                    break;
                case HSK_STATUS::VD_EA:
                    $next = HSK_STATUS::VC_EA;
                    break;
                case HSK_STATUS::VC_EA:
                    $next = HSK_STATUS::IS_EA;
                    break;
                default:
                    $next = '';
                    break;
            }

            return $next;
        }

        function hsk_class($status = '')
        {
            switch ($status) {
                case HSK_STATUS::OD:
                    $class = 'yellow-crusta';
                    break;
                case HSK_STATUS::OC:
                    $class = 'grey-steel';
                    break;
                case HSK_STATUS::VD:
                    $class = 'yellow-crusta';
                    break;
                case HSK_STATUS::VC:
                    $class = 'grey-steel';
                    break;
                case HSK_STATUS::VD_EA:
                    $class = 'red-thunderbird';
                    break;
                case HSK_STATUS::VC_EA:
                    $class = 'grey-steel';
                    break;
                case HSK_STATUS::OS:
                    $class = 'red-sunglo';
                    break;
                case HSK_STATUS::OO:
                    $class = 'red-sunglo';
                    break;
                default:
                    $class = 'grey-steel';
                    break;
            }

            return $class;
        }

        function caption($status = ''){
            switch ($status)
            {
                case HSK_STATUS::IS:
                    $result = 'Inspected';
                    break;
                case HSK_STATUS::ISC:
                    $result = 'Inspected - Check In';
                    break;
                case HSK_STATUS::OD:
                    $result = 'Occupied Dirty';
                    break;
                case HSK_STATUS::OC:
                    $result = 'Occupied Clean';
                    break;
                case HSK_STATUS::VD:
                    $result = 'Vacant Dirty';
                    break;
                case HSK_STATUS::VC:
                    $result = 'Vacant Clean';
                    break;
                case HSK_STATUS::ED:
                    $result = 'Expected Departure';
                    break;
                case HSK_STATUS::ED_EA:
                    $result = 'Expected Departure / Expected Arrival';
                    break;
                case HSK_STATUS::VD_EA:
                    $result = 'Vacant Dirty / Expected Arrival';
                    break;
                case HSK_STATUS::VC_EA:
                    $result = 'Vacant Clean / Expected Arrival';
                    break;
                case HSK_STATUS::IS_EA:
                    $result = 'Inspected Clean / Expected Arrival';
                    break;
                case HSK_STATUS::OS:
                    $result = 'Out Of Service';
                    break;
                case HSK_STATUS::OO:
                    $result = 'Out Of Order';
                    break;				
                case HSK_STATUS::MO:
                    $result = 'Minor Out Of Order';
                    break;
                default:
                    $result='';
                    break;
            }
            return $result;
        }

        function stat_to_idx($status = ''){
            switch ($status)
            {
                case HSK_STATUS::IS:
                    $result = 10;
                    break;
                case HSK_STATUS::ISC:
                    $result = 11;
                    break;
                case HSK_STATUS::OD:
                    $result = 20;
                    break;
                case HSK_STATUS::OC:
                    $result = 21;
                    break;
                case HSK_STATUS::VD:
                    $result = 25;
                    break;
                case HSK_STATUS::VC:
                    $result = 26;
                    break;
                case HSK_STATUS::ED:
                    $result = 30;
                    break;
                case HSK_STATUS::ED_EA:
                    $result = 31;
                    break;
                case HSK_STATUS::VD_EA:
                    $result = 40;
                    break;
                case HSK_STATUS::VC_EA:
                    $result = 41;
                    break;
                case HSK_STATUS::IS_EA:
                    $result = 50;
                    break;
                case HSK_STATUS::OS:
                    $result = 60;
                    break;
                case HSK_STATUS::OO:
                    $result = 61;
                    break;
                default:
                    $result=0;
                    break;
            }
            return $result;
        }

        function idx_to_stat($index = 0){
            switch ($index)
            {
                case 10:
                    $result = HSK_STATUS::IS;
                    break;
                case 11:
                    $result = HSK_STATUS::ISC;
                    break;
                case 20:
                    $result = HSK_STATUS::OD;
                    break;
                case 21:
                    $result = HSK_STATUS::OC;
                    break;
                case 25:
                    $result = HSK_STATUS::VD;
                    break;
                case 26:
                    $result = HSK_STATUS::VC;
                    break;
                case 30:
                    $result = HSK_STATUS::ED;
                    break;
                case 31:
                    $result = HSK_STATUS::ED_EA;
                    break;
                case 40:
                    $result = HSK_STATUS::VD_EA;
                    break;
                case 41:
                    $result = HSK_STATUS::VC_EA;
                    break;
                case 50:
                    $result = HSK_STATUS::IS_EA;
                    break;
                case 60:
                    $result = HSK_STATUS::OS;
                    break;
                case 61:
                    $result = HSK_STATUS::OO;
                    break;
                default:
                    $result= '';
                    break;
            }
            return $result;
        }
    }

    class GUEST_TYPE{
        const GUEST = 1;
        const HOUSE_USED = 2;
        const COMPLIMENT = 3;

        function caption($status = 0){
            $result = '';

            if($status == GUEST_TYPE::GUEST){
                $result = 'Guest';
            }
            else if($status == GUEST_TYPE::HOUSE_USED){
                $result = 'House Use';
            }
            else if($status == GUEST_TYPE::COMPLIMENT){
                $result = 'Compliment';
            }

            return $result;
        }
    }

    class GUEST_MEMBER_TYPE{
        const FAMILY = 1;
        const STAFF = 3;

        function id_caption($membertype_id = 0){
            $result = '';

            if($membertype_id == 1){
                $result = 'KTP';
            }
            else if($membertype_id == 2){
                $result = 'Passport';
            }
            else if($membertype_id == 3){
                $result = 'KITAS';
            }

            return $result;
        }
    }

    class RES_TYPE
    {
        const PERSONAL = 1;
        const CORPORATE = 2;
        const MEMBER = 3;
        const HOUSE_USE = 4;

        function caption($status = 0){
            $result = '';

            if($status == RES_TYPE::MEMBER){
                $result .= 'Member';
            }
            else if($status == RES_TYPE::CORPORATE){
                $result .= 'Corporate';
            }
            else if($status == RES_TYPE::HOUSE_USE){
                $result .= 'House Use';
            }
            else {
                $result .= 'Personal';
            }

            return $result;
        }

        function css_class($status = 0){
            $result = '';

            if($status == RES_TYPE::MEMBER){
                $result .= ' font-blue';
            }
            else if($status == RES_TYPE::CORPORATE){
                $result .= ' font-green-seagreen bold';
            }
            else if($status == RES_TYPE::HOUSE_USE){
                $result .= ' font-red-sunglo';
            }
            else {
                $result .= '';
            }

            return $result;
        }

        function css_icon($status = 0){
            $result = 'fa';

            if($status == RES_TYPE::MEMBER){
                $result .= ' fa-user font-blue';
            }
            else if($status == RES_TYPE::CORPORATE){
                $result .= ' fa-users font-green-seagreen';
            }
            else if($status == RES_TYPE::HOUSE_USE){
                $result .= ' fa-user font-red-sunglo';
            }
            else {
                $result .= ' fa-user';
            }

            return $result;
        }
    }
?>