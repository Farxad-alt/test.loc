<?php
    session_start();
    if (! isset($_SESSION['phone'])) {
        header('Location: /index.php');
        exit;
    }
    include __DIR__ . '/../header.php';
    include __DIR__ . '/../db.php';
    include __DIR__ . '/../classes/PlotManager.php';
    include __DIR__ . '/../modal_users.php';

    $plotManager = new PlotManager($pdo);

    $perPage = 20;
    $page    = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

    $totalUsers = $plotManager->getTotalUsers();

    $totalPages = ($perPage > 0 && $totalUsers > 0) ? ceil($totalUsers / $perPage) : 1;

    $users = $plotManager->getUsersWithPagination($page, $perPage);
?>

<div class="wrap">
	<div class="sub_header">
		<div>
			<div class="btn_sub dn" onclick="modalAdd.modal_show()">Add</div>
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
				<input id="search" type="text" placeholder="Search users" oninput="search.search_do('users');">
			</div>
		</div>
	</div>

	<table id="usersTable">
		<tr>
			<th>Plot ID</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Phone</th>
			<th>Email</th>
			<th>Last Login</th>
			<th></th>
		</tr>

		<?php if (! empty($users)): ?>
		<?php foreach ($users as $user): ?>
		<tr data-user-id="<?php echo htmlspecialchars($user['user_id'] ?? '') ?>">
			<td><?php echo htmlspecialchars($user['plot_id'] ?? '-') ?></td>
			<td><?php echo htmlspecialchars($user['first_name'] ?? '') ?></td>
			<td><?php echo htmlspecialchars($user['last_name'] ?? '') ?></td>
			<td><?php echo htmlspecialchars($user['phone'] ?? '') ?></td>
			<td><?php echo htmlspecialchars($user['email'] ?? '') ?></td>
			<td><?php echo ! empty($user['last_login']) ? date('d M Y - H:i', strtotime($user['last_login'])) : '-' ?></td>
			<td class="right_column">
				<i class="icon icon_ellipsis" onclick="modalEdit.menu_popup_toggle(this, event);">
					<div class="menu_popup">
						<div onclick="modalEdit.edit_user(<?php echo $user['user_id'] ?>)">Edit</div>
						<div onclick="modalEdit.delete_user(<?php echo $user['user_id'] ?>)">Delete</div>
					</div>
				</i>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php else: ?>
		<tr>
			<td colspan="7">Нет записей в таблице.</td>
		</tr>
		<?php endif; ?>
	</table>

</div>


<?php include __DIR__ . '/../footer.php'; ?>
