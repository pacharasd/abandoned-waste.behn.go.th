<?php
namespace App\Core;

/**
 * Paginator - High Performance & Modern Responsive Pagination Engine
 */
class Paginator {
    public int $currentPage;
    public int $perPage;
    public int $totalItems;
    public int $totalPages;
    public int $offset;
    public int $from;
    public int $to;
    public array $items;
    public string $path;
    public array $queryParams;

    public function __construct(array $items, int $totalItems, int $page = 1, int $perPage = 10, string $path = '', array $queryParams = []) {
        $this->totalItems = max(0, $totalItems);
        $this->perPage = max(1, $perPage);
        $this->totalPages = max(1, (int)ceil($this->totalItems / $this->perPage));
        $this->currentPage = max(1, min($page, $this->totalPages));
        $this->offset = ($this->currentPage - 1) * $this->perPage;
        $this->items = $items;
        $this->from = $this->totalItems > 0 ? $this->offset + 1 : 0;
        $this->to = min($this->offset + count($items), $this->totalItems);
        $this->path = $path ?: (strtok($_SERVER['REQUEST_URI'] ?? '', '?') ?: '');
        $this->queryParams = !empty($queryParams) ? $queryParams : $_GET;
    }

    /**
     * Create Paginator by slicing an in-memory array
     */
    public static function fromArray(array $allArrayItems, int $page = 1, int $perPage = 10, string $path = '', array $queryParams = []): self {
        $total = count($allArrayItems);
        $perPage = max(1, $perPage);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;
        $slicedItems = array_slice($allArrayItems, $offset, $perPage);

        return new self($slicedItems, $total, $page, $perPage, $path, $queryParams);
    }

    /**
     * Build URL for a given page number preserving existing query string
     */
    public function url(int $page): string {
        $params = $this->queryParams;
        if ($page <= 1) {
            unset($params['page']);
        } else {
            $params['page'] = $page;
        }

        $queryString = http_build_query($params);
        return htmlspecialchars($this->path . ($queryString ? '?' . $queryString : ''));
    }

    /**
     * Determine page numbers to display with smart ellipsis
     */
    public function getPageNumbers(int $onEachSide = 1): array {
        if ($this->totalPages <= 7) {
            return range(1, $this->totalPages);
        }

        $pages = [];
        $start = max(2, $this->currentPage - $onEachSide);
        $end = min($this->totalPages - 1, $this->currentPage + $onEachSide);

        $pages[] = 1;

        if ($start > 2) {
            $pages[] = '...';
        }

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }

        if ($end < $this->totalPages - 1) {
            $pages[] = '...';
        }

        $pages[] = $this->totalPages;

        return $pages;
    }

    /**
     * Render sleek Tailwind CSS pagination markup
     */
    public function render(): string {
        if ($this->totalItems <= 0) {
            return '';
        }

        $html = '<div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 bg-slate-50/80 border-t border-slate-200/80 text-xs text-slate-600 rounded-b-2xl">';
        
        // Left info
        $html .= '<div class="font-medium text-slate-600 flex items-center gap-1.5">';
        $html .= '<span>แสดงผล</span>';
        $html .= '<span class="font-bold text-slate-900">' . $this->from . '</span>';
        $html .= '<span>ถึง</span>';
        $html .= '<span class="font-bold text-slate-900">' . $this->to . '</span>';
        $html .= '<span>จากทั้งหมด</span>';
        $html .= '<span class="font-bold text-slate-900 font-mono bg-white px-2 py-0.5 rounded-lg border border-slate-200 shadow-2xs">' . number_format($this->totalItems) . '</span>';
        $html .= '<span>รายการ</span>';
        $html .= '</div>';

        // Right buttons (only render pagination links if totalPages > 1)
        if ($this->totalPages > 1) {
            $html .= '<nav aria-label="Pagination" class="flex items-center gap-1.5 flex-wrap justify-center">';

            // Previous Button
            if ($this->currentPage > 1) {
                $html .= '<a href="' . $this->url($this->currentPage - 1) . '" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 font-semibold text-slate-700 transition shadow-2xs">';
                $html .= '<i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>';
                $html .= '<span>ก่อนหน้า</span>';
                $html .= '</a>';
            } else {
                $html .= '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-100 bg-slate-100/70 text-slate-400 font-semibold cursor-not-allowed">';
                $html .= '<i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>';
                $html .= '<span>ก่อนหน้า</span>';
                $html .= '</span>';
            }

            // Page numbers
            $pageNumbers = $this->getPageNumbers();
            foreach ($pageNumbers as $p) {
                if ($p === '...') {
                    $html .= '<span class="px-2 py-1 text-slate-400 font-mono">...</span>';
                } elseif ($p === $this->currentPage) {
                    $html .= '<span class="w-8 h-8 flex items-center justify-center rounded-xl bg-emerald-600 text-white font-bold font-mono shadow-sm shadow-emerald-600/30 ring-1 ring-emerald-600">' . $p . '</span>';
                } else {
                    $html .= '<a href="' . $this->url($p) . '" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700 font-semibold font-mono text-slate-700 transition shadow-2xs">' . $p . '</a>';
                }
            }

            // Next Button
            if ($this->currentPage < $this->totalPages) {
                $html .= '<a href="' . $this->url($this->currentPage + 1) . '" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 hover:text-slate-900 font-semibold text-slate-700 transition shadow-2xs">';
                $html .= '<span>ถัดไป</span>';
                $html .= '<i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>';
                $html .= '</a>';
            } else {
                $html .= '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-100 bg-slate-100/70 text-slate-400 font-semibold cursor-not-allowed">';
                $html .= '<span>ถัดไป</span>';
                $html .= '<i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>';
                $html .= '</span>';
            }

            $html .= '</nav>';
        }

        $html .= '</div>';
        return $html;
    }
}
