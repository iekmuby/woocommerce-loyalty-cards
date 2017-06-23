<div id="loyalty-card">
	<label for="coupon_code"><?php _e( 'Loyalty card', 'sha-wlc' ); ?>:</label>
	<div class="loyalty-card-input">
		<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADgAAAAkCAYAAADckvn+AAAABHNCSVQICAgIfAhkiAAAAmtJREFUWIXtmE1u00AUgL+ZjmOnjlMa0kKjqq0pCAmJFYfgEFyGK7BAnIAjILGkJ0BISLCoKqiJ0h8aJ8ZN40lnhkUhUFViRZTEyifNajzS+968ebZHfH69evKgpZqUkP0jcybc3rqbdiCTRE47gElTekHlKHWFol68aVENqtzbvY/WBYeHXymKAgABRPUVqkHA8fERCDFeuL29g1/xGVwMODk5Rms9JYV/o96+X6VSqfAwXyWOYz50lkmSBGstCIhqEVtbWyRJQJZl44WbpxFxvIPWI5KkTqfTwbnZqwY1MpLRxSUHX9qEUYPWZszJ9z79fh+AXnaBf9qjVr9Nt3eOtRYhBIffjqmGt6jX60QrTc7SnDzPp6xzk3GTSdOU/f19PM+j2WyilEIIgbWWbreLlJJGo4H4VabGGNrtNsPhEM/zCMMQKWevZy3FcfwcQAiB1pogCNjY2CDPcwaDAXAlI6UkDEOGwyHGGJxzjEYjrLX4vk8QBGitKYoC59w4EdPmWsqLoiBJEs7Pz2m1WnieN55L0xStNVEUIaVECIExhjRNybIMYwy1Wm2887OCBMfvIQRkWUan08H3fdbX18Zzzln6/T5B4KOUGj8/0pper3etVK9wMzHEu1e7N1qf8jxq4TKXxpD/+KtxCAj8AD3SWGP/ZGlJ4lcqCCEx5pJCa2bl9Srs3tqMhDIZlCj515qicnfaMUwURf3JtGOYKOWuTxaC889CcN5ZCM47C8F5ZyE47yhm8CbsfyLc3p1SG6qZ+fWeEKU/gwvBeUdeXY6V8Rw6jAXx7OnKy8e71UdSilLtprXOfjwYfvoJ5S0dIAsFYJ4AAAAASUVORK5CYII=" />
		<input type="text" name="coupon_code" class="input-text" id="loyaltyCard" value="" placeholder="<?php _e( 'Card number', 'sha-wlc' ); ?>">
		<input type="button" class="button" id="cardAddButton" name="apply_coupon" value="<?php _e( 'Apply card', 'sha-wlc' ); ?>">
	</div>
</div>
<script type="text/javascript">
	var sha_wlc_nonce = '<?php echo $sha_wlc_nonce; ?>';
</script>