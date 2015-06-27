<?php

namespace Models;

use I18n\I18n;
use Libs\Utils;

/**
 * Tag
 * terms.term_id					->	term_taxonomy.term_id
 * term_taxonomy.term_taxonomy_id	->	term_relationships.term_taxonomy_id.
 * This model help us for to do querys and some operation about the tags of the diferents entries
 *
 * @author José María Valera Reales <@Chemaclass>
 */
class Tag {

	/*
	 * Some constants
	 */
	const TRANSIENT_ALL_TAGS = 'ALL_TAGS';

	/**
	 * Get all tags
	 *
	 * @see http://codex.wordpress.org/Transients_API
	 * @return array<Etiqueta> List with all tags
	 */
	public static function getAll() {
		global $wpdb;
		if (false === ($results = get_transient(self::TRANSIENT_ALL_TAGS))) {
			$results = $wpdb->get_results('
				SELECT ta.term_taxonomy_id as taxonomy_id, name, slug, count(*) total
				FROM wp_term_taxonomy ta
				JOIN wp_terms te ON (te.term_id = ta.term_id)
				JOIN wp_term_relationships re ON (re.term_taxonomy_id = ta.term_taxonomy_id)
				WHERE taxonomy = "post_tag"
				GROUP BY name, slug, taxonomy_id
				ORDER BY total DESC, name, slug');
			set_transient(self::TRANSIENT_ALL_TAGS, $results, 12 * HOUR_IN_SECONDS);
		}
		return $results;
	}
}