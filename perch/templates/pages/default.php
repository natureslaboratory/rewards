<?php if (!defined('PERCH_RUNWAY')) include($_SERVER['DOCUMENT_ROOT'].'/perch/runtime.php'); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
	<title><?php perch_pages_title(); ?></title>
	<?php perch_page_attributes(); ?>
    <link href="/assets/css/index.css" rel="stylesheet" >
</head>
<body>
    <?php 
        if (perch_get("action")) {
            ?>
            <a style="margin: 2rem; display: block" href="/"><button class="c-btn">Back</button></a>

            <?php
            switch (perch_get("action")) {
                case "create":
                    create_card();
                    break;
                case "edit":
                    if (perch_get("cardID")) {
                        edit_card(perch_get("cardID"));
                    } else {
                        ?> <script> window.location.href = "/" </script> <?php
                    }
                    break;
                case "delete":
                    if (perch_get("cardID")) {
                        delete_card(perch_get("cardID"));
                    } else {
                        ?> <script> window.location.href = "/" </script> <?php
                    }
                    break;
            }
        } else if (perch_get("cardID")) {
            ?>
            <a style="margin: 2rem; display: block" href="/"><button class="c-btn">Back</button></a>

            <?php
            card([
                "loyaltyCardID" => perch_get("cardID")
            ]);
        } else {
            ?>
            <div class="c-btn-div">
                <a href="/?action=create"><button class="c-btn">+ Create Card</button></a>
            </div>
            <?php
            get_cards();
        }
    ?>
     <?php //PerchUtil::output_debug(); ?>
</body>
</html>