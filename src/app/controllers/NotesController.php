<?php

class NotesController extends Controller
{
    private $noteModel;

    public function __construct()
    {
        $this->noteModel = $this->model('Note');
    }

    public function index()
    {

        if (!isLoggedIn()) {
            redirect('login');
        }

        $search = trim($_GET['search'] ?? '');
        $fromDate = trim($_GET['from_date'] ?? '');
        $toDate = trim($_GET['to_date'] ?? '');
        $sort = $_GET['sort'] ?? 'oldest';
        $view = $_GET['view'] ?? 'all';

        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        if ($page < 1) {
            $page = 1;
        }

        $perPage = 9;

        if (!in_array($sort, ['newest', 'oldest'], true)) {
            $sort = 'oldest';
        }

        if (!in_array($view, ['all', 'my', 'public'], true)) {
            $view = 'all';
        }

        $totalNotes = 0;

        switch ($view) {

            case 'my':

                $totalNotes = $this->noteModel->countNotesByUser(
                    $_SESSION['user_id'],
                    $search,
                    $fromDate,
                    $toDate
                );

                break;


            case 'public':

                $totalNotes = $this->noteModel->countPublicNotes(
                    $search,
                    $fromDate,
                    $toDate
                );

                break;


            case 'all':

            default:

                $totalNotes = $this->noteModel->countVisibleNotes(
                    $_SESSION['user_id'],
                    $search,
                    $fromDate,
                    $toDate
                );

                break;
        }

        $totalPages = (int) ceil($totalNotes / $perPage);
        $page = min($page, max(1, $totalPages));
        $offset = ($page - 1) * $perPage;
        $myNotes = [];
        $publicNotes = [];

        if ($view === 'all') {
            $allOwnedNotes = $this->noteModel->getNotesByUser(
                $_SESSION['user_id'], $search, $fromDate, $toDate, $sort
            );
            $allPublicNotes = $this->noteModel->getPublicNotes(
                $search, $fromDate, $toDate, $sort
            );

            $ownedById = [];
            foreach ($allOwnedNotes as $note) {
                $ownedById[(string) $note->id] = $note;
            }

            $allNotes = $ownedById;
            foreach ($allPublicNotes as $note) {
                if (!isset($allNotes[(string) $note->id])) {
                    $allNotes[(string) $note->id] = $note;
                }
            }

            $allNotes = array_values($allNotes);
            usort($allNotes, static function ($left, $right) use ($sort) {
                $comparison = strcmp((string) $left->updated_at, (string) $right->updated_at);
                if ($comparison === 0) {
                    $comparison = (int) $left->id <=> (int) $right->id;
                }

                return $sort === 'oldest' ? $comparison : -$comparison;
            });

            $pageNotes = array_slice($allNotes, $offset, $perPage);
            foreach ($pageNotes as $note) {
                if (isset($ownedById[(string) $note->id])) {
                    $myNotes[] = $ownedById[(string) $note->id];
                } else {
                    $publicNotes[] = $note;
                }
            }
        } else {
            if ($view === 'my') {
                $myNotes = $this->noteModel->getNotesByUser(
                    $_SESSION['user_id'], $search, $fromDate, $toDate, $sort, $perPage, $offset
                );
            }

            if ($view === 'public') {
                $publicNotes = $this->noteModel->getPublicNotes(
                    $search, $fromDate, $toDate, $sort, $perPage, $offset
                );

               
                $myNotes = array_values(array_filter(
                    $publicNotes,
                    static fn($note) => (int) $note->user_id === (int) $_SESSION['user_id']
                ));
            }
        }

        $data = [
            'notes' => $myNotes,
            'public_notes' => $publicNotes,
 
            'search' => $search,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'sort' => $sort,
            'view' => $view,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'total_notes' => $totalNotes,

            'title' => '',
            'body' => '',
            'title_err' => '',
            'body_err' => ''
        ];


        $this->view('notes/index', $data);
    }

