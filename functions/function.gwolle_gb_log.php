<?php

/*
 * gwolle_gb_add_log_entry()
 * Add a new log entry
 *
 * Parameters:
 *   - subject_id: (int)    the id of the entry
 *   - subject:    (string) one of the possible log_messages
 *
 * Return: (bool) true or false, depending on succes
 */

function gwolle_gb_add_log_entry( $subject_id, $subject ) {
	global $wpdb;

	if ( !isset($subject) || !isset($subject_id) || (int) $subject_id === 0 ) {
		return false;
	}

	$log_messages = array(
		'entry-unchecked',
		'entry-checked',
		'marked-as-spam',
		'marked-as-not-spam',
		'entry-edited',
		'imported-from-dmsguestbook',
		'entry-trashed',
		'entry-untrashed'
	);
	if ( !in_array( $subject, $log_messages ) ) {
		return false;
	}

	$result = $wpdb->query( $wpdb->prepare(
		"
		INSERT INTO $wpdb->gwolle_gb_log
		(
			log_subject,
			log_subjectId,
			log_authorId,
			log_date
		) VALUES (
			%s,
			%d,
			%d,
			%s
		)
		",
		array(
			addslashes( $subject ),
			intval( $subject_id ),
			intval( get_current_user_id() ),
			current_time( 'timestamp' )
		)
	) );

	if ($result == 1) {
		return true;
	}
	return false;
}


/*
 * gwolle_gb_get_log_entries
 * Function to get log entries.
 *
 * Parameter: (string) $subject_id: the id of the guestbook entry where the log belongs to
 *
 * Return: Array with an Array of log_entries
 * id           => (int) id
 * subject      => (string) subject of the log, what happened
 * author_id    => (int) author_id of the user responsible for this log entry
 * log_date     => (string) log_date with timestamp
 * msg          => (string) subject of the log, what happened. In Human Readable form, translated
 * author_login => (string) display_name or login_name of the user as standard WP_User
 * msg_html     => (string) string of html-text ready for displayed
 *
 */

function gwolle_gb_get_log_entries( $subject_id ) {
	global $wpdb;

	if ( !isset($subject_id) || (int) $subject_id === 0 ) {
		return false;
	}

	//  Message to strings
	$log_messages = array(
		'entry-unchecked'             => __('Entry has been locked.',    GWOLLE_GB_TEXTDOMAIN),
		'entry-checked'               => __('Entry has been checked.',   GWOLLE_GB_TEXTDOMAIN),
		'marked-as-spam'              => __('Entry marked as spam.',     GWOLLE_GB_TEXTDOMAIN),
		'marked-as-not-spam'          => __('Entry marked as not spam.', GWOLLE_GB_TEXTDOMAIN),
		'entry-edited'                => __('Entry has been edited.',    GWOLLE_GB_TEXTDOMAIN),
		'imported-from-dmsguestbook'  => __('Imported from DMSGuestbook', GWOLLE_GB_TEXTDOMAIN),
		'entry-trashed'               => __('Entry has been trashed.',   GWOLLE_GB_TEXTDOMAIN),
		'entry-untrashed'             => __('Entry has been untrashed.', GWOLLE_GB_TEXTDOMAIN)
	);

	$where = " 1 = %d";
	$values = Array(1);
	$tablename = $wpdb->prefix . "gwolle_gb_log";

	$where .= "
		AND
			log_subjectId = %d";

	$values[] = $subject_id;

	// FIXME, donot use * but list all the columns we want, it is cheaper for the db
	$sql = "
			SELECT
				*
			FROM
				" . $tablename . "
			WHERE
				" . $where . "
			ORDER BY
				log_date ASC
			;";

	$sql = $wpdb->prepare( $sql, $values );

	$entries = $wpdb->get_results( $sql, ARRAY_A );

	//$wpdb->print_error();
	//echo "number of rows: " . $wpdb->num_rows;

	if ( count($entries) == 0 ) {
		return false;
	}


	// Array to store the log entries
	$log_entries = array();

	foreach ( $entries as $entry ) {
		$log_entry = array(
			'id'        => (int) $entry['log_id'],
			'subject'   => stripslashes($entry['log_subject']),
			'author_id' => (int) $entry['log_authorId'],
			'log_date'  => stripslashes($entry['log_date'])
		);

		$log_entry['msg'] = (isset($log_messages[$log_entry['subject']])) ? $log_messages[$log_entry['subject']] : $log_entry['subject'];

		// Get author's display name or login name if not already done.
		$userdata = get_userdata( $log_entry['author_id'] );
		if (is_object($userdata)) {
			if ( isset( $userdata->display_name ) ) {
				$log_entry['author_login'] = $userdata->display_name;
			} else {
				$log_entry['author_login'] = $userdata->user_login;
			}
		} else {
			$log_entry['author_login'] = '<i>' . __('Unknown', GWOLLE_GB_TEXTDOMAIN) . '</i>';
		}

		// Construct the message in HTML
		$log_entry['msg_html']  = date_i18n( get_option('date_format'), $log_entry['log_date']) . ", ";
		$log_entry['msg_html'] .= date_i18n( get_option('time_format'), $log_entry['log_date']);
		$log_entry['msg_html'] .= ': ' . $log_entry['msg'];

		if ( $log_entry['author_id'] == get_current_user_id() ) {
			$log_entry['msg_html'] .= ' (<strong>' . __('You', GWOLLE_GB_TEXTDOMAIN) . '</strong>)';
		} else {
			$log_entry['msg_html'] .= ' (' . $log_entry['author_login'] . ')';
		}

		$log_entries[] = $log_entry;
	}

	return $log_entries;
}

