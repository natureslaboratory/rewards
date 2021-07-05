<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


# Include your class files as needed - up to you.

include("classes/Rewards.LoyaltyCard.class.php");
include("classes/Rewards.LoyaltyCards.class.php");
include("classes/Rewards.Address.class.php");
include("classes/Rewards.Addresses.class.php");
include("classes/Rewards.LoyaltyCardBuilder.class.php");

# Create the function(s) users will call from their pages

function nl_rewards_form_handler($SubmittedForm)
{
  if ($SubmittedForm->validate()) {
    $API  = new PerchAPI(1.0, 'nl_rewards');

    switch ($SubmittedForm->formID) {
      case "create_card":
        $LoyaltyCardBuilder = new Rewards_LoyaltyCardBuilder($API);
        return $LoyaltyCardBuilder->create_card($SubmittedForm->data);
      case "edit_card":
        $LoyaltyCardBuilder = new Rewards_LoyaltyCardBuilder($API);
        return $LoyaltyCardBuilder->edit_card($SubmittedForm->data);
      case "delete_card":
        $LoyaltyCardBuilder = new Rewards_LoyaltyCardBuilder($API);
        $loyaltyCardID = $SubmittedForm->data["loyaltyCardID"];
        return $LoyaltyCardBuilder->delete_card($loyaltyCardID);
    }
  }
}

// Forms

function create_card()
{
  $API = new PerchAPI(1.0, "nl_rewards");

  $Template = $API->get("Template");
  $Template->set("rewards/card_form.html", "rewards");

  $html = $Template->render(["formType" => "create"]);
  $html = $Template->apply_runtime_post_processing($html);

  echo $html;
}

function edit_card($loyaltyCardID)
{
  $API = new PerchAPI(1.0, "nl_rewards");
  $cardBuilder = new Rewards_LoyaltyCardBuilder($API);

  $card = $cardBuilder->get_card($loyaltyCardID);

  $Template = $API->get("Template");
  $Template->set("rewards/card_form.html", "rewards");

  $card["formType"] = "edit";
  
  $html = $Template->render($card);
  $html = $Template->apply_runtime_post_processing($html, $card);

  echo $html;
}

function get_card($loyaltyCardID)
{
  $API = new PerchAPI(1.0, "nl_rewards");
  $cards = new Rewards_LoyaltyCards($API);

  $card = $cards->get_card($loyaltyCardID);

  $Template = $API->get("Template");
  $Template->set("rewards/card.html", "rewards");

  $html = $Template->render($card);
  $html = $Template->apply_runtime_post_processing($html, true);

  echo $html;
}

function get_cards()
{
  $API = new PerchAPI(1.0, "nl_rewards");
  $cardBuilder = new Rewards_LoyaltyCardBuilder($API);

  $loyaltyCards = $cardBuilder->get_cards();

  $Template = $API->get("Template");
  $Template->set("rewards/cards.html", "rewards");

  $html = $Template->render_group($loyaltyCards);
  $html = $Template->apply_runtime_post_processing($html, $loyaltyCards);

  echo $html;
}

function update_balance($loyaltyCardID, $newBalance)
{
  $API = new PerchAPI(1.0, "nl_rewards");
  $cards = new Rewards_LoyaltyCards($API);

  return $cards->update_card([
    "loyaltyCardID" => $loyaltyCardID,
    "cardBalance" => $newBalance
  ]);
}

function delete_card($loyaltyCardID) {
  $API = new PerchAPI(1.0, "nl_rewards");
  
  $Template = $API->get("Template");
  $Template->set("rewards/delete.html", "rewards");

  $data = [
    "loyaltyCardID" => $loyaltyCardID
  ];

  $html = $Template->render($data);
  $html = $Template->apply_runtime_post_processing($html, $data);

  echo $html;
}

function card($opts = []) {
  $API = new PerchAPI(1.0, "nl_rewards");
  $cardBuilder = new Rewards_LoyaltyCardBuilder($API);
  $card = null;
  if (array_key_exists("loyaltyCardID", $opts) && $opts["loyaltyCardID"]) {
    $card = $cardBuilder->get_card($opts["loyaltyCardID"]);
  } else if (array_key_exists("memberEmail", $opts) && $opts["memberEmail"]) {
    $card = $cardBuilder->get_card_by_email($opts["memberEmail"]);
  }

  if (!$card) {
    return false;
  }

  if (array_key_exists("skip-template", $opts) && $opts["skip-template"]) {
    return $card;
  }

  $template = array_key_exists("template", $opts) && $opts["template"] ? $opts["template"] : "rewards/card.html";

  $Template = $API->get("Template");
  $Template->set($template, "rewards");

  $html = $Template->render($card);
  $html = $Template->apply_runtime_post_processing($html, $card);

  echo $html;

}

// function create_card()
// {
//   $API = new PerchAPI(1.0, "nl_rewards");

//   $Template = $API->get("Template");
//   $Template->set("rewards/card_form.html", "rewards");

//   $html = $Template->render(["formType" => "create"]);
//   $html = $Template->apply_runtime_post_processing($html);

//   echo $html;
// }



