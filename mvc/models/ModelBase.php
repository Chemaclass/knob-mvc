<?php

namespace Models;

/**
 * Base abstract for all Models
 *
 * @author José María Valera Reales
 */
abstract class ModelBase {

	// columns from the table of our Model
	protected static $columns = array ();
	// Primary Key
	protected static $PK = 'ID';

	/*
	 * Members
	 */
	public $ID;
	public $created_at;
	public $updated_at;

	/**
	 * Constructor
	 *
	 * @param integer $ID
	 */
	public function __construct($ID = 0) {
		$this->ID = $ID;
		global $wpdb;
		static::$columns = $wpdb->get_col_info();
	}

	/**
	 * Return all objects
	 *
	 * @return array<Object>
	 */
	public static function all() {
		global $wpdb;
		// Son class
		$model = get_called_class();
		$whatryResults = $wpdb->get_results('SELECT * FROM wp_' . $model::$table);
		$result = [ ];
		foreach ( $whatryResults as $qr ) {
			$a = new $model();
			foreach ( self::$columns as $c ) {
				$a->$c = $qr->$c;
			}
			$result[] = $a;
		}
		return $result;
	}

	/**
	 * Search and return the Object across his ID
	 *
	 * @param integer $ID
	 * @return object
	 */
	public static function find($ID = false) {
		if ($ID == null || !is_numeric($ID)) {
			return null;
		}
		global $wpdb;
		$model = get_called_class();
		$whatry = 'SELECT *
				FROM wp_' . static::$table . '
				WHERE ' . static::$PK . '= %d';
		$object = $wpdb->get_row($wpdb->prepare($whatry, $ID));
		if (!$object) {
			return null;
		}
		$a = new $model();
		if ($object) {
			foreach ( $object as $c => $val ) {
				$a->$c = $val;
			}
		}
		return $a;
	}

	/**
	 * Search all values across one column
	 *
	 * @param string $column
	 * @param string $value
	 * @param boolean $single
	 *        	Por defecto false. True si es sólo 1.
	 * @return array<object>
	 */
	public static function findAllBy($column, $value, $single = false) {
		global $wpdb;
		$objects = [ ];
		$model = get_called_class();
		$query = 'SELECT * FROM wp_' . static::$table . ' WHERE ' . $column . '= %s';
		$resultsQuery = $wpdb->get_results($wpdb->prepare($query, $value));

		/*
		 * Mount the object
		 */
		$mountTheObject = function ($_object) use($model) {
			$object = new $model();
			foreach ( $_object as $column => $val ) {
				$object->$column = $val;
			}
			return $object;
		};

		if ($single) {
			foreach ( $resultsQuery as $_object ) {
				return $mountTheObject($_object);
			}
		}

		foreach ( $resultsQuery as $_object ) {
			$objects[] = $mountTheObject($_object);
		}

		return $objects;
	}

	/**
	 * Todo one DELETE
	 *
	 * @return Exception|boolean
	 */
	public function delete() {
		if ($this->ID !== false) {
			global $wpdb;
			try {
				return $wpdb->query($wpdb->prepare('
						DELETE FROM wp_' . static::$table . ' WHERE ID = %d', $this->ID));
			} catch ( Exception $e ) {
				return $e;
			}
		}
		return false;
	}

	/**
	 * Get the first element from the where
	 *
	 * @param unknown $column
	 * @param unknown $what
	 * @param unknown $value
	 */
	public static function first($column, $what, $value) {
		$w = self::where($column, $what, $value);
		if ($w && is_array($w)) {
			return $w[0];
		}
		return null;
	}

	/**
	 * Return the filter results
	 *
	 * @param string $column
	 * @param string $what
	 * @param string $value
	 */
	public static function where($column, $what, $value) {
		global $wpdb;
		// TODO: Improve it
		$all = self::all();
		$result = [ ];
		foreach ( $all as $item ) {
			if (isset($item->$column)) {
				if (self::isCompareColumn($item->$column, $what, $value)) {
					$result[] = $item;
				}
			}
		}
		return $result;
	}

	/**
	 *
	 * @param unknown $column
	 * @param unknown $what
	 * @param unknown $value
	 * @return boolean
	 */
	private static function isCompareColumn($column, $what, $value) {
		switch ($what) {
			case "=" :
				if ($column == $value) {
					return true;
				}
				return false;
			case "<" :
				if ($column < $value) {
					return true;
				}
				return false;
			case ">" :
				if ($column > $value) {
					return true;
				}
				return false;
			case ">=" :
				if ($column >= $value) {
					return true;
				}
				return false;
			case "<=" :
				if ($column <= $value) {
					return true;
				}
				return false;
		}
		return false;
	}

	/**
	 * __toArray
	 *
	 * @return array
	 */
	public function __toArray() {
		return call_user_func('get_object_vars', $this);
	}

	/**
	 * Create one key for the nonce request from ajax
	 *
	 * @param string $kindOfNonce
	 *        	Type of nonce
	 * @return string Nonce
	 */
	protected function createNonce($kindOfNonce) {
		return wp_create_nonce($kindOfNonce . $this->ID);
	}
}