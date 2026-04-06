<div class="table-responsive">
  <table class="table table-bordered table-sm align-middle text-center mb-0">
    <thead class="table-light">
      <tr>
        <th class="sticky-column" style="width: 150px;">🔑 Role</th>
        <?php foreach ($menus as $menu): ?>
          <th class="text-nowrap"><?= $menu['nama'] ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($roles as $role): ?>
        <?php if (strtolower($role['nama_role']) === 'superadmin') continue; ?>
        <tr>
          <td class="sticky-column">
            <strong class="text-primary"><?= ucfirst($role['nama_role']) ?></strong>
            <input type="hidden" name="akses[<?= $role['id'] ?>][]" value="">
          </td>
          <?php foreach ($menus as $menu): ?>
            <td>
              <div class="form-check form-switch d-flex justify-content-center">
                <input class="form-check-input"
                       type="checkbox"
                       name="akses[<?= $role['id'] ?>][]"
                       value="<?= $menu['id'] ?>"
                       <?= in_array($menu['id'], $akses[$role['id']] ?? []) ? 'checked' : '' ?>>
              </div>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