    public function create()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        $data = [
            'title' => '',
            'body' => '',
            'title_err' => '',
            'body_err' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['title'] = trim($_POST['title']);
            $data['body'] = trim($_POST['body']);
            $is_public = isset($_POST['is_public']) ? 1 : 0;
            
            if (empty($data['title'])) {
                $data['title_err'] = "لطفا عنوان یادداشت را وارد کنید.";
            }

            if (empty($data['body'])) {
                $data['body_err'] = "لطفا متن یادداشت خود را انتخاب کنید.";
            }

            if (!empty($data['title_err']) || (!empty($data['body_err']))) {

                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    http_response_code(422);
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => false,
                        'title_err' => $data['title_err'],
                        'body_err' => $data['body_err']
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
            }

            if (empty($data['title_err']) && empty($data['body_err'])) {
                $noteData = [
                    'user_id' => $_SESSION['user_id'],
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'is_public' => $is_public
                ];

                if ($this->noteModel->addNote($noteData)) {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
                        return;
                    }
                    redirect('notes');
                }
            }
        }

        $myNotes = $this->noteModel->getNotesByUser($_SESSION['user_id']);

        $publicNotes = $this->noteModel->getPublicNotes();


        $data['notes'] = $myNotes;

        $data['public_notes'] = $publicNotes;

        $this->view('notes/index', $data);
    }

    public function edit()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        $id = $_GET['id'] ?? null;

        if (!$id) {
            redirect('notes');
        }

        $note = $this->noteModel->getNoteById($id, $_SESSION['user_id']);

        if (!$note) {
            redirect('notes');
        }

        $data = [
            'id' => $note->id,
            'user_id' => $_SESSION['user_id'],
            'title' => $note->title,
            'body' => $note->body,
            'is_public' => $note->is_public,
            'title_err' => '',
            'body_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['title'] = trim($_POST['title']);
            $data['body'] = trim($_POST['body']);
            $data['is_public'] = isset($_POST['is_public']) ? 1 : 0;

            if (empty($data['title'])) {
                $data['title_err'] = "لطفا عنوان یادداشت خود را وارد کنید.";
            }

            if (empty($data['body'])) {
                $data['body_err'] = "لطفا متن یادداشت خود را وارد کنید.";
            }

            if (empty($data['title_err']) && empty($data['body_err'])) {
                if ($this->noteModel->updateNote($data)) {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
                        return;
                    }
                    redirect('notes');
                }
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                http_response_code(422);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'title_err' => $data['title_err'],
                    'body_err' => $data['body_err']
                ], JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        // Editing is handled by the modal on the notes page. AJAX submissions
        // return JSON above; direct/non-AJAX requests return to that page.
        redirect('notes');
    }

    public function delete()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }

        $id = $_POST['id'] ?? $_GET['id'] ?? null;

        if (!$id) {

            redirect('notes');
        }

        if ($this->noteModel->deleteNote($id, $_SESSION['user_id'])) {
            $returnView = $_POST['view'] ?? 'all';
            if (!in_array($returnView, ['all', 'my', 'public'], true)) {
                $returnView = 'all';
            }

            $returnSort = $_POST['sort'] ?? 'oldest';
            if (!in_array($returnSort, ['newest', 'oldest'], true)) {
                $returnSort = 'oldest';
            }

            $search = $_POST['search'] ?? '';
            $fromDate = $_POST['from_date'] ?? '';
            $toDate = $_POST['to_date'] ?? '';
            $page = max(1, (int) ($_POST['p'] ?? 1));

            $returnQuery = http_build_query([
                'view' => $returnView,
                'search' => is_string($search) ? $search : '',
                'from_date' => is_string($fromDate) ? $fromDate : '',
                'to_date' => is_string($toDate) ? $toDate : '',
                'sort' => $returnSort,
                'p' => $page,
            ]);

            redirect('notes&' . $returnQuery);
        } else {
            echo "خطا در حذف یادداشت";
        }
    }
}
