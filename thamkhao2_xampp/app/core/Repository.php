<?php
class Repository
{
    private PDO $db;
    private array $resources;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->resources = require APP_PATH . '/config/resources.php';
    }

    public function config(string $resource): array
    {
        if (!isset($this->resources[$resource])) {
            throw new InvalidArgumentException('Unknown resource: ' . $resource);
        }
        return $this->resources[$resource];
    }

    public function all(string $resource): array
    {
        $cfg = $this->config($resource);
        $sql = $this->selectSql($cfg) . (!empty($cfg['order']) ? ' ORDER BY ' . $cfg['order'] : '');
        return $this->db->query($sql)->fetchAll();
    }

    public function find(string $resource, array $keys): ?array
    {
        $cfg = $this->config($resource);
        $where = $this->whereForPk($cfg, $keys);
        $stmt = $this->db->prepare($this->selectSql($cfg) . ' WHERE ' . $where['sql'] . ' LIMIT 1');
        $stmt->execute($where['params']);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(string $resource, array $data): void
    {
        $cfg = $this->config($resource);
        foreach (($cfg['auto'] ?? []) as $auto) {
            if (($data[$auto] ?? '') === '') {
                unset($data[$auto]);
            }
        }
        $columns = array_keys($data);
        $sql = 'INSERT INTO ' . $cfg['table'] . ' (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', $columns) . ')';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    public function update(string $resource, array $keys, array $data): void
    {
        $cfg = $this->config($resource);
        foreach (array_merge($cfg['pk'], $cfg['auto'] ?? []) as $skip) {
            unset($data[$skip]);
        }
        $sets = [];
        $params = [];
        foreach ($data as $column => $value) {
            $sets[] = $column . ' = :set_' . $column;
            $params['set_' . $column] = $value;
        }
        $where = $this->whereForPk($cfg, $keys, 'pk_');
        $stmt = $this->db->prepare('UPDATE ' . $cfg['table'] . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where['sql']);
        $stmt->execute(array_merge($params, $where['params']));
    }

    public function delete(string $resource, array $keys): void
    {
        $cfg = $this->config($resource);
        $where = $this->whereForPk($cfg, $keys);
        $stmt = $this->db->prepare('DELETE FROM ' . $cfg['table'] . ' WHERE ' . $where['sql']);
        $stmt->execute($where['params']);
    }

    public function options(array $relation): array
    {
        $sql = sprintf('SELECT %s AS value, %s AS label FROM %s ORDER BY %s', $relation['value'], $relation['label'], $relation['table'], $relation['label']);
        return $this->db->query($sql)->fetchAll();
    }

    public function searchMembers(string $maThanhVien, string $hoTen): array
    {
        $stmt = $this->db->prepare("SELECT ThanhVien.*, VaiTro.TenVaiTro FROM ThanhVien LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro WHERE (:ma = '' OR ThanhVien.MaThanhVien LIKE :maLike) AND (:ten = '' OR ThanhVien.HoTen LIKE :tenLike) ORDER BY ThanhVien.MaThanhVien ASC");
        $stmt->execute(['ma' => $maThanhVien, 'maLike' => '%' . $maThanhVien . '%', 'ten' => $hoTen, 'tenLike' => '%' . $hoTen . '%']);
        return $stmt->fetchAll();
    }

    public function searchEvents(string $maSuKien, string $tenSuKien): array
    {
        $stmt = $this->db->prepare("SELECT SuKien.*, ThanhVien.HoTen AS NguoiToChucTen FROM SuKien LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = SuKien.NguoiToChuc WHERE (:ma = '' OR SuKien.MaSuKien LIKE :maLike) AND (:ten = '' OR SuKien.TenSuKien LIKE :tenLike) ORDER BY SuKien.NgayBatDau DESC, SuKien.MaSuKien ASC");
        $stmt->execute(['ma' => $maSuKien, 'maLike' => '%' . $maSuKien . '%', 'ten' => $tenSuKien, 'tenLike' => '%' . $tenSuKien . '%']);
        return $stmt->fetchAll();
    }

    public function findMemberByEmail(?string $email): ?array
    {
        if (!$email) {
            return null;
        }
        $stmt = $this->db->prepare('SELECT ThanhVien.*, VaiTro.TenVaiTro FROM ThanhVien LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro WHERE ThanhVien.Email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function login(string $email, string $password): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ThanhVien WHERE Email = :email AND MatKhau = :password LIMIT 1');
        $stmt->execute(['email' => $email, 'password' => $password]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function selectSql(array $cfg): string
    {
        return 'SELECT ' . ($cfg['select'] ?? ($cfg['table'] . '.*')) . ' FROM ' . $cfg['table'] . (!empty($cfg['join']) ? ' ' . $cfg['join'] : '');
    }

    private function whereForPk(array $cfg, array $keys, string $prefix = ''): array
    {
        $parts = [];
        $params = [];
        foreach ($cfg['pk'] as $pk) {
            if (!array_key_exists($pk, $keys)) {
                throw new InvalidArgumentException('Missing primary key: ' . $pk);
            }
            $param = $prefix . $pk;
            $parts[] = $cfg['table'] . '.' . $pk . ' = :' . $param;
            $params[$param] = $keys[$pk];
        }
        return ['sql' => implode(' AND ', $parts), 'params' => $params];
    }
}
