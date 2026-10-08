<?php
/**
 * Partial: Modal Popup Peringatan
 * Panggil dengan: $modal = ['type' => 'danger', 'title' => '...', 'text' => '...'];
 */

if (!empty($modal)):
    $type = $modal['type'] ?? 'danger';
    $iconMap = [
        'danger'  => 'alert-octagon',
        'warning' => 'alert-triangle',
        'success' => 'check-circle-2',
    ];
    $icon = $iconMap[$type] ?? 'alert-circle';
?>
<div class="modal-overlay" id="appModal" onclick="if(event.target===this)this.remove()">
  <div class="modal">
    <div class="modal-icon <?= $type ?>">
      <i data-lucide="<?= $icon ?>"></i>
    </div>
    <h3 class="modal-title"><?= htmlspecialchars($modal['title']) ?></h3>
    <p class="modal-text"><?= $modal['text'] ?></p>
    <button type="button" class="btn btn-primary btn-full"
            onclick="document.getElementById('appModal').remove()">
      <i data-lucide="check"></i> Mengerti
    </button>
  </div>
</div>
<?php endif; ?>