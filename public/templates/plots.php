<?php
session_start();
if (!isset($_SESSION['phone'])) {
    header('Location: /index.php');
    exit;
}
    include __DIR__ . '/../header.php';
    include __DIR__ . '/../db.php';
    include __DIR__ . '/../classes/PlotManager.php';
    include __DIR__ . '/../modal_plots.php';

    $plotManager = new PlotManager($pdo);

    $query = isset($_GET['query']) ? $_GET['query'] : '';
    $page  = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

    // Получаем данные
    $plots = $plotManager->searchPlots($query, $page);

    $perPage    = 20;
    $page       = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $totalPlots = $plotManager->getTotalPlots();
    $totalPages = ceil($totalPlots / $perPage);

    $plots = $plotManager->getPlotsWithPagination($page, $perPage);

?>

<div class="wrap">
	<div class="sub_header">
		<div>
			<div class="btn_sub dn" onclick="modal_plots.modal_show()">Add</div>
		</div>
		<div>
			<div id="paginator">
				<div class="pagination_controls">
					<?php for ($i = 1; $i <= $totalPages; $i++): ?>
					<a href="?page=<?php echo $i ?>" class="<?php echo($i === $page) ? 'page-link active' : 'page-link' ?>">
						<?php echo $i ?>
					</a>
					<?php endfor; ?>
				</div>
			</div>
			<div class="input_group">
				<i class="icon_search"></i>
				<input id="search" type="text" placeholder="search " oninput="search.search_do('plots');">
				<table id="plotsTable"></table>

			</div>
		</div>

	</div>

	<?php if (empty($plots)): ?>
	<p>Нет участков для отображения.</p>
	<?php else: ?>
	<table id="plotsTable">
		<thead>
			<tr>
				<th>Plot, number</th>
				<th>Size, acres</th>
				<th>Status</th>
				<th>Billing</th>
				<th>Price</th>
				<th>Owners</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($plots as $plot): ?>
			<tr>
				<td><?php echo htmlspecialchars($plot['number'] ?? '') ?></td>
				<td><?php echo htmlspecialchars($plot['size'] ?? '') ?></td>
				<td><?php echo htmlspecialchars($plot['status'] ?? '') ?></td>
				<td><?php echo htmlspecialchars($plot['billing'] ?? '') ?></td>
				<td><?php echo htmlspecialchars($plot['price'] ?? '') ?> AED</td>
				<td>
					<?php if (! empty($plot['users'])): ?>
					<?php foreach ($plot['users'] as $user): ?>
					<div>
						<?php echo htmlspecialchars($user['first_name'] ?? '') ?>
						<?php echo htmlspecialchars($user['last_name'] ?? '') ?> :
						<span class="gray"><?php echo htmlspecialchars($user['phone'] ?? '') ?></span>
					</div>
					<?php endforeach; ?>
					<?php else: ?>
					-
					<?php endif; ?>
				</td>
				<td class="right_column">
					<i class="icon icon_ellipsis" onclick="common.menu_popup_toggle(this, event);">
						<span class="menu_popup">
							<div>Edit</div>
						</span>
					</i>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>


<?php include __DIR__ . '/../footer.php'; ?>
