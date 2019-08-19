<form ACTION="<? echo $action; ?>" METHOD="POST">
	<input name="AMOUNT" value="<? echo $amount; ?>" type="hidden">
	<input name="CURRENCY" value="<? echo $currency; ?>" type="hidden">
	<input name="ORDER" value="<? echo $order_id; ?>" type="hidden">
	<input name="DESC" value="<? echo $desc; ?>" type="hidden">
	<input name="MERCH_NAME" value="<? echo $merch_name; ?>" type="hidden">
	<input name="MERCH_URL" value="<? echo $merch_url; ?>" type="hidden">
	<input name="TERMINAL" value="<? echo $terminal; ?>" type="hidden">
	<input name="EMAIL" value="<? echo $email; ?>" type="hidden">
	<input name="TRTYPE" value="<? echo $trtype; ?>" type="hidden">    
	<input name="COUNTRY" value="<? echo $country; ?>" type="hidden"> 
	<input name="MERCH_GMT" value="<? echo $merch_gmt; ?>" type="hidden"> 
	<input name="TIMESTAMP" value="<? echo $oper_time; ?>" type="hidden">
	<input name="NONCE" value="<? echo $nonce; ?>" type="hidden">
	<input name="BACKREF" value="<? echo $backref; ?>" type="hidden">
	<input name="LANG" value="<? echo $lang; ?>" type="hidden">
	<input name="P_SIGN" value="<? echo $p_sign; ?>" type="hidden">	
	<button type="submit"><? echo $button_confirm; ?></button>
</form>