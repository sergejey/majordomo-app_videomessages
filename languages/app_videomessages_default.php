<?php


$dictionary=array(

'SET_UNSEEN'=>'Set as unseen',
'RECORD_MESSAGE'=>'Record message'
/* end module names */


);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
