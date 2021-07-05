<?php

//     ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);

class Rewards_Addresses extends PerchAPI_Factory
{
	protected $table     = 'rewards_addresses';
	protected $pk        = 'addressID';
	protected $singular_classname = 'Address';

	protected $default_sort_column = 'addressID';

	public $static_fields   = [
        'loyaltyCardID',
        'addressHouse',
        'addressStreet',
        'addressCity',
        'addressCounty',
        'addressPostcode'
    ];

    # cardBalance is in pennies

    function __construct($API)
	{
		parent::__construct($API);
		$sql = "CREATE TABLE IF NOT EXISTS $this->table (
			addressID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            loyaltyCardID INT(11) NOT NULL,
            addressHouse VARCHAR(255) NOT NULL,
            addressStreet VARCHAR(255) NOT NULL,
            addressCity VARCHAR(255) NOT NULL,
            addressCounty VARCHAR(255),
            addressPostcode VARCHAR(255) NOT NULL
		);";

		$statements = explode(';', $sql);
		foreach ($statements as $statement) {
			$statement = trim($statement);
			if ($statement != '') $this->db->execute($statement);
		}
	}

    function create_address($data) {
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

    function update_address($data) {
        if (!$data[$this->pk]) {
            return;
        }
        $sql = "UPDATE $this->table SET "; 

        $count = 0;
        foreach ($this->static_fields as $key) {
            if ($data[$key]) {
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

    function get_address($addressID) {
        $sql = "SELECT * FROM $this->table WHERE addressID='$addressID' LIMIT 1";
        return $this->db->get_row($sql);
    }

    function delete_address($addressID) {
        $sql = "DELETE FROM $this->table WHERE addressID='$addressID'";
        return $this->db->execute($sql);
    }

    function get_address_by_loyalty_card($loyaltyCardID) {
        $sql = "SELECT * FROM $this->table WHERE loyaltyCardID='$loyaltyCardID' LIMIT 1";
        return $this->db->get_row($sql);
    }

}