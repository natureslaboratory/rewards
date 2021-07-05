<?php

class Rewards_LoyaltyCardBuilder
{
    protected PerchAPI $API;
    protected Rewards_LoyaltyCards $LoyaltyCards;
    protected Rewards_Addresses $Addresses;

    function __construct($API)
	{
        $this->API = $API;
        $this->LoyaltyCards = new Rewards_LoyaltyCards($API);
        $this->Addresses = new Rewards_Addresses($API);
	}

    function create_card($data) {
        $loyaltyCardID = $this->LoyaltyCards->create_card($data);
        return $this->Addresses->create_address(array_merge($data, ["loyaltyCardID" => $loyaltyCardID]));
    }

    function get_card($loyaltyCardID) {
        $card = $this->LoyaltyCards->get_card($loyaltyCardID);
        $address = $this->Addresses->get_address_by_loyalty_card($loyaltyCardID);

        if ($address) {
            return array_merge($card, $address);
        } else {
            return $card;
        }
    }

    function get_card_by_email($memberEmail) {
        $card = $this->LoyaltyCards->get_card_by_email($memberEmail);
        if (!$card) {
            return false;
        }
        $address = $this->Addresses->get_address_by_loyalty_card($card["loyaltyCardID"]);
        if ($address) {
            return array_merge($card, $address);
        } else {
            return $card;
        }
    }

    function get_cards() {
        $cards = $this->LoyaltyCards->get_cards();
        $newCards = [];
        foreach ($cards as $card) {
            $address = $this->Addresses->get_address_by_loyalty_card($card["loyaltyCardID"]);
            if (!$address) {
                $newCards[] = $card;
            } else {
                $newCards[] = array_merge($card, $address);
            }
        }

        return $newCards;
    }

    function edit_card($data) {
        $this->LoyaltyCards->update_card($data);

        if (array_key_exists("addressID", $data)) {
            $this->Addresses->update_address($data);
        } else {
            $this->Addresses->create_address($data);
        }
    }

    function delete_card($loyaltyCardID) {
        $address = $this->Addresses->get_address_by_loyalty_card($loyaltyCardID);
        $addressResult = null;
        if ($address) {
            $addressResult = $this->Addresses->delete_address($address["addressID"]);
        }
        $loyaltyCardResult = $this->LoyaltyCards->delete_card($loyaltyCardID);
        return [
            $addressResult,
            $loyaltyCardResult
        ];
    }
}