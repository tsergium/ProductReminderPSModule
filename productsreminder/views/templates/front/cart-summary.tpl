{if $cookie->isLogged()}
	<script type="text/javascript">
	$(document).ready(function(){
		// BEGIN: Create elements
		$('th.cart_description').after(function(){
			return "<th class=\"cart_unit item\">{l s='Remind me' mod='productreminder'}</th>";
		});
		$('td.cart_description').each(function(){
			var currentObject = $(this);
			var s = $(this).parent().find('.cart_quantity_delete').attr('href');
			var idProduct = s.match(/id_product=([^&]+)/)[1];
			var query = $.ajax({
				type: 'POST',
				url: baseDir + 'modules/productsreminder/ajax.php',
				data: 'idProduct=' + idProduct + '&action=cart-summary-create-elem',
				dataType: 'json',
				success: function(data) {
					if(data)
					{
						console.log(data);
						currentObject.after(function(){
							return data;
						});
					}
				}
			});
		});
		// END: Create elements
		
		// BEGIN: Save selections
		$('#cart_summary').on('change', '.remindPeriod',function(){
			var idProduct = $(this).data('id');
			var period = $(this).val();
			var query = $.ajax({
				type: 'POST',
				url: baseDir + 'modules/productsreminder/ajax.php',
				data: 'idProduct=' + idProduct + '&period=' + period + '&action=cart-summary-save',
				dataType: 'json',
				success: function(data) {
					if(data)
					{
						console.log(data);
					}
				}
			});
		});
		// END: Save selections
	});
	</script>
{/if}