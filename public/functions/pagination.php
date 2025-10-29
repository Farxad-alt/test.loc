<?php
/**
 * Выводит пагинацию
 *
 * @param int $currentPage Текущая страница
 * @param int $totalPages Общее количество страниц
 * @param string $baseUrl Базовый URL для ссылок (например, '/plots/?')
 */
function renderPagination($currentPage, $totalPages, $baseUrl = '?')
{
    echo '
    <div class="pagination_controls">
        <!-- Previous -->
        ' . ($currentPage > 1 ? '
            <a href="' . htmlspecialchars($baseUrl . 'page=' . max(1, $currentPage - 1)) . '" class="page-link">←</a>
        ' : '') . '

        <!-- Pages -->
        ' . implode('', array_map(function ($i) use ($currentPage, $baseUrl) {
        return '
                <a href="' . htmlspecialchars($baseUrl . 'page=' . $i) . '"
                   class="page-link' . ($i === $currentPage ? ' active' : '') . '">
                    ' . $i . '
                </a>';
    }, range(1, $totalPages))) . '

        <!-- Next -->
        ' . ($currentPage < $totalPages ? '
            <a href="' . htmlspecialchars($baseUrl . 'page=' . min($totalPages, $currentPage + 1)) . '" class="page-link">→</a>
        ' : '') . '
    </div>';
}
