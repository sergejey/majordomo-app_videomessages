<?php
/**
* App_quotes 
*
* App_quotes
*
* @package project
* @author Serge J. <jey@tut.by>
* @copyright http://www.atmatic.eu/ (c)
* @version 0.1 (wizard, 13:02:00 [Feb 09, 2013])
*/
//
//
class app_videomessages extends module {
/**
* app_quotes
*
* Module class constructor
*
* @access private
*/
function app_videomessages() {
  $this->name="app_videomessages";
  $this->title="Video Messages";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  $this->admin($out);

  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  if ($this->single_rec) {
   $out['SINGLE_REC']=1;
  }
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}

/**
* Title
*
* Description
*
* @access public
*/
 function checkNewVideos() {
  $tmp=SQLSelectOne("SELECT COUNT(*) as TOTAL FROM app_videomessages WHERE IS_NEW=1");
  setGlobal('ThisComputer.NewVideoMessages', $tmp['TOTAL']);
  return $tmp['TOTAL'];
 }

/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='app_quotes' || $this->data_source=='') {
  global $id;

  if (!$id) {
   $tmp=$this->checkNewVideos();
   if ($tmp==1) {
    $id=current(SQLSelectOne("SELECT ID FROM app_videomessages WHERE IS_NEW=1"));
   }
  }

  if ($this->view_mode=='' || $this->view_mode=='search_app_quotes') {
   //TO-DO
   global $uploaded_file;

   if ($uploaded_file!='') {
    DebMes('file uploaded: '.$uploaded_file);
    global $uploaded_file_name;
    $dest_dir=ROOT.'cms/videomessages';
    if (!is_dir($dest_dir)) {
     mkdir($dest_dir, 0777);
    }
    $dest_file=time().'_'.$uploaded_file_name;
    @copy($uploaded_file, $dest_dir.'/'.$dest_file);
    $rec=array();
    $rec['FILE']=$dest_file;
    $rec['IS_NEW']=1;
    $rec['ADDED']=date('Y-m-d H:i:s');
    $rec['ID']=SQLInsert('app_videomessages', $rec);
    $this->checkNewVideos();
   } elseif (!$id) {

    $messages=SQLSelect("SELECT * FROM app_videomessages ORDER BY IS_NEW DESC, ADDED DESC");
    if (count($messages)) {
     $out['MESSAGES']=$messages;
    }

   } elseif ($id) {

    $rec=SQLSelectOne("SELECT * FROM app_videomessages WHERE ID='".(int)$id."'");
    if ($rec['IS_NEW']) {
     $rec['IS_NEW']=0;
     SQLUpdate('app_videomessages', $rec);
     $this->checkNewVideos();
    }
    outHash($rec, $out);

   }



  }
  if ($this->view_mode=='set_unseen') {
   SQLExec("UPDATE app_videomessages SET IS_NEW=1 WHERE ID=".(int)$id);
   $this->checkNewVideos();
   $this->redirect("?");
  }

  if ($this->view_mode=='delete_app_videomessages') {
   $this->delete_app_videomessages($this->id);
   $this->checkNewVideos();
   $this->redirect("?");
  }
 }
}

/**
* app_quotes edit/add
*
* @access public
*/
 function edit_app_quotes(&$out, $id) {
  require(DIR_MODULES.$this->name.'/app_quotes_edit.inc.php');
 }
/**
* app_quotes delete record
*
* @access public
*/
 function delete_app_videomessages($id) {
  $rec=SQLSelectOne("SELECT * FROM app_videomessages WHERE ID='$id'");
  @unlink(ROOT.'cms/videomessages/'.$rec['FILE']);
  // some action for related tables
  SQLExec("DELETE FROM app_videomessages WHERE ID='".$rec['ID']."'");
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS app_videomessages');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
app_quotes - Quotes
*/
  $data = <<<EOD
 app_videomessages: ID int(10) unsigned NOT NULL auto_increment
 app_videomessages: FILE varchar(255) NOT NULL default ''
 app_videomessages: IS_NEW tinyint(3) NOT NULL default '0'
 app_videomessages: ADDED datetime
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgRmViIDA5LCAyMDEzIHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
?>