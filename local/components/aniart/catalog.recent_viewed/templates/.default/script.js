(function(Aniart){

	var RecentViewedItem = Aniart.Widget.extend({

		defaults: function(){
			return {
				ajaxHandler: '/ajax/common.php',
				$delete: null,
				deleteSelector: '.delete-recent-item'
			}
		},

		initialize: function(){
			var _this = this;
			var productId = this.$el.data('id');
			this.$delete = this.$el.find(this.deleteSelector);
			this.$delete.on('click', function(){
				$.post(_this.ajaxHandler, {
					handler: 'reсent_viewed',
					f: 'deleteItem',
					productId: productId
				}, function(response){
					// в случае успешного запроса скрываем удаленный (текущий элемент)
				}, 'json')
			});
		}

	});

	$(document).ready(function(){
		$('#recent_viewed_items .bx_catalog_item').each(function(){
			var $item = $(this);
			new RecentViewedItem($item);
		});
	});

})(window.Aniart);
