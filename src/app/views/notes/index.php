<?php

/**
 * @var array $data
 */

require_once APPROOT . '/views/layouts/header.php';

?>
<div class="container my-4">
    <?php
    $search = $data['search'] ?? '';
    $fromDate = $data['from_date'] ?? '';
    $toDate = $data['to_date'] ?? '';
    $sort = $data['sort'] ?? 'oldest';
    $view = $data['view'] ?? 'all';
    $hasActiveFilters = !empty($search) || !empty($fromDate) || !empty($toDate) || $sort !== 'oldest' || $view !== 'all';
    $ownedNotes = $data['notes'] ?? [];
    $publicNotes = $data['public_notes'] ?? [];
    $totalPages = $data['total_pages'] ?? 1;
    $currentPage = $data['current_page'] ?? 1;
    $totalNotes = $data['total_notes'] ?? 0;
    $perPage = $data['per_page'] ?? 9;
    $ownedIds = [];
    foreach ($ownedNotes as $ownedNote) {
        $ownedIds[(string)$ownedNote->id] = true;
    }
    $allNotes = [];
    foreach ($ownedNotes as $listedNote) {
        $allNotes[(string)$listedNote->id] = $listedNote;
    }
    foreach ($publicNotes as $listedNote) {
        if (!isset($allNotes[(string)$listedNote->id])) {
            $allNotes[(string)$listedNote->id] = $listedNote;
        }
    }
    $displayNotes = array_values($allNotes);
    usort($displayNotes, static function ($left, $right) use ($sort) {
        $comparison = strcmp((string)$left->updated_at, (string)$right->updated_at);
        if ($comparison === 0) {
            $comparison = (int) $left->id <=> (int) $right->id;
        }

        return $sort === 'oldest' ? $comparison : -$comparison;
    });
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">یادداشت‌ها</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNoteModal">افزودن یادداشت</button>
    </div>
    <div class="row g-4 align-items-start" dir="ltr">
        <aside class="col-12 col-lg-3" dir="rtl">
            <form action="<?php echo URLROOT; ?>/public/" method="GET" class="card filter-sidebar shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">فیلترها</h3><input type="hidden" name="page" value="notes">
                    <div class="mb-3"><label for="search" class="form-label">جستجو</label><input type="text" class="form-control" id="search" name="search" placeholder="جستجو در عنوان یا متن" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"></div>
                    <div class="mb-3"><label for="view" class="form-label">نمایش</label><select class="form-select" id="view" name="view">
                            <option value="all" <?php echo $view === 'all' ? 'selected' : ''; ?>>همه یادداشت‌ها</option>
                            <option value="my" <?php echo $view === 'my' ? 'selected' : ''; ?>>یادداشت‌های من</option>
                            <option value="public" <?php echo $view === 'public' ? 'selected' : ''; ?>>یادداشت‌های عمومی</option>
                        </select></div>
                    <div class="mb-3"><label for="sort" class="form-label">مرتب‌سازی</label><select class="form-select" id="sort" name="sort">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>جدیدترین</option>
                            <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>قدیمی‌ترین</option>
                        </select></div>
                    <div class="mb-3"><label for="fromDateDisplay" class="form-label">از تاریخ</label>
                        <div class="input-group"><input type="text" class="form-control" id="fromDateDisplay" placeholder="انتخاب تاریخ" autocomplete="off" readonly><button type="button" class="btn btn-outline-secondary" id="clearFromDate" title="پاک کردن تاریخ">×</button></div><input type="hidden" name="from_date" id="fromDate" value="<?php echo htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3"><label for="toDateDisplay" class="form-label">تا تاریخ</label>
                        <div class="input-group"><input type="text" class="form-control" id="toDateDisplay" placeholder="انتخاب تاریخ" autocomplete="off" readonly><button type="button" class="btn btn-outline-secondary" id="clearToDate" title="پاک کردن تاریخ">×</button></div><input type="hidden" name="to_date" id="toDate" value="<?php echo htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">اعمال فیلتر</button><?php if ($hasActiveFilters): ?><a href="?page=notes" class="btn btn-outline-secondary btn-sm w-100 mt-2">پاک کردن فیلترها</a><?php endif; ?>
                </div>
            </form>
        </aside>
        <section class="col-12 col-lg-9" dir="rtl">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="h4 mb-0"><?php echo $view === 'my' ? 'یادداشت‌های من' : ($view === 'public' ? 'یادداشت‌های عمومی' : 'همه یادداشت‌ها'); ?></h3>
                <?php if ($totalNotes > 0): ?>
                    <?php
                    $startNote = (($currentPage - 1) * $perPage) + 1;
                    $endNote = min($currentPage * $perPage, $totalNotes);
                    ?>
                    <p class="text-muted mb-0" aria-live="polite">
                        نمایش <?php echo $startNote; ?> تا <?php echo $endNote; ?> از <?php echo $totalNotes; ?> یادداشت
                    </p>
                <?php endif; ?>
            </div>

            <?php if (empty($displayNotes)): ?><div class="alert alert-info text-center"><?php echo $hasActiveFilters ? 'یادداشتی مطابق فیلترهای انتخاب‌شده پیدا نشد.' : 'هنوز یادداشتی ثبت نشده است.'; ?></div>
            <?php else: ?><div class="note-cards" dir="rtl"><?php foreach ($displayNotes as $note): $isOwned = isset($ownedIds[(string)$note->id]);
                                                                $isPublic = !$isOwned || !empty($note->is_public); ?>
                        <article class="note-card">
                            <div class="card note-card-inner shadow-sm" dir="rtl">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <h4 class="card-title h5"><?php echo htmlspecialchars($note->title, ENT_QUOTES, 'UTF-8'); ?></h4><span class="badge rounded-pill <?php echo $isPublic ? 'text-bg-success' : 'text-bg-secondary'; ?>"><?php echo $isPublic ? 'عمومی' : 'شخصی'; ?></span>
                                    </div>
                                    <p class="card-text note-body"><?php echo htmlspecialchars($note->body, ENT_QUOTES, 'UTF-8'); ?></p>
                                    <div class="text-muted small note-meta">
                                        <?php if ($note->is_public && isset($note->author_name)): ?>

                                            <div>
                                                نویسنده:
                                                <?php echo htmlspecialchars($note->author_name, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>

                                        <?php endif; ?>
                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-1">
                                            <span>
                                                تاریخ ایجاد: <time class="jalali-date" data-date="<?php echo htmlspecialchars($note->created_at, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(substr($note->created_at, 0, 10), ENT_QUOTES, 'UTF-8'); ?></time>
                                            </span>
                                            <span>ساعت: <?php echo formatTime($note->created_at); ?></span>
                                        </div>
                                        <?php if (!empty($note->updated_at) && $note->updated_at !== $note->created_at): ?>
                                            <div class="d-flex flex-wrap align-items-center gap-3 mb-1">
                                                <span>
                                                    تاریخ ویرایش: <time class="jalali-date" data-date="<?php echo htmlspecialchars($note->updated_at, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(substr($note->updated_at, 0, 10), ENT_QUOTES, 'UTF-8'); ?></time>
                                                </span>
                                                <span>ساعت: <?php echo formatTime($note->updated_at); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($isOwned): ?><div class="d-flex gap-2 mt-3"><button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editNoteModal<?php echo $note->id; ?>">ویرایش</button><button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteNoteModal<?php echo $note->id; ?>">حذف</button></div><?php endif; ?>
                                </div>
                            </div>
                        </article><?php endforeach; ?>
                </div><?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <?php
                $pages = [];

                if ($totalPages <= 4) {

                    for ($i = 1; $i <= $totalPages; $i++) {
                        $pages[] = $i;
                    }

                } else {

                    // همیشه صفحه اول و دوم
                    $pages[] = 1;
                    $pages[] = 2;

                    // اگر فاصله با صفحه فعلی زیاد بود
                    if ($currentPage > 4) {
                        $pages[] = '...';
                    }


                    // صفحات اطراف صفحه فعلی
                    for ($i = max(3, $currentPage - 1); $i <= min($totalPages - 2, $currentPage + 1); $i++) {
                        $pages[] = $i;
                    }


                    // اگر فاصله تا آخر زیاد بود
                    if ($currentPage < $totalPages - 3) {
                        $pages[] = '...';
                    }


                    // فقط آخرین صفحه نمایش داده می‌شود.
                    $pages[] = $totalPages;
                }

                $paginationQuery = http_build_query([
                    'page' => 'notes',
                    'search' => $search,
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'sort' => $sort,
                    'view' => $view,
                ]);
                ?>
                <nav class="notes-pagination" aria-label="&#1589;&#1601;&#1581;&#1607; &#1576;&#1606;&#1583;&#1740;" dir="rtl">
                    <ul class="pagination justify-content-center flex-wrap mt-4">
                        <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                            <?php if ($currentPage <= 1): ?>
                                <span class="page-link" aria-disabled="true">&laquo; &#1602;&#1576;&#1604;&#1740;</span>
                            <?php else: ?>
                                <a class="page-link" href="?<?php echo $paginationQuery; ?>&amp;p=<?php echo $currentPage - 1; ?>">&laquo; &#1602;&#1576;&#1604;&#1740;</a>
                            <?php endif; ?>
                        </li>

                        <?php foreach ($pages as $pageNumber): ?>

                            <?php if ($pageNumber === '...'): ?>

                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>

                            <?php else: ?>

                                <li class="page-item <?php echo $currentPage == $pageNumber ? 'active' : ''; ?>">

                                    <?php if ($currentPage == $pageNumber): ?>

                                        <span class="page-link">
                                            <?php echo $pageNumber; ?>
                                        </span>

                                    <?php else: ?>

                                        <a class="page-link"
                                           href="?<?php echo $paginationQuery; ?>&amp;p=<?php echo $pageNumber; ?>">
                                            <?php echo $pageNumber; ?>
                                        </a>

                                    <?php endif; ?>

                                </li>

                            <?php endif; ?>

                        <?php endforeach; ?>

                        <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                            <?php if ($currentPage >= $totalPages): ?>
                                <span class="page-link" aria-disabled="true">&#1576;&#1593;&#1583;&#1740; &raquo;</span>
                            <?php else: ?>
                                <a class="page-link" href="?<?php echo $paginationQuery; ?>&amp;p=<?php echo $currentPage + 1; ?>">&#1576;&#1593;&#1583;&#1740; &raquo;</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

            <?php foreach ($ownedNotes as $note): ?>
                <!-- ویرایش Modal -->

                <div
                    class="modal fade note-form-modal"
                    id="editNoteModal<?php echo $note->id; ?>"
                    tabindex="-1"
                    dir="rtl">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content shadow border-0 rounded-4">

                            <div class="modal-header border-0">

                                <h5 class="modal-title fw-bold">
                                    ویرایش یادداشت
                                </h5>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="بستن"></button>

                            </div>


                            <form
                                action="?page=notes-edit&amp;id=<?php echo urlencode($note->id); ?>"
                                method="POST"
                                class="note-form">

                                <div class="modal-body">


                                    <div class="mb-3">

                                        <label
                                            class="form-label"
                                            for="editTitle<?php echo $note->id; ?>">
                                            عنوان یادداشت
                                        </label>

                                        <input
                                            type="text"
                                            id="editTitle<?php echo $note->id; ?>"
                                            name="title"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars(
                                                        $note->title,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>">

                                        <small
                                            class="text-danger d-none"
                                            data-error-for="title"></small>

                                    </div>


                                    <div class="mb-3">

                                        <label
                                            class="form-label"
                                            for="editBody<?php echo $note->id; ?>">
                                            متن یادداشت
                                        </label>

                                        <textarea
                                            id="editBody<?php echo $note->id; ?>"
                                            name="body"
                                            class="form-control"
                                            rows="5"><?php echo htmlspecialchars(
                                                            $note->body,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ); ?></textarea>

                                        <small
                                            class="text-danger d-none"
                                            data-error-for="body"></small>

                                    </div>


                                    <div class="form-check">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="is_public"
                                            value="1"
                                            id="editPublic<?php echo $note->id; ?>"
                                            <?php echo !empty($note->is_public) ? 'checked' : ''; ?>>

                                        <label
                                            class="form-check-label"
                                            for="editPublic<?php echo $note->id; ?>">
                                            انتشار عمومی یادداشت
                                        </label>

                                    </div>

                                </div>


                                <div class="modal-footer border-0 flex-column align-items-stretch">

                                    <small
                                        class="text-danger"
                                        data-request-error></small>

                                    <div
                                        class="d-flex justify-content-between"
                                        dir="rtl">

                                        <button
                                            type="button"
                                            class="btn btn-light border"
                                            data-bs-dismiss="modal">
                                            لغو
                                        </button>

                                        <button
                                            type="submit"
                                            class="btn btn-primary">
                                            ذخیره تغییرات
                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>


                <!-- حذف Modal -->

                <div
                    class="modal fade"
                    id="deleteNoteModal<?php echo $note->id; ?>"
                    data-bs-backdrop="static"
                    data-bs-keyboard="false"
                    tabindex="-1"
                    aria-labelledby="deleteNoteModalLabel<?php echo $note->id; ?>"
                    aria-hidden="true"
                    dir="rtl">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content shadow border-0 rounded-4">

                            <div class="modal-header">

                                <h1
                                    class="modal-title fs-5"
                                    id="deleteNoteModalLabel<?php echo $note->id; ?>">
                                    تأیید حذف یادداشت
                                </h1>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="بستن"></button>

                            </div>


                            <div class="modal-body">

                                آیا مطمئن هستید که می‌خواهید این یادداشت را حذف کنید؟
                                این عملیات قابل بازگشت نیست.

                            </div>


                            <div class="modal-footer">

                                <form
                                    action="?page=notes-delete"
                                    method="POST"
                                    class="w-100">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo htmlspecialchars(
                                                    $note->id,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>">

                                    <input type="hidden" name="view" value="<?php echo htmlspecialchars($view, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="p" value="<?php echo (int) $currentPage; ?>">

                                    <div
                                        class="d-flex justify-content-between"
                                        dir="rtl">

                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal">
                                            لغو
                                        </button>

                                        <button
                                            type="submit"
                                            class="btn btn-danger">
                                            حذف یادداشت
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>


            <?php endforeach; ?>
        </section>
    </div>
</div>

<div
    class="modal fade note-form-modal"
    id="createNoteModal"
    tabindex="-1"
    dir="rtl"
    data-open="<?php echo !empty($data['open_modal']) ? 'true' : 'false'; ?>">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content shadow border-0 rounded-4">


            <div class="modal-header border-0">

                <h5 class="modal-title fw-bold">
                    ایجاد یادداشت جدید
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>

            </div>


            <form
                action="?page=notes-create"
                method="POST"
                id="createNoteForm"
                class="note-form">

                <div class="modal-body">


                    <div class="mb-3">

                        <label class="form-label">
                            عنوان یادداشت
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?php echo htmlspecialchars(
                                        $data['title'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>">


                        <?php if (!empty($data['title_err'])): ?>

                            <small class="text-danger">

                                <?php echo htmlspecialchars(
                                    $data['title_err'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </small>

                        <?php endif; ?>


                        <small
                            class="text-danger d-none"
                            data-error-for="title"></small>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            متن یادداشت
                        </label>

                        <textarea
                            name="body"
                            class="form-control"
                            rows="5"><?php echo htmlspecialchars(
                                            $data['body'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?></textarea>


                        <?php if (!empty($data['body_err'])): ?>

                            <small class="text-danger">

                                <?php echo htmlspecialchars(
                                    $data['body_err'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </small>

                        <?php endif; ?>


                        <small
                            class="text-danger d-none"
                            data-error-for="body"></small>

                    </div>


                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="is_public"
                            value="1"
                            id="createPublic">

                        <label
                            class="form-check-label"
                            for="createPublic">
                            انتشار عمومی یادداشت
                        </label>

                    </div>

                </div>


                <div class="modal-footer border-0">

                    <div
                        class="d-flex justify-content-between w-100"
                        dir="rtl">

                        <button
                            type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal">
                            لغو
                        </button>


                        <button
                            type="submit"
                            class="btn btn-primary">
                            ذخیره
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<?php

require_once APPROOT . '/views/layouts/footer.php';

?>
