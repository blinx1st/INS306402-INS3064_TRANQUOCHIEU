<section class="page-head">
    <div>
        <p class="eyebrow">MVC + PDO + MySQL</p>
        <h1>Danh sach sinh vien</h1>
    </div>
    <a class="button" href="<?= e(url('students/create')) ?>">Them sinh vien</a>
</section>

<?php if ($message = flash('success')): ?>
    <div class="alert alert--success"><?= e($message) ?></div>
<?php endif; ?>

<section class="panel">
    <?php if (empty($students)): ?>
        <p class="empty">Chua co sinh vien nao. Hay them du lieu dau tien.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ho ten</th>
                        <th>Email</th>
                        <th>Dien thoai</th>
                        <th>Chuyen nganh</th>
                        <th>Ngay tao</th>
                        <th class="actions">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= e($student['id']) ?></td>
                            <td><?= e($student['name']) ?></td>
                            <td><?= e($student['email']) ?></td>
                            <td><?= e($student['phone'] ?: '-') ?></td>
                            <td><?= e($student['major']) ?></td>
                            <td><?= e($student['created_at']) ?></td>
                            <td class="actions">
                                <a class="link" href="<?= e(url('students/edit/' . $student['id'])) ?>">Sua</a>
                                <form action="<?= e(url('students/delete/' . $student['id'])) ?>" method="post" class="inline-form" onsubmit="return confirm('Xoa sinh vien nay?')">
                                    <button class="link link--danger" type="submit">Xoa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

