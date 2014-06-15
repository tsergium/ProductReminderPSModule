{if $cookie->isLogged()}
	<script type="text/javascript">
	$(document).ready(function(){
		// BEGIN: Create elements
		$('#order-detail-content table thead tr th').eq(2).before(function(){
			return "<th class=\"cart_unit item\">{l s='Remind me' mod='productreminder'}</th>";
		});
		$('#order-detail-content table tbody tr td.return_quantity').each(function(){
			var currentObject = $(this);
			var s = $(this).parent().find('.order_qte_input').attr('name');
			
			var idProduct = parseInt(s.replace ( /[^\d.]/g, '' ), 10);
			console.log(idProduct);
			var query = $.ajax({
				type: 'POST',
				url: baseDir + 'modules/productsreminder/ajax.php',
				data: 'idProduct=' + idProduct + '&action=order-history-create-elem',
				dataType: 'json',
				success: function(data) {
					if(data)
					{
						console.log(data);
						currentObject.before(function(){
							return data;
						});
					}
				}
			});
		});
		// END: Create elements
		
		// BEGIN: Save selections
		$('#order-detail-content').on('change', '.remindPeriod',function(){
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