<?php
$status = "status_verifikasi_$verifikator";
$pesan = "pesan_verifikasi_$verifikator";
$previous_verifikator_status = $verifikator > 1 ? "status_verifikasi_" . ($verifikator - 1) : null;
$can_verify = true;

if ($previous_verifikator_status && $per->$previous_verifikator_status != 4) {
    $can_verify = false;
}
?>
    <?php if ($can_verify): ?>
    <?php if ($per->$status == 1): ?>
        <?php if ($this->session->userdata('id_role') == 1): ?>
            <a href="<?= base_url("admin/tunjangan/request_verifikasi/{$per->tanggal}/$verifikator") ?>" class="btn btn-primary" >Request Verification</button>
        <?php endif; ?>
    <?php elseif ($per->$status == 2): ?>
        <?php if ($this->session->userdata('jabatan_id') == (5 + $verifikator)): ?>
            <button class="btn btn-danger" data-toggle="modal" data-target="#modal-reject-v<?= $verifikator ?>-<?= $per->id ?>">Reject</button>
            <?php if ($verifikator == 5): ?>
                <a href="#" data-toggle="modal" data-target="#modal-approve-ttd-v<?= $verifikator ?>-<?= $per->id ?>" class="btn btn-success" >Approve</a>
            <?php else: ?>
                <a href="<?= base_url("admin/tunjangan/approve_verifikasi/{$per->tanggal}/$verifikator") ?>" class="btn btn-success" >Approve</a>
            <?php endif; ?>

        <?php endif; ?>
        <?php if ($this->session->userdata('id_role') == 1): ?>
            <button class="btn btn-warning">Waiting Verification</button>
        <?php endif; ?>
    <?php elseif ($per->$status == 3): ?>
        <?php if ($this->session->userdata('id_role') == 1): ?>
            <a href="<?= base_url("admin/tunjangan/request_verifikasi/{$per->tanggal}/$verifikator") ?>" class="btn btn-primary">Request Verification Again</a>
        <?php endif; ?>
        <button class="btn btn-danger" data-toggle="modal" data-target="#modal-v<?= $verifikator ?>-<?= $per->id ?>">
        
        <i class="fa fa-book"></i>
            Rejected
        </button>
    <?php elseif ($per->$status == 4): ?>
        <button class="btn btn-success">
            <i class="fa fa-check"></i>
            Approved 
        </button>
    <?php endif; ?>

    <!-- Modal for reject message -->
    <div class="modal fade" id="modal-reject-v<?= $verifikator ?>-<?= $per->id ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?= base_url("admin/tunjangan/reject_verifikasi/{$per->tanggal}/$verifikator") ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Verification <?= $verifikator ?></h5>
                    </div>
                    <div class="modal-body">
                        <textarea name="pesan" class="form-control" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for approve with ttd -->
    <div class="modal fade" id="modal-approve-ttd-v<?= $verifikator ?>-<?= $per->id ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?= base_url("admin/tunjangan/verify_with_signature/{$per->tanggal}/$verifikator") ?>" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload TTD Verification <?= $verifikator ?></h5>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="signature_file" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal to show reject message -->
    <div class="modal fade" id="modal-v<?= $verifikator ?>-<?= $per->id ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Message</h5>
                </div>
                <div class="modal-body">
                    <p><?= $per->$pesan ?></p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
