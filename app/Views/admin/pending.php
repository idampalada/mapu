<?= $this->extend('admin/layouts/app') ?>
<?= $this->section('content') ?>

<h3 class="mb-4">Daftar Pengguna Menunggu Aktivasi</h3>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
<?php endif; ?>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Email</th>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= esc($user['email']) ?></td>
            <td><?= esc($user['username']) ?></td>
            <td><?= esc($user['status'] ?? '-') ?></td>
            <td>
                <form method="post" action="<?= base_url('admin/users/activate') ?>">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <button class="btn btn-success btn-sm">Aktifkan</button>
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>
