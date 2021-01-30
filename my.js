
$(function(){
    setInterval(function(){
    	$.ajax({
    	    url:  url + "/db_view_interval_update.php",
    	    type: 'GET',

    	    //通信状態に問題がないかどうか
    	    success: function(data_ary) {
//    	    	console.log(JSON.parse(data_ary));

    	    	 $('.dice_roll').text(JSON.parse(data_ary).dice_roll);
    	    	 $('.dice_roll_timestamp').text(JSON.parse(data_ary).dice_roll_timestamp);
    	    	 $('.now_deck_cards_num').text(JSON.parse(data_ary).now_deck_cards_num);

    	    	 if(JSON.parse(data_ary).now_deck_cards_num == 0){
        	    	 $('.deck_empty').text("デッキが空です");
    	    		 $('.name').hide();
    	    		 $('.type').hide();
    	    		 $('.effect').hide();
    	    	 }
    	    	 else{
    	    		 $('.deck_empty').hide();

    	    		 if(JSON.parse(data_ary).is_deck_top_card_exits){
    	    			 $('.card_img').attr('src', JSON.parse(data_ary).img_path);
        	    		 $('.name').text(JSON.parse(data_ary).name);
        	    		 $('.type').text(JSON.parse(data_ary).type);
        	    		 $('.effect').text(JSON.parse(data_ary).effect);
    	    		 }

    	    	 }

    	    },
    	    //通信エラーになった場合の処理
    	    error: function(err) {

    	        //エラー処理を書く

    	    }
    	});
    },1000);
});
