<?php
abstract class ResourceController extends Controller
{
    protected string $resourceKey = '';
    protected string $controllerName = '';
    protected string $listAction = '';
    protected string $pageTitle = '';
    protected array $afterCreate = [];
    protected array $afterEdit = [];
    protected array $afterDelete = [];
    protected bool $ownOnly = false;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new Repository();
    }

    public function index(): void
    {
        $this->guardRead();
        $cfg = $this->repo->config($this->resourceKey);
        $rows = $this->shouldLimitToCurrentMember()
            ? $this->repo->allForMember($this->resourceKey, (string)current_member_id())
            : $this->repo->all($this->resourceKey);
        $this->render('generic/list', [
            'title' => $this->pageTitle ?: $cfg['title'],
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'cfg' => $cfg,
            'rows' => $rows,
            'canWrite' => $this->canWrite(),
        ]);
    }

    public function searchMembers(string $actionName): void
    {
        $this->guardRead();
        $ma = trim($_GET['mathanhvien'] ?? '');
        $ten = trim($_GET['hoten'] ?? '');
        $rows = $this->repo->searchMembers($ma, $ten);
        $this->render('generic/list', [
            'title' => $this->pageTitle ?: 'Tìm kiếm thành viên',
            'controller' => $this->controllerName,
            'listAction' => $actionName,
            'cfg' => $this->repo->config('ThanhVien'),
            'rows' => $rows,
            'search' => 'members',
            'searchValues' => ['mathanhvien' => $ma, 'hoten' => $ten],
            'emptyMessage' => $rows ? '' : 'Không tìm thấy sinh viên này.',
            'canWrite' => $this->canWrite(),
        ]);
    }

    public function searchEvents(string $actionName): void
    {
        $this->guardRead();
        $ma = trim($_GET['maSuKien'] ?? '');
        $ten = trim($_GET['tenSuKien'] ?? '');
        $rows = $this->repo->searchEvents($ma, $ten);
        $this->render('generic/list', [
            'title' => $this->pageTitle ?: 'Tìm kiếm sự kiện',
            'controller' => $this->controllerName,
            'listAction' => $actionName,
            'cfg' => $this->repo->config('SuKien'),
            'rows' => $rows,
            'search' => 'events',
            'searchValues' => ['maSuKien' => $ma, 'tenSuKien' => $ten],
            'emptyMessage' => $rows ? '' : 'Không tìm thấy sự kiện phù hợp.',
            'canWrite' => $this->canWrite(),
        ]);
    }

    public function Details(...$params): void
    {
        $this->guardRead();
        $cfg = $this->repo->config($this->resourceKey);
        $keys = $this->keysFromRequest($cfg, $params);
        $row = $this->repo->find($this->resourceKey, $keys);
        if (!$row) {
            $this->notFound();
            return;
        }
        $this->denyIfNotOwner($row);
        $this->render('generic/details', [
            'title' => 'Thông tin chi tiết ' . lower_text($cfg['title']),
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'cfg' => $cfg,
            'row' => $row,
            'keys' => $keys,
            'canWrite' => $this->canWrite(),
        ]);
    }

    public function Create(): void
    {
        $this->guardWrite();
        $cfg = $this->repo->config($this->resourceKey);
        if ($this->isPost()) {
            try {
                $data = $this->collectData($cfg);
                Validator::validateResource($cfg, $data);
                $this->repo->insert($this->resourceKey, $data);
                $this->goAfter($this->afterCreate ?: ['controller' => $this->controllerName, 'action' => $this->listAction]);
            } catch (Throwable $e) {
                $this->renderForm($cfg, $_POST, 'Create', 'Thêm ' . lower_text($cfg['title']), $e->getMessage());
            }
            return;
        }
        $this->renderForm($cfg, [], 'Create', 'Thêm ' . lower_text($cfg['title']));
    }

    public function Edit(...$params): void
    {
        $this->guardWrite();
        $cfg = $this->repo->config($this->resourceKey);
        $keys = $this->keysFromRequest($cfg, $params);
        $row = $this->repo->find($this->resourceKey, $keys);
        if (!$row) {
            $this->notFound();
            return;
        }
        $this->denyIfNotOwner($row);
        if ($this->isPost()) {
            try {
                $data = $this->collectData($cfg, $row);
                Validator::validateResource($cfg, $data);
                $this->repo->update($this->resourceKey, $keys, $data);
                $this->goAfter($this->afterEdit ?: ['controller' => $this->controllerName, 'action' => $this->listAction]);
            } catch (Throwable $e) {
                $this->renderForm($cfg, array_merge($row, $_POST), 'Edit', 'Cập nhật ' . lower_text($cfg['title']), $e->getMessage(), $keys);
            }
            return;
        }
        $this->renderForm($cfg, $row, 'Edit', 'Cập nhật ' . lower_text($cfg['title']), '', $keys);
    }

    public function Delete(...$params): void
    {
        $this->guardWrite();
        $cfg = $this->repo->config($this->resourceKey);
        $keys = $this->keysFromRequest($cfg, $params);
        $row = $this->repo->find($this->resourceKey, $keys);
        if (!$row) {
            $this->notFound();
            return;
        }
        $this->denyIfNotOwner($row);
        if ($this->isPost()) {
            try {
                $this->repo->delete($this->resourceKey, $keys);
                $this->goAfter($this->afterDelete ?: ['controller' => $this->controllerName, 'action' => $this->listAction]);
            } catch (Throwable $e) {
                $this->renderDelete($cfg, $row, $keys, 'Không thể xóa vì dữ liệu đang được sử dụng ở bảng khác. ' . $e->getMessage());
            }
            return;
        }
        $this->renderDelete($cfg, $row, $keys);
    }

    protected function renderProfile(string $editAction): void
    {
        $this->requireLogin();
        $member = $this->repo->findMemberByEmail(current_email());
        if (!$member) {
            redirect_to('Login_64131060', 'Login_64131060');
        }
        $this->render('generic/profile', [
            'title' => 'Trang cá nhân',
            'controller' => $this->controllerName,
            'editAction' => $editAction,
            'cfg' => $this->repo->config('ThanhVien'),
            'row' => $member,
            'canWrite' => true,
        ]);
    }

    protected function renderAlert(string $title, string $message, string $buttonText, string $controller, string $action): void
    {
        $this->render('generic/message', [
            'title' => $title,
            'message' => $message,
            'buttonText' => $buttonText,
            'buttonUrl' => url_for($controller, $action),
        ]);
    }

    protected function renderForm(array $cfg, array $row, string $action, string $title, string $error = '', array $keys = []): void
    {
        $relations = [];
        foreach ($cfg['fields'] as $field => $meta) {
            if (($meta['type'] ?? '') === 'select' && isset($meta['relation'])) {
                $relations[$field] = $this->repo->options($meta['relation']);
            }
        }
        $this->render('generic/form', compact('cfg', 'row', 'action', 'title', 'error', 'keys', 'relations') + [
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'canWrite' => $this->canWrite(),
        ]);
    }

    protected function renderDelete(array $cfg, array $row, array $keys, string $error = ''): void
    {
        $this->render('generic/delete', [
            'title' => 'Xóa ' . lower_text($cfg['title']),
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'cfg' => $cfg,
            'row' => $row,
            'keys' => $keys,
            'error' => $error,
            'canWrite' => $this->canWrite(),
        ]);
    }

    protected function keysFromRequest(array $cfg, array $params): array
    {
        $keys = [];
        foreach ($cfg['pk'] as $index => $pk) {
            if (isset($_POST[$pk])) {
                $keys[$pk] = $_POST[$pk];
            } elseif (isset($_GET[$pk])) {
                $keys[$pk] = $_GET[$pk];
            } elseif (isset($params[$index])) {
                $keys[$pk] = $params[$index];
            } elseif (isset($_GET['id']) && count($cfg['pk']) === 1) {
                $keys[$pk] = $_GET['id'];
            }
        }
        return $keys;
    }

    protected function collectData(array $cfg, array $existing = []): array
    {
        $data = [];
        foreach ($cfg['fields'] as $field => $meta) {
            $type = $meta['type'] ?? 'text';
            if (($meta['readonly'] ?? false) && in_array($field, $cfg['auto'] ?? [], true)) {
                continue;
            }
            if ($type === 'image') {
                $data[$field] = $this->handleUpload($field, $existing[$field] ?? ($_POST[$field] ?? ''));
                continue;
            }
            $value = $_POST[$field] ?? '';
            if (($meta['nullable'] ?? false) && trim((string)$value) === '') {
                $data[$field] = null;
                continue;
            }
            if ($type === 'datetime') {
                $value = $value === '' ? date('Y-m-d H:i:s') : str_replace('T', ' ', $value);
                if (strlen($value) === 16) {
                    $value .= ':00';
                }
            }
            if ($type === 'date' && $value === '') {
                $value = date('Y-m-d');
            }
            $data[$field] = $value;
        }
        return $data;
    }

    private function handleUpload(string $field, string $current = ''): string
    {
        $inputName = $field . '_upload';
        if (isset($_FILES[$inputName]) && is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
            Validator::validateImageUpload($_FILES[$inputName]);
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES[$inputName]['name']));
            $target = PUBLIC_PATH . '/Image/' . $safe;
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }
            move_uploaded_file($_FILES[$inputName]['tmp_name'], $target);
            return $safe;
        }
        return $_POST[$field] ?? $current;
    }

    protected function goAfter(array $route): void
    {
        redirect_to($route['controller'] ?? $this->controllerName, $route['action'] ?? $this->listAction, $route['params'] ?? []);
    }

    protected function guardRead(): void
    {
        $roles = $this->rolesForController();
        if (!$roles) {
            return;
        }
        $this->requireRoles($roles);
    }

    protected function guardWrite(): void
    {
        $roles = $this->writeRolesForController();
        if (!$roles) {
            $this->denyUnauthorized();
        }
        $this->requireRoles($roles);
    }

    protected function canWrite(): bool
    {
        $roles = $this->writeRolesForController();
        return (bool)($roles && current_role() && in_array((string)current_role(), $roles, true));
    }

    protected function rolesForController(): array
    {
        if (str_contains($this->controllerName, '_Admin_')) {
            return ['TVCN'];
        }
        if (str_contains($this->controllerName, '_Assitant_')) {
            return ['TVTG'];
        }
        if (str_contains($this->controllerName, '_Member_')) {
            return ['TV'];
        }
        return [];
    }

    protected function writeRolesForController(): array
    {
        if (str_contains($this->controllerName, '_Admin_')) {
            return ['TVCN'];
        }
        if (str_contains($this->controllerName, '_Assitant_')) {
            return ['TVTG'];
        }
        if (str_contains($this->controllerName, '_Member_') && in_array($this->resourceKey, ['ThanhVien', 'DiemDanh'], true)) {
            return ['TV'];
        }
        return [];
    }

    protected function shouldLimitToCurrentMember(): bool
    {
        return current_member_id()
            && ($this->ownOnly || (str_contains($this->controllerName, '_Member_') && in_array($this->resourceKey, ['ThanhVien', 'DiemDanh', 'DiemRenLuyen', 'TongDiemRenLuyen', 'ChungNhan'], true)));
    }

    protected function denyIfNotOwner(array $row): void
    {
        if ($this->shouldLimitToCurrentMember() && isset($row['MaThanhVien']) && (string)$row['MaThanhVien'] !== (string)current_member_id()) {
            $this->denyUnauthorized();
        }
    }
}
