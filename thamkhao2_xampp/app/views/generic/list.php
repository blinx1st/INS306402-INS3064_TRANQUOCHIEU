<section class="panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>

    <?php if (!empty($data['search'])): ?>
        <form class="search-form" method="get" action="<?= url_for($data['controller'], $data['listAction']) ?>">
            <?php if ($data['search'] === 'members'): ?>
                <input class="form-control" type="text" name="mathanhvien" placeholder="Tìm kiếm mã thành viên..." value="<?= h($data['searchValues']['mathanhvien'] ?? '') ?>">
                <input class="form-control" type="text" name="hoten" placeholder="Tìm kiếm thành viên..." value="<?= h($data['searchValues']['hoten'] ?? '') ?>">
            <?php else: ?>
                <input class="form-control" type="text" name="maSuKien" placeholder="Tìm kiếm mã sự kiện..." value="<?= h($data['searchValues']['maSuKien'] ?? '') ?>">
                <input class="form-control" type="text" name="tenSuKien" placeholder="Tìm kiếm tên sự kiện..." value="<?= h($data['searchValues']['tenSuKien'] ?? '') ?>">
            <?php endif; ?>
            <button class="btn-main" type="submit">TÌM KIẾM</button>
        </form>
    <?php endif; ?>

    <div class="toolbar">
        <a class="btn-main" href="<?= url_for($data['controller'], 'Create') ?>">THÊM MỚI</a>
        <?php if (str_contains($data['controller'], 'ThanhVien_Admin')): ?>
            <a class="btn-back" href="<?= url_for('NhomHocTap_Admin_64131060', 'NhomHocTap_Admin_64131060') ?>">NHÓM HỌC TẬP</a>
        <?php elseif (str_contains($data['controller'], 'ThanhVien_Assitant')): ?>
            <a class="btn-back" href="<?= url_for('NhomHocTap_Assitant_64131060', 'NhomHocTap_Assitant_64131060') ?>">NHÓM HỌC TẬP</a>
        <?php elseif (str_contains($data['controller'], 'SuKien_Admin')): ?>
            <a class="btn-back" href="<?= url_for('ThanhVienSuKien_Admin_64131060', 'ThanhVienSuKien_Admin_64131060') ?>">THÀNH VIÊN THAM GIA</a>
        <?php elseif (str_contains($data['controller'], 'SuKien_Assitant')): ?>
            <a class="btn-back" href="<?= url_for('ThanhVienSuKien_Assitant_64131060', 'ThanhVienSuKien_Assitant_64131060') ?>">THÀNH VIÊN THAM GIA</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($data['emptyMessage'])): ?><div class="alert alert-warning"><?= h($data['emptyMessage']) ?></div><?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead><tr>
                <?php foreach ($data['cfg']['list'] as $label): ?><th><?= h($label) ?></th><?php endforeach; ?>
                <th>Thao tác</th>
            </tr></thead>
            <tbody>
            <?php foreach ($data['rows'] as $row): ?>
                <tr>
                    <?php foreach ($data['cfg']['list'] as $field => $label): ?>
                        <td>
                            <?php if ($field === 'Anh' && !empty($row[$field])): ?>
                                <img class="thumb" src="<?= asset_url('Image/' . $row[$field]) ?>" alt="<?= h($row[$field]) ?>">
                            <?php else: ?>
                                <?= nl2br(h($row[$field] ?? '')) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td style="white-space:nowrap;">
                        <?php $params = []; foreach ($data['cfg']['pk'] as $pk) { $params[$pk] = $row[$pk]; } ?>
                        <a class="btn btn-sm btn-info text-white" href="<?= url_for($data['controller'], 'Details', $params) ?>">Chi tiết</a>
                        <a class="btn btn-sm btn-warning" href="<?= url_for($data['controller'], 'Edit', $params) ?>">Sửa</a>
                        <a class="btn btn-sm btn-danger" href="<?= url_for($data['controller'], 'Delete', $params) ?>">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
