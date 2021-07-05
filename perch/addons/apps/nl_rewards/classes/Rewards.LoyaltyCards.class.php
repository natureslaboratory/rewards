<?php

//     ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);

class Rewards_LoyaltyCards extends PerchAPI_Factory
{
	protected $table     = 'rewards_loyalty_cards';
	protected $pk        = 'loyaltyCardID';
	protected $singular_classname = 'LoyaltyCard';

	protected $default_sort_column = 'loyaltyCardID';

	public $static_fields   = [
        'memberEmail',
        'memberFirstName',
        'memberLastName',
        'memberPhone',
        'cardNumber',
        'cardBalance'
    ];

    # cardBalance is in pennies

    function __construct($API)
	{
		parent::__construct($API);
		$sql = "CREATE TABLE IF NOT EXISTS $this->table (
			loyaltyCardID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            memberEmail VARCHAR(255) NOT NULL,
            memberFirstName VARCHAR(255) NOT NULL,
            memberLastName VARCHAR(255) NOT NULL,
            memberPhone VARCHAR(255),
            cardNumber VARCHAR(255) NOT NULL,
            cardBalance INT(255) NOT NULL DEFAULT 0,
			dateCreated datetime default NOW()
		);";

		$statements = explode(';', $sql);
		foreach ($statements as $statement) {
			$statement = trim($statement);
			if ($statement != '') $this->db->execute($statement);
		}
	}

    function create_card($data) {
        $sql = "INSERT INTO $this->table (";
        $count = 0;
        foreach ($this->static_fields as $key) {
            if (array_key_exists($key, $data)) {
                if ($count == 0) {
                    $sql .= $key;
                } else {
                    $sql .= ", $key";
                }
                $count++;
            }
        }
        $sql .= ") VALUES (";
        $count = 0;
        foreach ($this->static_fields as $key) {
            if (array_key_exists($key, $data)) {
                if ($count == 0) {
                    $sql .= "'$data[$key]'";
                } else {
                    $sql .= ", '$data[$key]'";
                }
                $count++;
            }
        }

        $sql .= ");";
        $result = $this->db->execute($sql);
        if ($result) {
            return $this->db->get_row("SELECT LAST_INSERT_ID()")["LAST_INSERT_ID()"];
        }
    }

    function update_card($data) {
        if (!array_key_exists($this->pk, $data)) {
            return;
        }
        $sql = "UPDATE $this->table SET "; 

        $count = 0;
        foreach ($this->static_fields as $key) {
            if (array_key_exists($key, $data)) {
                if ($count == 0) {
                    $sql .= "$key='$data[$key]'";
                } else {
                    $sql .= ", $key='$data[$key]'";
                }
                $count++;
            }
        }
        
        $sql .= " WHERE ". $this->pk . "=" . $data[$this->pk];
        return $this->db->execute($sql);
    }

    function get_card($loyaltyCardID) {
        $sql = "SELECT * FROM $this->table WHERE loyaltyCardID='$loyaltyCardID' LIMIT 1";
        return $this->db->get_row($sql);
    }

    function get_card_by_email($memberEmail) {
        $sql = "SELECT * FROM $this->table WHERE memberEmail='$memberEmail' LIMIT 1";
        return $this->db->get_row($sql);
    }

    function get_cards() {
        $sql = "SELECT * FROM $this->table";
        return $this->db->get_rows($sql);
    }

    function delete_card($loyaltyCardID) {
        $sql = "DELETE FROM $this->table WHERE loyaltyCardID='$loyaltyCardID'";
        return $this->db->execute($sql);
    }

}