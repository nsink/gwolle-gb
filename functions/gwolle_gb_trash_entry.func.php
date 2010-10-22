<?php
  /**
   * gwolle_gb_trash_entry
   * Moves an entry to trash.
   */
  if (!function_exists('gwolle_gb_trash_entry')) {
    function gwolle_gb_trash_entry($args=array()) {
      global $wpdb;
      if (!isset($args['entry_id']) || (int)$args['entry_id'] === 0) {
        return FALSE;
      }
      
      // Load settings, if not set
    	global $gwolle_gb_settings;
    	if (!isset($gwolle_gb_settings)) {
        include_once(GWOLLE_GB_DIR.'/functions/gwolle_gb_get_settings.func.php');
        gwolle_gb_get_settings();
      }
      
      $entry_isDeleted = (isset($args['untrash']) && $args['untrash'] === TRUE) ? 0 : 1;
      
      $sql = "
      UPDATE
        ".$wpdb->gwolle_gb_entries."
      SET
        entry_isDeleted = ".$entry_isDeleted."
      WHERE
        entry_id = ".(int)$args['entry_id']."
      LIMIT 1";
      $result = mysql_query($sql);
      if (mysql_affected_rows() == 1) {
        //  Write log entry
        include_once(GWOLLE_GB_DIR.'/functions/gwolle_gb_add_log_entry.func.php');
        $log = array();
        $log['subject']     = ($entry_isDeleted === 1) ? 'entry-trashed' : 'entry-untrashed';
        $log['subject_id']  = (int)$args['entry_id'];
        gwolle_gb_add_log_entry($log);
        return TRUE;
      }
      return FALSE;
    }
  }