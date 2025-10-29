<!-- Модальное окно -->
<div id="modal_add_plot" class="modal ">
	<div id="modal_container">
		<div id="modal_content" class="modal_content">
			<div class="modal_head">
				<i class="icon_close" onclick="modal_plots.modal_hide()"></i>
			</div>
			<div class="modal_body">
				<div class="input_group_modal">
					<div>Status</div>
					<select id="status">
						<option value="0">Free</option>
						<option value="1">Reserved</option>
						<option value="2">Sold</option>
					</select>
				</div>
				<div class="input_group_modal">
					<div>Billing</div>
					<select id="billing">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div>
				<div class="input_group_modal">
					<div>Lot number</div>
					<input type="text" id="number" placeholder="Enter number">
				</div>
				<div class="input_group_modal">
					<div>Size, acres</div>
					<input type="text" id="size" placeholder="Enter size">
				</div>
				<div class="input_group_modal">
					<div>Price, AED</div>
					<input type="text" id="price" placeholder="Enter price">
				</div>
				<div class="modal_controls">
					<div>
						<div class="btn_modal" onclick="modal_plots.plot_add()">Save</div>
					</div>
					<div>
						<div class="btn_modal light" onclick="modal_plots.modal_hide()">Cancel</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>