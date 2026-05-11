<section class="panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>
    <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="">
        <?php foreach (($data['keys'] ?? []) as $pk => $value): ?><input type="hidden" name="<?= h($pk) ?>" value="<?= h($value) ?>"><?php endforeach; ?>
        <div class="form-grid">
            <?php foreach ($data['cfg']['fields'] as $field => $meta): ?>
                <?php
                $type = $meta['type'] ?? 'text';
                $value = $data['row'][$field] ?? '';
                $isPk = in_array($field, $data['cfg']['pk'], true);
                $disabled = (($data['action'] === 'Edit' && $isPk) || ($meta['readonly'] ?? false));
                ?>
                <div class="form-field">
                    <label for="<?= h($field) ?>"><?= h($meta['label'] ?? $field) ?></label>
                    <?php if ($type === 'textarea'): ?>
                        <textarea class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" <?= !empty($meta['required']) ? 'required' : '' ?>><?= h($value) ?></textarea>
                    <?php elseif ($type === 'select'): ?>
                        <select class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" <?= !empty($meta['required']) ? 'required' : '' ?> <?= $disabled ? 'disabled' : '' ?>>
                            <option value="">-- Chọn --</option>
                            <?php foreach (($data['relations'][$field] ?? []) as $option): ?>
                                <option value="<?= h($option['value']) ?>" <?= (string)$value === (string)$option['value'] ? 'selected' : '' ?>><?= h($option['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($disabled): ?><input type="hidden" name="<?= h($field) ?>" value="<?= h($value) ?>"><?php endif; ?>
                    <?php elseif ($type === 'select_static'): ?>
                        <select class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" <?= !empty($meta['required']) ? 'required' : '' ?>>
                            <?php foreach (($meta['options'] ?? []) as $optionValue => $optionLabel): ?>
                                <option value="<?= h($optionValue) ?>" <?= (string)$value === (string)$optionValue ? 'selected' : '' ?>><?= h($optionLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($type === 'datetime'): ?>
                        <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" type="datetime-local" value="<?= h(format_datetime_for_input($value)) ?>" <?= !empty($meta['required']) ? 'required' : '' ?>>
                    <?php elseif ($type === 'date'): ?>
                        <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" type="date" value="<?= h(format_date_for_input($value)) ?>" <?= !empty($meta['required']) ? 'required' : '' ?>>
                    <?php elseif ($type === 'image'): ?>
                        <?php if ($value): ?><div><img class="thumb" src="<?= asset_url('Image/' . $value) ?>" alt="<?= h($value) ?>"></div><?php endif; ?>
                        <input type="hidden" name="<?= h($field) ?>" value="<?= h($value) ?>">
                        <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>_upload" type="file" accept="image/*">
                    <?php else: ?>
                        <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" type="<?= h($type) ?>" value="<?= h($value) ?>" <?= !empty($meta['required']) ? 'required' : '' ?> <?= $disabled ? 'readonly' : '' ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="toolbar" style="margin-top:18px;">
            <button class="btn-main" type="submit">LƯU</button>
            <a class="btn-back" href="<?= url_for($data['controller'], $data['listAction']) ?>">QUAY VỀ</a>
        </div>
    </form>
</section>
