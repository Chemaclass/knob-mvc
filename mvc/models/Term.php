<?php

namespace Models;

/**
 * Tag
 * terms.term_id					->	term_taxonomy.term_id
 * term_taxonomy.term_taxonomy_id	->	term_relationships.term_taxonomy_id.
 * This model help us for to do querys and some operation about the tags of the diferents entries
 *
 * @author José María Valera Reales <@Chemaclass>
 */
class Term extends ModelBase {
	public static $table = "terms";
	static $PK = 'term_id';

	/*
	 * Some constants
	 */
	const TRANSIENT_ALL_TAGS = 'ALL_TAGS';

	/*
	 * Members
	 */
	private $total;

	/**
	 * Return all categories
	 *
	 * @param array $args
	 * @return array<Term>
	 * @link https://codex.wordpress.org/Function_Reference/get_terms
	 */
	public static function getCategories($args = []) {
		if (!count($args)) {
			$args = [
				'orderby' => 'name,count',
				'hide_empty' => true
			];
		}
		foreach ( get_terms('category', $args) as $_c ) {
			$cat = Term::find($_c->term_id);
			$cat->total = $_c->count;
			$categories[] = $cat;
		}
		return $categories;
	}

	/**
	 * Return all tags
	 *
	 * @param array $args
	 * @return array<Term>
	 * @link https://codex.wordpress.org/Function_Reference/get_terms
	 */
	public static function getTags($args = []) {
		if (!count($args)) {
			$args = [
				'orderby' => 'name,count',
				'hide_empty' => true
			];
		}
		foreach ( get_terms('post_tag', $args) as $_t ) {
			$tag = Term::find($_t->term_id);
			$tag->total = $_t->count;
			$tags[] = $tag;
		}
		return $tags;
	}

	/**
	 *
	 * @return string the name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * @return string the slug
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 *
	 * @return integer
	 */
	public function getTotal() {
		return $this->total;
	}

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