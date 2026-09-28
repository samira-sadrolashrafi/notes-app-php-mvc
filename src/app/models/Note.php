<?php


class Note
{

    private $db;

    public function __construct()
    {

        $this->db = new Database();
    }

    public function getNotesByUser($user_id, $search = '', $fromDate = '', $toDate = '', $sort = 'oldest', $limit = null, $offset = null)
    {

        $sql = "SELECT notes.*, users.username AS author_name FROM notes INNER JOIN users ON notes.user_id = users.id WHERE notes.user_id = :user_id";

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search_title OR body LIKE :search_body)";
        }

        if (!empty($fromDate)) {
            $sql .= " AND (notes.created_at >= :from_date)";
        }

        if (!empty($toDate)) {
            $sql .= " AND (notes.created_at <= :to_date)";
        }

        switch ($sort) {
            case 'newest':
                $sql .= " ORDER BY notes.created_at DESC, notes.id DESC";
                break;
            case 'oldest':
            default:
                $sql .= " ORDER BY notes.created_at ASC, notes.id ASC";
                break;
        }

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $this->db->query($sql);

        $this->db->bind(':user_id', $user_id);

        if (!empty($search)) {
            $search_value = '%' . $search . '%';

            $this->db->bind(':search_title', $search_value);
            $this->db->bind(':search_body', $search_value);
        }

        if (!empty($fromDate)) {
            $this->db->bind(':from_date', $fromDate . ' 00:00:00');
        }

        if (!empty($toDate)) {
            $this->db->bind(':to_date', $toDate . ' 23:59:59');
        }

        if ($limit !== null) {
            $this->db->bind(':limit', (int) $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int) $offset, PDO::PARAM_INT);
        }

        return $this->db->allResult();
    }



    public function addNote($data)
    {
        $this->db->query("INSERT INTO notes (user_id, title, body, is_public, created_at, updated_at) VALUES (:user_id, :title, :body, :is_public, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");

        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);
        $this->db->bind(':is_public', $data['is_public']);

        return $this->db->execute();
    }

    public function getNoteById($id, $user_id)
    {
        $this->db->query("SELECT * FROM notes WHERE id = :id AND user_id = :user_id");

        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);

        return $this->db->singleResult();
    }

    public function updateNote($data)
    {
        $this->db->query("UPDATE notes SET title = :title, body = :body, is_public = :is_public, updated_at = CURRENT_TIMESTAMP WHERE id = :id AND user_id = :user_id");

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);
        $this->db->bind(':is_public', $data['is_public']);

        return $this->db->execute();
    }

    public function deleteNote($id, $user_id)
    {
        $this->db->query("DELETE FROM notes WHERE id = :id AND user_id = :user_id");

        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);

        return $this->db->execute();
    }

    public function getPublicNotes($search = '', $fromDate = '', $toDate = '', $sort = 'oldest', $limit = null, $offset = null)
    {

        $sql = "SELECT notes.id, notes.user_id, notes.title, notes.body, notes.created_at, notes.updated_at, notes.is_public, users.username AS author_name FROM notes INNER JOIN users ON notes.user_id = users.id WHERE notes.is_public = :is_public";
        if (!empty($search)) {
            $sql .= " AND(title LIKE :search_title OR body LIKE :search_body)";
        }

        if (!empty($fromDate)) {
            $sql .= " AND notes.created_at >= :from_date";
        }

        if (!empty($toDate)) {
            $sql .= " AND notes.created_at <= :to_date";
        }

        if ($sort === 'oldest') {
            $sql .= " ORDER BY notes.created_at ASC, notes.id ASC";
        } else {
            $sql .= " ORDER BY notes.created_at DESC, notes.id DESC";
        }

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $this->db->query($sql);

        $this->db->bind(':is_public', 1);

        if (!empty($search)) {
            $search_value = "%$search%";
            $this->db->bind(':search_title', $search_value);
            $this->db->bind(':search_body', $search_value);
        }

        if (!empty($fromDate)) {
            $this->db->bind(':from_date', $fromDate . ' 00:00:00');
        }

        if (!empty($toDate)) {
            $this->db->bind(':to_date', $toDate . ' 23:59:59');
        }

        if ($limit !== null) {
            $this->db->bind(':limit', (int) $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int) $offset, PDO::PARAM_INT);
        }

        return $this->db->allResult();
    }


    public function countVisibleNotes($user_id, $search = '', $fromDate = '', $toDate = '')
    {
        $sql = "SELECT COUNT(*) AS total FROM notes WHERE (user_id = :user_id OR is_public = :is_public)";

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search_title OR body LIKE :search_body)";
        }

        if (!empty($fromDate)) {
            $sql .= " AND created_at >= :from_date";
        }

        if (!empty($toDate)) {
            $sql .= " AND created_at <= :to_date";
        }

        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':is_public', 1);

        if (!empty($search)) {
            $search_value = '%' . $search . '%';
            $this->db->bind(':search_title', $search_value);
            $this->db->bind(':search_body', $search_value);
        }

        if (!empty($fromDate)) {
            $this->db->bind(':from_date', $fromDate . ' 00:00:00');
        }

        if (!empty($toDate)) {
            $this->db->bind(':to_date', $toDate . ' 23:59:59');
        }

        $result = $this->db->singleResult();
        return $result->total;
    }

    public function countNotesByUser($user_id, $search = '', $fromDate = '', $toDate = '')
    {
        $sql = "SELECT COUNT(*) AS total FROM notes WHERE user_id = :user_id";

        if (!empty($search)) {
            $sql .= " AND(title LIKE :search_title OR body LIKE :search_body) ";
        }

        if (!empty($fromDate)) {
            $sql .= " AND created_at >= :from_date";
        }

        if (!empty($toDate)) {
            $sql .= " AND created_at <= :to_date";
        }

        $this->db->query($sql);

        $this->db->bind(':user_id', $user_id);

        if (!empty($search)) {
            $search_value = '%' . $search . '%';

            $this->db->bind(':search_title', $search_value);
            $this->db->bind(':search_body', $search_value);
        }

        if (!empty($fromDate)) {
            $this->db->bind(':from_date', $fromDate . ' 00:00:00');
        }

        if (!empty($toDate)) {
            $this->db->bind(':to_date', $toDate . ' 23:59:59');
        }

        $result = $this->db->singleResult();

        return $result->total;
    }

    public function countPublicNotes($search = '', $fromDate = '', $toDate = '')
    {
        $sql = "SELECT COUNT(*) AS total FROM notes WHERE is_public = :is_public";

        if (!empty($search)) {
            $sql .= " AND(title LIKE :search_title OR body LIKE :search_body) ";
        }

        if (!empty($fromDate)) {
            $sql .= " AND created_at >= :from_date";
        }

        if (!empty($toDate)) {
            $sql .= " AND created_at <= :to_date";
        }

        $this->db->query($sql);

        $this->db->bind(':is_public', 1);

        if (!empty($search)) {
            $search_value = '%' . $search . '%';

            $this->db->bind(':search_title', $search_value);
            $this->db->bind(':search_body', $search_value);
        }

        if (!empty($fromDate)) {
            $this->db->bind(':from_date', $fromDate . ' 00:00:00');
        }

        if (!empty($toDate)) {
            $this->db->bind(':to_date', $toDate . ' 23:59:59');
        }

        $result = $this->db->singleResult();

        return $result->total;
    }
}
