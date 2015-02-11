<?php


$dictionary=array(

'SET_UNSEEN'=>'Отметить как непросмотренное',
'RECORD_MESSAGE'=>'Записать сообщение'
/* end module names */


);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
