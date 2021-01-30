<?php
require_once "const.inc.php";
require_once "h.inc.php";

$dice_rolls = [
    1=>"無1",
    2=>"無2",
    3=>"無3",
    4=>"無4",
    5=>"カオス",
    6=>"プレインズウォーク",
];


$pdo = new PDO(
    $dsn,
    $user,
    $password
    );

$pdo->exec("SET NAMES utf8");


$is_server_request_post = $_SERVER["REQUEST_METHOD"] === "POST";
if($is_server_request_post){
    if($_POST["reset"]){
        $pdo->exec("
            UPDATE deck_cards SET state = '';
        ");
        $stmt = $pdo->query("
            SELECT COUNT(*) AS num FROM deck_cards;
        ");
        $deck_cards_count_ary = $stmt->fetch();
        $deck_cards_count = $deck_cards_count_ary["num"];

        $decks = [];
        $decks = array_pad($decks, $deck_size, 0);

        $now_phenomenon_count = 0;
        foreach($decks as $i => $decks_value){
            while(true){
                $tb_id_candidate = mt_rand(1, $deck_cards_count);
                $is_duplication = false;
                foreach($decks as $decks_id){
                    if($decks_id == $tb_id_candidate){
                        $is_duplication = true;
                    }
                }
                if($is_duplication){
                }
                else{
                    $stmt = $pdo->prepare("
                        SELECT * FROM deck_cards WHERE id = ?;
                    ");
                    $stmt->execute(array($tb_id_candidate));
                    $candidate_card = $stmt->fetch();
                    $is_phenomenon_card = $candidate_card["type"] == "現象";
                    if ($is_phenomenon_card){
                        if($now_phenomenon_count <= $max_phenomenon_i){
                            $decks[$i] = $tb_id_candidate;
                            $now_phenomenon_count++;
                            break;
                        }
                    }
                    else{
                        $decks[$i] = $tb_id_candidate;
                        break;
                    }
                }
            }
        }
        foreach($decks as $tb_id){
            $stmt = $pdo->prepare("
                UPDATE deck_cards SET state = ? WHERE id = ?
            ");
            $stmt->execute(array($is_deckes,$tb_id));
        }
    }
    elseif($_POST["darw"]){
        $stmt = $pdo->prepare("
            SELECT id FROM deck_cards WHERE state = ?;
        ");
        $stmt->execute(array($is_deckes));
        $not_top_decks = $stmt->fetchAll();
        $when_draw_card_deck_num = count($not_top_decks);
        $max_when_draw_card_deck_i = $when_draw_card_deck_num - 1;

        $stmt = $pdo->prepare("
            SELECT id FROM deck_cards WHERE state = ?;
        ");
        $stmt->execute(array($is_deck_top));
        $deck_top_card_tb_id_ary = $stmt->fetch();
        $deck_top_card_tb_id = $deck_top_card_tb_id_ary["id"];
        if($when_draw_card_deck_num >= 1){
            $draw_card_i = mt_rand(0, $max_when_draw_card_deck_i);

            $draw_card_tb_id = $not_top_decks[$draw_card_i]["id"];


            if(isset($deck_top_card_tb_id)){
                $stmt = $pdo->prepare("
                    UPDATE deck_cards SET state = '' WHERE id = ?;
                ");
                $stmt->execute(array($deck_top_card_tb_id));
            }

            $stmt = $pdo->prepare("
                UPDATE deck_cards SET state = ? WHERE id = ?;
            ");
            $stmt->execute(array($is_deck_top,$draw_card_tb_id));
        }
        else {
            if(isset($deck_top_card_tb_id)){
                $stmt = $pdo->prepare("
                    UPDATE deck_cards SET state = '' WHERE id = ?;
                ");
                $stmt->execute(array($deck_top_card_tb_id));
            }
        }
    }
    elseif($_POST["dice"]){
        $set_dice_roll =  $dice_rolls[mt_rand(1, 6)];

        $stmt = $pdo->prepare("
            UPDATE dice SET roll = ? WHERE id = 1;
        ");
        $stmt->execute(array($set_dice_roll));
    }
    header($location_url);
}


$stmt = $pdo->prepare("
            SELECT id FROM deck_cards WHERE state = ?;
        ");
$stmt->execute(array($is_deckes));
$display_not_top_decks = $stmt->fetchAll();
$display_now_deck_cards_num = count($display_not_top_decks);


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


?>




<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>プレインチェイス</title>
    <style type="text/css">
        .effect {width:50%;}
    </style>
</head>
<body>


	<form action="" method="POST">
		<p>
			<input class="button" type="submit" name="reset" value="リセット">
			<input class="button" type="submit" name="darw" value="ドロー">
			<input class="button" type="submit" name="dice" value="ダイスロール">
		</p>
	</form>
    <p>ダイスの目 : <spam  class="dice_roll"><?php h($now_dice_roll); ?></spam></p>
    <p>ダイスタイムスタンプ : <spam class="dice_roll_timestamp"><?php h($now_dice_roll_timestamp); ?></spam></p>
	<p>次元デッキ残り枚数 : <spam class="now_deck_cards_num"><?php h($display_now_deck_cards_num) ?></spam></p>
	<div class="card_text">
		<?php if($display_now_deck_cards_num <= 0) : ?>
			<p class="deck_empty">デッキが空です</p>
    	<?php else : ?>
        	<?php if(isset($display_deck_top_card)) : ?>
        		<img class="card_img" src="<?php h($display_deck_top_card["img_path"]); ?>" alt="">
        		<div class="name"><?php h($display_deck_top_card["name"]); ?></div>
        		<div class="type"><?php h($display_deck_top_card["type"]); ?></div>
        		<div class="effect"><?php h($display_deck_top_card["effect"]); ?></div>
        	<?php else : ?>
        	<?php endif;?>
    	<?php endif;?>
	</div>

</body>
<script>var url = '<?php echo($url); ?>';</script>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="my.js"></script>
</html>