<?php
require_once "const.inc.php";

$is_server_request_get = $_SERVER["REQUEST_METHOD"] === "GET";

if ($is_server_request_get){
    $pdo = new PDO(
        $dsn,
        $user,
        $password
        );

    $pdo->exec("SET NAMES utf8");

    $stmt = $pdo->prepare("
            SELECT id FROM deck_cards WHERE state = ?;
        ");
    $stmt->execute(array($is_deckes));
    $not_top_decks = $stmt->fetchAll();
    $now_deck_cards_num = count($not_top_decks);

    $stmt = $pdo->prepare("
            SELECT id FROM deck_cards WHERE state = ?;
        ");
    $stmt->execute(array($is_deck_top));
    $display_deck_top_card_tb_id_ary = $stmt->fetch();
    $display_deck_top_card_tb_id = $display_deck_top_card_tb_id_ary["id"];

    $stmt = $pdo->prepare("
            SELECT * FROM deck_cards WHERE id = ?;
        ");
    $stmt->execute(array($display_deck_top_card_tb_id));
    $display_deck_top_card = $stmt->fetch();

    $stmt = $pdo->query("
            SELECT * FROM dice WHERE id = 1;
        ");
    $now_dice_roll_ary = $stmt->fetch();
    $now_dice_roll = $now_dice_roll_ary["roll"];
    $now_dice_roll_timestamp = $now_dice_roll_ary["roll_timestamp"];


    $data_ary = [
        "dice_roll"=>$now_dice_roll,
        "dice_roll_timestamp"=>$now_dice_roll_timestamp,
        "now_deck_cards_num"=>$now_deck_cards_num,
        "is_deck_top_card_exits"=> $display_deck_top_card_tb_id != NULL,
        "name"=>$display_deck_top_card["name"],
        "type"=>$display_deck_top_card["type"],
        "effect"=>$display_deck_top_card["effect"],
        "img_path"=>$display_deck_top_card["img_path"],
    ];
    echo json_encode($data_ary);
}

?>
