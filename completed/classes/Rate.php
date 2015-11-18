<?php
class Rate {
	
	private $objDb = null;
	
	private $_dbhost = 'localhost';
	private $_dbname = 'comments';
	private $_dbuser = 'root';
	private $_dbpass = 'password';
	
	private $_table_1 = 'comments';
	private $_table_2 = 'ratings';
	
	public $_user;
	
	
	
	
	
	
	
	
	
	public function __construct($user = null) {
		$this->_user = !empty($user) ? $user : getenv('REMOTE_ADDR');
	}
	
	
	
	
	
	
	
	
	
	
	private function connect() {
		try {
			$this->objDb = new PDO(
				"mysql:host={$this->_dbhost};dbname={$this->_dbname}", 
				$this->_dbuser, 
				$this->_dbpass, 
				array(
					PDO::ATTR_PERSISTENT => true
				)
			);
			$this->objDb->exec("SET CHARACTER SET utf8");
		} catch(PDOException $e) {
			echo $e->getMessage();
			exit();
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	public function getPosts() {
		if ($this->objDb == null) {
			$this->connect();
		}
		$sql = "SELECT *,
				DATE_FORMAT(`date`, '%d/%m/%Y') AS `date_formatted`
				FROM `{$this->_table_1}`
				WHERE `active` = 1
				ORDER BY `date` DESC";
		$statement = $this->objDb->query($sql);
		return $statement->fetchAll(PDO::FETCH_ASSOC);			
	}
	
	
	
	
	
	
	public function getPost($id = null) {
		if (!empty($id)) {
			if ($this->objDb == null) {
				$this->connect();
			}
			$sql = "SELECT *
					FROM `{$this->_table_1}`
					WHERE `id` = ?";
			$statement = $this->objDb->prepare($sql);
			$statement->execute(array($id));
			return $statement->fetch(PDO::FETCH_ASSOC);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	public function getByUser($id = null) {
		if (!empty($id) && !empty($this->_user)) {
			if ($this->objDb == null) {
				$this->connect();
			}
			$sql = "SELECT *
					FROM `{$this->_table_2}`
					WHERE `user` = ?
					AND `item` = ?";
			$statement = $this->objDb->prepare($sql);
			$statement->execute(array($this->_user, $id));
			return $statement->fetch(PDO::FETCH_ASSOC);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function isSubmitted($id = null) {
		if (!empty($id)) {
			if ($this->objDb == null) {
				$this->connect();
			}
			$found = $this->getByUser($id);
			return !empty($found) ? true : false;
		}
		return true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function addRating($id = null, $rate = null) {
		if (!empty($id) && !empty($this->_user)) {
			if ($this->objDb == null) {
				$this->connect();
			}
			$rate = $rate == 1 ? 1 : 0;
			$sql = "INSERT INTO `{$this->_table_2}`
					(`user`, `item`, `rate`)
					VALUES (?, ?, ?)";
			$statement = $this->objDb->prepare($sql);
			if ($statement->execute(array($this->_user, $id, $rate))) {
				return $this->updateRating($id, $rate);
			}
			return false;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function updateRating($id = null, $rate = null) {
		if (!empty($id)) {
			if ($this->objDb == null) {
				$this->connect();
			}
			$sql  = "UPDATE `{$this->_table_1}` SET ";
			$sql .= $rate == 1 ? " `up` = `up` + 1 " : " `down` = `down` + 1 ";
			$sql .= "WHERE `id` = ?";
			$statement = $this->objDb->prepare($sql);
			return $statement->execute(array($id));
		}
	}	
	
	
	
	
	
	
	
	
	
	
	
	public function getAllByUser() {
		if (!empty($this->_user)) {
			if ($this->objDb == null) {
				$this->connect();
			}
			$sql = "SELECT *
					FROM `{$this->_table_2}`
					WHERE `user` = ?";
			$statement = $this->objDb->prepare($sql);
			$statement->execute(array($this->_user));
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function reset() {
		if (!empty($this->_user)) {
			$list = $this->getAllByUser();
			if (!empty($list)) {
				foreach($list as $row) {
					$field = $row['rate'] == 1 ? 'up' : 'down';
					$this->removeRating($row['item'], $field);
				}
				$sql = "DELETE FROM `{$this->_table_2}`
						WHERE `user` = ?";
				$statement = $this->objDb->prepare($sql);
				return $statement->execute(array($this->_user));
			}
			return false;
		}
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function removeRating($id = null, $field = null) {
		if (!empty($id) && !empty($field)) {
			$sql = "UPDATE `{$this->_table_1}`
					SET `{$field}` = `{$field}` -1
					WHERE `id` = ?";
			$statement = $this->objDb->prepare($sql);
			return $statement->execute(array($id));
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function buttonSet($id = null) {
		if (!empty($id) && !empty($this->_user)) {
			$post = $this->getPost($id);
			$found = $this->getByUser($id);
			if (!empty($found)) {
				$out  = '<div class="rateWrapper">';
				$out .= '<span class="rateDone rateUp';
				$out .= $found['rate'] == 1 ? ' active' : null;
				$out .= '" data-item="';
				$out .= $id;
				$out .= '"><span class="rateUpN">';
				$out .= intval($post['up']);
				$out .= '</span></span>';
				$out .= '<span class="rateDone rateDown';
				$out .= $found['rate'] == 0 ? ' active' : null;
				$out .= '" data-item="';
				$out .= $id;
				$out .= '"><span class="rateDownN">';
				$out .= intval($post['down']);
				$out .= '</span></span>';
				$out .= '</div>';
				return $out;
			} else {
				$out  = '<div class="rateWrapper">';
				$out .= '<span class="rate rateUp" data-item="';
				$out .= $id;
				$out .= '"><span class="rateUpN">';
				$out .= intval($post['up']);
				$out .= '</span></span>';
				$out .= '<span class="rate rateDown" data-item="';
				$out .= $id;
				$out .= '"><span class="rateDownN">';
				$out .= intval($post['down']);
				$out .= '</span></span>';
				$out .= '</div>';
				return $out;
			}
		}
	}
	
	
	
	











}



